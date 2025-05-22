<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {

        // Permissions for managing Permissions
        Permission::firstOrCreate(['name' => 'view permissions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create permissions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit permissions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete permissions', 'guard_name' => 'web']);

        // Assign these to your superadmin/admin roles
        $superAdminRole = Role::findByName('superadmin', 'web');
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo([
                'view permissions',
                'create permissions',
                'edit permissions',
                'delete permissions',
            ]);
        }

        $adminRole = Role::findByName('admin', 'web');
        if ($adminRole) {
            // Maybe admin can only view or has limited permission management
            $adminRole->givePermissionTo(['view permissions']);
        }

        $this->command->info('Permissions for Permission Management created and assigned.');

    }
}
