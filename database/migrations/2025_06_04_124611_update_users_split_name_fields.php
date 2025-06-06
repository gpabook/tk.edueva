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

        $table->string('name_th')->nullable();
        $table->string('surname_th')->nullable();

        $table->string('name_en')->nullable();
        $table->string('surname_en')->nullable();

        $table->date('birthday')->nullable();
        $table->enum('gender', ['male', 'female', 'other'])->nullable();

        $table->string('phone')->nullable();
        $table->string('line_id')->nullable();
        $table->string('address')->nullable();


    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['name_th', 'surname_th', 'name_en', 'surname_en', 'birthday', 'gender', 'phone', 'line_id', 'address']);
        });
    }
};
