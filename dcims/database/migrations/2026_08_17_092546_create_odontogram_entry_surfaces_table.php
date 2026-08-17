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
        Schema::create('odontogram_entry_surfaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontogram_entry_id')->constrained()->cascadeOnDelete();
            $table->string('surface');
            $table->unique(['odontogram_entry_id', 'surface']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontogram_entry_surfaces');
    }
};
