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
        Schema::create('treatment_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_plan_id')->constrained()->restrictOnDelete();
            $table->foreignId('procedure_id')->constrained()->restrictOnDelete();
            $table->foreignId('tooth_id')->nullable()->constrained('teeth')->restrictOnDelete();
            $table->string('status')->default('proposed');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('estimated_unit_price', 12, 2);
            $table->decimal('estimated_total', 12, 2);
            $table->string('priority')->nullable();
            $table->text('notes')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_plan_items');
    }
};
