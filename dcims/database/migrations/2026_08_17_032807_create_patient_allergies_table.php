<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('allergen');
            $table->string('reaction')->nullable();
            $table->string('severity')->default('mild');
            $table->date('onset_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_allergies');
    }
};
