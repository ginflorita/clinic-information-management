<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->string('encounter_number')->unique();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->constrained()->restrictOnDelete();
            $table->string('encounter_type')->default('visit');
            $table->string('status')->default('in_progress');
            $table->timestampTz('started_at');
            $table->timestampTz('ended_at')->nullable();
            $table->text('chief_complaint')->nullable();
            $table->text('clinical_summary')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
