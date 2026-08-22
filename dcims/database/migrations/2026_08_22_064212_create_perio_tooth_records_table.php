<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perio_tooth_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perio_examination_id')->constrained()->restrictOnDelete();
            $table->foreignId('tooth_id')->constrained('teeth')->restrictOnDelete();
            $table->unsignedTinyInteger('mobility')->nullable();
            $table->unsignedTinyInteger('furcation')->nullable();
            $table->text('notes')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['perio_examination_id', 'tooth_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perio_tooth_records');
    }
};
