<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BankAccount;


class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
///////////////////////////////////
        // สร้างบัญชีธนาคาร 200 เรคคอร์ด
//        BankAccount::factory()->count(200)->create();
///////////////////////////////////
        User::all()->each(function ($user) {
            BankAccount::updateOrCreate(
                ['student_id' => $user->student_id],       // if exists, skip; otherwise create
                ['balance' => 0]                // initial balance
            );
        });
    }
}
