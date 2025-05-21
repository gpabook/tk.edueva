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
        ])->each(fn($perm) => Permission::firstOrCreate(['name' => $perm]));

        // assign to admin
        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'read roles',
            'create roles',
            'update roles',
        ]);

        // assign full access to superadmin
        $super = Role::findByName('superadmin');
        $super->givePermissionTo(Permission::all());

        // clear cache so changes take effect immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }
}