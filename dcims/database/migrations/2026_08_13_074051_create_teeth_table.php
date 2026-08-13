<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teeth', function (Blueprint $table) {
            $table->id();
            $table->string('notation_system')->default('FDI');
            $table->string('tooth_code');
            $table->string('tooth_name');
            $table->string('dentition_type');
            $table->string('arch');
            $table->unsignedInteger('position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unique(['notation_system', 'tooth_code', 'dentition_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teeth');
    }
};
