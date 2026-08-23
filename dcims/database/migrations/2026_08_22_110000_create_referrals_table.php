<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_number')->unique();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('referring_provider_id')->constrained('providers')->restrictOnDelete();
            $table->string('receiving_name');
            $table->string('receiving_specialty')->nullable();
            $table->string('receiving_contact')->nullable();
            $table->text('reason');
            $table->text('clinical_summary')->nullable();
            $table->date('referral_date');
            $table->string('status')->default('draft');
            $table->text('response')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestampsTz();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
