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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            // FK ไปยัง class_levels.id
            $table->foreignId('class_level_id')
            ->constrained('class_levels')
            ->onDelete('cascade');
            $table->string('name_room_en',  20);  // e.g. 'A','B','1','2'
            $table->string('name_room_th',  20);  // e.g. 'A','B','1','2'
            // ให้แต่ละ (class_level_id,name) ไม่ซ้ำกัน
            $table->unique(['class_level_id','name_room_en']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};