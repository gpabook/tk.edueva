<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // ensure permissions exist
        collect([
            'read roles',
            'create roles',
            'update roles',
            'delete roles',
            'read banks',
            'view banks',
            'update avatar',
            'read permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
        ])->each(fn($perm) => Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web'])); // <--- GOOD: Added guard_name

        // Create roles first if they might not exist (safer)
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // assign to admin
        $adminRole = Role::findByName('admin', 'web'); // <--- Specify guard_name
        if ($adminRole) { // <--- GOOD: Check if role exists
            $adminRole->givePermissionTo([
                'read roles',
                'create roles',
                'update roles',
                'update avatar',
                'read banks',
                'view banks',
            ]);
        } else {
            $this->command->warn("Role 'admin' not found. Skipping permission assignment.");
        }


        // assign to teacher
        // $admin = Role::findByName('teacher'); // <--- Typo: variable name should be $teacherRole
        $teacherRole = Role::findByName('teacher', 'web'); // <--- Corrected variable name and added guard_name
        if ($teacherRole) { // <--- GOOD: Check if role exists
            $teacherRole->givePermissionTo([
                'read banks',
                'view banks',
                'update avatar',
            ]);
        } else {
            $this->command->warn("Role 'teacher' not found. Skipping permission assignment.");
        }

        // assign to student
        // $admin = Role::findByName('student'); // <--- Typo: variable name should be $studentRole
        $studentRole = Role::findByName('student', 'web'); // <--- Corrected variable name and added guard_name
        if ($studentRole) { // <--- GOOD: Check if role exists
            $studentRole->givePermissionTo([
                'view banks',
                'update avatar',
            ]);
        } else {
            $this->command->warn("Role 'student' not found. Skipping permission assignment.");
        }


        // assign full access to superadmin
        $superAdminRole = Role::findByName('superadmin', 'web'); // <--- Specify guard_name
        if ($superAdminRole) { // <--- GOOD: Check if role exists
            $superAdminRole->givePermissionTo(Permission::where('guard_name', 'web')->get()); // <--- Assign all permissions for the 'web' guard
        } else {
            $this->command->warn("Role 'superadmin' not found. Skipping permission assignment.");
        }


        // clear cache so changes take effect immediately
        // This should ideally be at the beginning or handled globally by the seeder call
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }
}
