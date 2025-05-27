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
        Schema::create('class_levels', function (Blueprint $table) {
            $table->id();
            $table->string('code',   5)->unique(); // e.g. 'K1', 'G10'
            $table->string('name_en',  50);           // e.g. 'Kindergarten 1', 'Grade 10'
            $table->string('name_th',  50);           // e.g. 'Kindergarten 1', 'Grade 10'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_levels');
    }
};