<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('lab_id')->constrained()->restrictOnDelete();
            $table->foreignId('procedure_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('tooth_id')->nullable()->constrained('teeth')->restrictOnDelete();
            $table->date('sent_date')->nullable();
            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestampsTz();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_orders');
    }
};
