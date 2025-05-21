<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        foreach ([
            'read roles',
            'create roles',
            'update roles',
            'delete roles',
        ] as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

    }
}