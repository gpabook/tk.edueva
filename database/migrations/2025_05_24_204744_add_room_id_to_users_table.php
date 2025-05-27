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
        Schema::table('users', function (Blueprint $table) {
            // เพิ่มคอลัมน์ room_id ชี้ไปยังตาราง rooms
            $table->foreignId('room_id')
                  ->nullable()
                  ->constrained('rooms')
                  ->onDelete('set null')
                  ->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ลบ foreign key แล้วลบคอลัมน์
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
};
