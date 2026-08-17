<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained()->restrictOnDelete();
            $table->foreignId('appointment_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('chair_id')->nullable()->constrained()->nullOnDelete();
            $table->timestampTz('scheduled_start');
            $table->timestampTz('scheduled_end');
            $table->string('status')->default('scheduled');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestampsTz();
            $table->timestampTz('cancelled_at')->nullable();
        });

        // Double-booking prevention at the DB layer — Postgres-only (SQLite has
        // neither the gist index type nor the btree_gist extension). See
        // AppointmentController for the matching application-level check that
        // keeps this enforced under the SQLite test suite too.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');

            DB::statement(<<<'SQL'
                ALTER TABLE appointments
                ADD CONSTRAINT no_provider_double_booking
                EXCLUDE USING gist (
                    provider_id WITH =,
                    tstzrange(scheduled_start, scheduled_end) WITH &&
                ) WHERE (status NOT IN ('cancelled', 'no_show'))
            SQL);

            DB::statement(<<<'SQL'
                ALTER TABLE appointments
                ADD CONSTRAINT no_chair_double_booking
                EXCLUDE USING gist (
                    chair_id WITH =,
                    tstzrange(scheduled_start, scheduled_end) WITH &&
                ) WHERE (status NOT IN ('cancelled', 'no_show') AND chair_id IS NOT NULL)
            SQL);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
