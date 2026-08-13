<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('relationship_type');
            $table->boolean('is_guardian')->default(false);
            $table->boolean('is_emergency_contact')->default(false);
            $table->timestampsTz();
        });

        // Exactly one of related_patient_id (an existing patient) or contact_name
        // (a non-patient guardian/contact) must be set — never both, never neither.
        // Postgres-only: SQLite (used by the test suite) can't ALTER TABLE to add
        // a CHECK constraint after creation, so this is enforced there at the
        // application layer instead (see PatientRelationshipController).
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
                ALTER TABLE patient_relationships
                ADD CONSTRAINT patient_relationships_exactly_one_contact CHECK (
                    (related_patient_id IS NOT NULL AND contact_name IS NULL)
                    OR (related_patient_id IS NULL AND contact_name IS NOT NULL)
                )
            SQL);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_relationships');
    }
};
