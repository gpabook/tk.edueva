<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankAccount;

class BankAccountLargeSeeder extends Seeder
{
    public function run(): void
    {
        // generate 2,000 bank accounts
        BankAccount::factory()
            ->count(2000)
            ->create();
    }
}