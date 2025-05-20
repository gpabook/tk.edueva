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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        // foreign key to bank_accounts.id
        $table->foreignId('account_id')
              ->constrained('bank_accounts')
              ->cascadeOnDelete();
        // deposit or withdraw
        $table->enum('type', ['deposit','withdraw'])->index();
        // amount and optional description
        $table->decimal('amount', 15, 2);
        $table->string('description')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};