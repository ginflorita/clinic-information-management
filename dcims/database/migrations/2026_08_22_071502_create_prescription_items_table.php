<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained()->restrictOnDelete();
            $table->string('dose');
            $table->string('frequency');
            $table->string('route')->nullable();
            $table->string('duration')->nullable();
            $table->unsignedInteger('quantity');
            $table->text('instructions')->nullable();
            $table->unsignedInteger('refills')->default(0);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
