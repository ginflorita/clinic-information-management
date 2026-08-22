<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('consent_type_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('granted');
            $table->timestampTz('granted_at');
            $table->timestampTz('revoked_at')->nullable();
            $table->foreignId('obtained_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestampsTz();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};
