<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignRolesSeeder extends Seeder
{
    public function run()
    {
        User::where('email','superadmin@example.com')
            ->first()
            ?->assignRole('superadmin');

        User::where('email','admin@example.com')
            ->first()
            ?->assignRole('admin');

        User::where('email','teacher@example.com')
            ->first()
            ?->assignRole('teacher');

        User::where('email','student@example.com')
            ->first()
            ?->assignRole('student');
    }
}