<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perio_site_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perio_tooth_record_id')->constrained()->cascadeOnDelete();
            $table->string('site');
            $table->decimal('probing_depth', 4, 1);
            $table->decimal('gingival_recession', 4, 1)->nullable();
            $table->decimal('clinical_attachment_level', 4, 1)->nullable();
            $table->decimal('gingival_margin', 4, 1)->nullable();
            $table->boolean('bleeding_on_probing')->default(false);
            $table->boolean('plaque_present')->default(false);

            $table->unique(['perio_tooth_record_id', 'site']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perio_site_measurements');
    }
};
