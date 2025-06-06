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
    Schema::create('bank_accounts', function (Blueprint $table) {
        $table->id();

        // student_id as string (must match users.student_id type)
        $table->string('student_id', 20);

        // Add the foreign key to users.student_id
        $table->foreign('student_id')
              ->references('student_id')
              ->on('users')
              ->onDelete('cascade');

        $table->decimal('balance', 15, 2)->default(0);
        $table->timestamps();
    });

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
