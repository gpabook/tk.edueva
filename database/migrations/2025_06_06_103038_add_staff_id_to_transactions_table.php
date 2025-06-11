<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // เพิ่ม foreign key ไปยัง users.id
            $table->foreignId('staff_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('description'); // ใส่หลัง description
        });
    }

     /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });
    }

};
