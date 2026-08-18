<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('encounter_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('diagnosis_id')->constrained()->restrictOnDelete();
            $table->foreignId('tooth_id')->nullable()->constrained('teeth')->restrictOnDelete();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('diagnosed_by')->constrained('users')->restrictOnDelete();
            $table->timestampTz('diagnosed_at');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounter_diagnoses');
    }
};
