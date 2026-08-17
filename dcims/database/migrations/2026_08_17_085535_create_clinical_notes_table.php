<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained()->restrictOnDelete();
            $table->string('note_type')->default('progress');
            $table->text('note_text');
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('signed_at')->nullable();
            // Corrections to a signed note create a new row here rather than
            // editing the original — this FK links the amendment back to the
            // note it supersedes, per §13/§83's immutability rule.
            $table->foreignId('amends_note_id')->nullable()->constrained('clinical_notes')->nullOnDelete();
            $table->text('amendment_reason')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_notes');
    }
};
