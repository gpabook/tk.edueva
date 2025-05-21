<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // User::factory()->superAdmin()->create();
        // User::factory()->admin()->create();
        // User::factory()->teacher()->create();
        // User::factory()->student()->create();

        /*
        $user = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('secret'),
            ]
        );

        // Give them the superadmin role
        $user->assignRole('superadmin');
        */
    }
}
