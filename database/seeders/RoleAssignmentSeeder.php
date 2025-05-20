<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class RoleAssignmentSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(1);
        if ($user && ! $user->hasRole('superadmin')) {
            $user->assignRole('superadmin');
        }
    }
}