<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application’s database.
     */
    public function run(): void
    {
        // Call each of your other seeders here:
        $this->call([
            UserSeeder::class,
            //BankSystemSeeder::class,    // if you have one for bank data
            //BankAccountSeeder::class,
            //BankAccountLargeSeeder::class,
            StudentBankAccountSeeder::class,
            // …any others
        ]);
    }
}