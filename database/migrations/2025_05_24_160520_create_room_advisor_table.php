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
        Schema::create('room_advisor', function (Blueprint $table) {
        $table->id();
        // FK ไป rooms.id
        $table->foreignId('room_id')
              ->constrained('rooms')
              ->onDelete('cascade');

        // FK ไป users.id (เฉพาะครู)
        $table->foreignId('user_id')
              ->constrained('users')
              ->onDelete('cascade');

        // ถ้าต้องการว่าห้ามมีคู่ซ้ำ (room-user ซ้ำกัน) ให้ใช้ unique composite key
        $table->unique(['room_id','user_id']);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_advisor');
    }
};
