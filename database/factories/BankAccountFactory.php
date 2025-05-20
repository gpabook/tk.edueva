<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BankAccount;
use App\Models\User;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition(): array
    {
        return [
            // pick a random existing user
            'user_id' => User::inRandomOrder()->first()->id,
            // give a random starting balance
            'balance' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}