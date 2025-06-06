<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $roleCountsRaw = User::query()
        ->toBase()
        ->select('role')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('role')
        ->get();

    $roleCountsMap = $roleCountsRaw->pluck('count', 'role');

    $roleChart = collect(UserRole::cases())->map(function ($roleEnum) use ($roleCountsMap) {
        return [
            'label' => $roleEnum->label(),
            'count' => $roleCountsMap[$roleEnum->value] ?? 0,
        ];
    });

    return Inertia::render('Dashboard', [
        'roleChart' => $roleChart,
    ]);
    }
}
