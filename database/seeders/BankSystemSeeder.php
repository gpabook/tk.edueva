<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\Transaction;

class BankSystemSeeder extends Seeder
{
    public function run()
    {
        // … your existing code …

        BankAccount::all()->each(function($account) {
            Transaction::factory()
                       ->count(5)
                       ->state(['account_id' => $account->id])
                       ->create();

            // now Intelephense will recognize DB
            $balance = $account->transactions()
                ->sum(DB::raw("CASE WHEN type='deposit' THEN amount ELSE -amount END"));

            $account->update(['balance' => $balance]);
        });
    }
}