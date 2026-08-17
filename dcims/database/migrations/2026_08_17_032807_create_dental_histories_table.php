<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dental_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('previous_dentist')->nullable();
            $table->text('previous_treatments')->nullable();
            $table->boolean('previous_extraction')->default(false);
            $table->boolean('previous_root_canal')->default(false);
            $table->text('prosthetic_history')->nullable();
            $table->text('orthodontic_history')->nullable();
            $table->text('previous_surgery')->nullable();
            $table->text('previous_complications')->nullable();
            $table->text('dental_habits')->nullable();
            $table->text('oral_hygiene')->nullable();
            $table->text('chief_concerns')->nullable();
            $table->timestampTz('recorded_at');
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dental_histories');
    }
};
