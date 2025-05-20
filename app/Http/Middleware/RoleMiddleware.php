<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! Auth::check()) {
            abort(403);
        }

        // Convert passed-in role names to their enum values:
        $allowed = array_map(fn($r) => UserRole::fromName($r)->value, $roles);

        if (! in_array(Auth::user()->role->value, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}