<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BankAccount;

class StudentBankAccountSeeder extends Seeder
{
public function run(): void
{
    $students = User::factory()
        ->count(100)
        ->student()
        ->create();

    $bar = $this->command->getOutput()->createProgressBar($students->count());
    $bar->start();

    $students->each(function($student) use ($bar) {
        BankAccount::factory()
            ->count(rand(1, 3))
            ->create(['user_id' => $student->id]);
        $bar->advance();
    });

    $bar->finish();
    $this->command->info("\n✅ Done creating bank accounts");
}

}