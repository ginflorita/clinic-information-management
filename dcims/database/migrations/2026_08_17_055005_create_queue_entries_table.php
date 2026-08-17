<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('queue_date');
            $table->unsignedInteger('queue_number');
            $table->string('status')->default('waiting');
            $table->timestampTz('checked_in_at');
            $table->timestampTz('called_at')->nullable();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampsTz();

            $table->unique(['queue_date', 'queue_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_entries');
    }
};
