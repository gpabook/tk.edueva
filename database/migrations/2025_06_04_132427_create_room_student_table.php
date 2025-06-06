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
        Schema::create('room_student', function (Blueprint $table) {
            $table->id();
                    // FK ไปยัง users.id (เฉพาะนักเรียน)
            $table->foreignId('user_id')
        ->constrained('users')
        ->onDelete('cascade');

  // FK ไปยัง rooms.id
            $table->foreignId('room_id')
        ->constrained('rooms')
        ->onDelete('cascade');

            $table->date('assigned_at')->nullable();     // วันที่เริ่มอยู่ห้องนี้
            $table->date('left_at')->nullable();         // วันที่ออกจากห้องนี้ (null = ยังอยู่)

            $table->timestamps();

    // ป้องกันข้อมูลซ้ำกัน (อยู่ห้องเดียวกันซ้ำหลายแถว)
            $table->unique(['user_id', 'room_id', 'assigned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_student');
    }
};