<?php

// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Enums\UserRole;

class DashboardController extends Controller
{
        public function index()
    {
        $user = Auth::user();

        return Inertia::render('Dashboard', [
            // send exactly the bits your Vue page needs
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'role'       => $user->role->name,    // "SuperAdmin", "Admin", etc.
                'role_label' => $user->role->label(), // "Super Admin", "Admin", etc.
            ],
        ]);
    }
}