<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * invoices.balance / amount_paid are trigger-maintained caches
     * (architecture.md §76) — application code must never write them
     * directly. Postgres-only: SQLite has no trigger functions/PL/pgSQL,
     * so under the SQLite test suite these columns simply hold whatever
     * value the application set at row-insert time and won't move when
     * payments/adjustments change. The real guarantee lives here.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION recalculate_invoice_balance() RETURNS trigger AS $$
            BEGIN
                NEW.amount_paid := COALESCE((
                    SELECT SUM(amount) FROM payments
                    WHERE invoice_id = NEW.id AND status = 'completed'
                ), 0);

                NEW.balance := NEW.total_amount - NEW.amount_paid - COALESCE((
                    SELECT SUM(amount) FROM invoice_adjustments
                    WHERE invoice_id = NEW.id
                ), 0);

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER invoices_recalculate_balance
            BEFORE INSERT OR UPDATE ON invoices
            FOR EACH ROW EXECUTE FUNCTION recalculate_invoice_balance();

            CREATE OR REPLACE FUNCTION touch_invoice_balance() RETURNS trigger AS $$
            DECLARE
                affected_invoice_id BIGINT;
            BEGIN
                affected_invoice_id := COALESCE(NEW.invoice_id, OLD.invoice_id);

                IF affected_invoice_id IS NOT NULL THEN
                    UPDATE invoices SET updated_at = updated_at WHERE id = affected_invoice_id;
                END IF;

                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER payments_touch_invoice_balance
            AFTER INSERT OR UPDATE OR DELETE ON payments
            FOR EACH ROW EXECUTE FUNCTION touch_invoice_balance();

            CREATE TRIGGER invoice_adjustments_touch_invoice_balance
            AFTER INSERT OR UPDATE OR DELETE ON invoice_adjustments
            FOR EACH ROW EXECUTE FUNCTION touch_invoice_balance();
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            DROP TRIGGER IF EXISTS invoice_adjustments_touch_invoice_balance ON invoice_adjustments;
            DROP TRIGGER IF EXISTS payments_touch_invoice_balance ON payments;
            DROP FUNCTION IF EXISTS touch_invoice_balance();
            DROP TRIGGER IF EXISTS invoices_recalculate_balance ON invoices;
            DROP FUNCTION IF EXISTS recalculate_invoice_balance();
        SQL);
    }
};
