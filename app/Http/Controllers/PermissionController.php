<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission; // Import the Permission model
use Illuminate\Validation\Rule; // For unique validation rule

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::orderBy('id')->paginate(20);
        return Inertia::render('Permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Permissions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name'), // Ensure permission name is unique
            ],
            // 'guard_name' is usually 'web' by default and handled by the model if not specified.
            // You can add validation for it if you allow users to specify it:
            // 'guard_name' => 'sometimes|string|max:255',
        ]);

        Permission::create([
            'name' => $request->name,
            // 'guard_name' => $request->guard_name ?? 'web', // Default to 'web' if not provided
        ]);

        return redirect()->route( (request()->has('is_admin_route') ? 'admin.permissions.index' : 'permissions.index') ) // Adjust if you use admin prefix
                         ->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     * We pass the permission model directly to the view.
     */
    public function edit(Permission $permission) // Route model binding
    {
        return Inertia::render('Permissions/Edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission) // Route model binding
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id), // Ignore current permission ID
            ],
            // 'guard_name' => 'sometimes|string|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            // 'guard_name' => $request->guard_name ?? $permission->guard_name, // Keep existing or update
        ]);

        return redirect()->route( (request()->has('is_admin_route') ? 'admin.permissions.index' : 'permissions.index') ) // Adjust if you use admin prefix
                         ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission) // Route model binding
    {
        // Before deleting a permission, you might want to check if it's assigned to any roles
        // and decide on a strategy (e.g., prevent deletion, or remove from roles first).
        // For simplicity, we'll just delete it here.
        // $permission->roles()->detach(); // If you want to manually remove from all roles first

        $permission->delete();

        return redirect()->route( (request()->has('is_admin_route') ? 'admin.permissions.index' : 'permissions.index') ) // Adjust if you use admin prefix
                         ->with('success', 'Permission deleted successfully.');
    }
}
