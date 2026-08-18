<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Now that a payment can be allocated across several invoices,
     * amount_paid must be summed from payment_allocations (joined to
     * completed payments) rather than payments.amount matched by
     * invoice_id directly — a single payment row no longer maps 1:1 to
     * one invoice. Postgres-only, same guard as the rest of this
     * migration family.
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
                    SELECT SUM(pa.amount_applied)
                    FROM payment_allocations pa
                    JOIN payments p ON p.id = pa.payment_id
                    WHERE pa.invoice_id = NEW.id AND p.status = 'completed'
                ), 0);

                NEW.balance := NEW.total_amount - NEW.amount_paid - COALESCE((
                    SELECT SUM(amount) FROM invoice_adjustments
                    WHERE invoice_id = NEW.id
                ), 0);

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS payments_touch_invoice_balance ON payments;

            CREATE OR REPLACE FUNCTION touch_invoices_for_payment() RETURNS trigger AS $$
            DECLARE
                affected_payment_id BIGINT;
                rec RECORD;
            BEGIN
                affected_payment_id := COALESCE(NEW.id, OLD.id);

                FOR rec IN SELECT DISTINCT invoice_id FROM payment_allocations WHERE payment_id = affected_payment_id
                LOOP
                    UPDATE invoices SET updated_at = updated_at WHERE id = rec.invoice_id;
                END LOOP;

                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER payments_touch_invoice_balance
            AFTER INSERT OR UPDATE OR DELETE ON payments
            FOR EACH ROW EXECUTE FUNCTION touch_invoices_for_payment();

            CREATE TRIGGER payment_allocations_touch_invoice_balance
            AFTER INSERT OR UPDATE OR DELETE ON payment_allocations
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
            DROP TRIGGER IF EXISTS payment_allocations_touch_invoice_balance ON payment_allocations;
            DROP TRIGGER IF EXISTS payments_touch_invoice_balance ON payments;
            DROP FUNCTION IF EXISTS touch_invoices_for_payment();

            CREATE TRIGGER payments_touch_invoice_balance
            AFTER INSERT OR UPDATE OR DELETE ON payments
            FOR EACH ROW EXECUTE FUNCTION touch_invoice_balance();

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
        SQL);
    }
};
