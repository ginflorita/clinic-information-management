<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('previous_surgeries')->nullable();
            $table->text('hospitalization')->nullable();
            $table->text('current_medications')->nullable();
            $table->string('pregnancy_status')->nullable();
            $table->string('smoking_status')->nullable();
            $table->string('alcohol_use')->nullable();
            $table->text('family_medical_history')->nullable();
            $table->string('physician_name')->nullable();
            $table->string('physician_contact')->nullable();
            $table->text('medical_alerts')->nullable();
            $table->timestampTz('recorded_at');
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};
