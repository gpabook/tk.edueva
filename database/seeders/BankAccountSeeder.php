<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BankAccount;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {
            BankAccount::updateOrCreate(
                ['user_id' => $user->id],       // if exists, skip; otherwise create
                ['balance' => 0]                // initial balance
            );
        });
    }
}