<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Define permissions (optional)
        $perms = ['manage users', 'view reports', 'edit articles'];
        foreach ($perms as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Define roles & give permissions
        Role::firstOrCreate(['name' => 'superadmin'])
            ->givePermissionTo(Permission::all());
        Role::firstOrCreate(['name' => 'admin'])
            ->givePermissionTo(['view reports', 'edit articles']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'student']);
    }
}