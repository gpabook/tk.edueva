<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('prefix_th')->nullable(); // เช่น นาย นาง นางสาว
            $table->string('name_th');
            $table->string('surname_th');
            $table->string('relationship')->nullable(); // เช่น บิดา มารดา ปู่ ย่า ฯลฯ

            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('phone_3')->nullable();

            $table->text('address')->nullable();
            $table->text('current_address')->nullable();

            $table->decimal('location_lat', 10, 7)->nullable(); // พิกัดละติจูด
            $table->decimal('location_lng', 10, 7)->nullable(); // พิกัดลองจิจูด

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
