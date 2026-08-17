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
        Schema::create('odontogram_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontogram_id')->constrained()->restrictOnDelete();
            $table->foreignId('tooth_id')->constrained('teeth')->restrictOnDelete();
            $table->foreignId('condition_id')->constrained('tooth_conditions')->restrictOnDelete();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontogram_entries');
    }
};
