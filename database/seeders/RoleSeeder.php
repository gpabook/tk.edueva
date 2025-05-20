<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        foreach (['superadmin','admin','teacher','student'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}