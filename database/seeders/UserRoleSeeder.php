<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // Create or update a SuperAdmin
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('secret'),
                'role'     => UserRole::SuperAdmin,
                'status'   => 1,
            ],
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('secret'),
                'role'     => UserRole::Admin,
                'status'   => 1,
            ],
        );

        // Teacher
        User::updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name'     => 'Teacher User',
                'password' => Hash::make('secret'),
                'role'     => UserRole::Teacher,
                'status'   => 1,
            ],
        );

        // Student
        User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name'     => 'Student User',
                'password' => Hash::make('secret'),
                'role'     => UserRole::Student,
                'status'   => 1,
            ],
        );
    }
}