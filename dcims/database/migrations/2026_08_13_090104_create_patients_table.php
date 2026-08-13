<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('preferred_name')->nullable();
            $table->date('date_of_birth');
            $table->string('sex');
            $table->string('civil_status')->nullable();
            $table->string('occupation')->nullable();
            $table->string('email')->nullable();
            $table->date('registration_date');
            $table->string('referral_source')->nullable();
            $table->string('status')->default('active');
            $table->timestampsTz();
            $table->timestampTz('archived_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
