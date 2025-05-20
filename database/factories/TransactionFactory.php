<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\BankAccount;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        $type    = $this->faker->randomElement(['deposit','withdraw']);
        $amount  = $this->faker->randomFloat(2, 10, 500);
        return [
            'account_id'  => BankAccount::inRandomOrder()->first()->id,
            'type'        => $type,
            'amount'      => $amount,
            'description' => ucfirst($type) . ' of ' . $amount,
        ];
    }
}