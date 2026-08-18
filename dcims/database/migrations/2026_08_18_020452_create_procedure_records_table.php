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
        Schema::create('procedure_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained()->restrictOnDelete();
            $table->foreignId('procedure_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained()->restrictOnDelete();
            $table->foreignId('tooth_id')->nullable()->constrained('teeth')->restrictOnDelete();
            $table->foreignId('treatment_plan_item_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('status')->default('completed');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->timestampTz('performed_at');
            $table->text('notes')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedure_records');
    }
};
