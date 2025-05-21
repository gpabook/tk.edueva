<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Import the Permission model

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load permissions count for each role (optional, but can be useful)
        $roles = Role::withCount('permissions')->orderBy('id')->paginate(10);
        return Inertia::render('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all available permissions to assign in the form
        $permissions = Permission::orderBy('name')->get();
        return Inertia::render('Roles/Create', [
            'permissions' => $permissions, // Pass all available permissions to the view
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array', // Expect an array of permission IDs or names
            'permissions.*' => 'exists:permissions,id', // Validate that each permission ID exists
        ]);

        $role = Role::create(['name' => $request->name]);

        // If permissions were sent, sync them with the new role
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
                         ->with('success', 'Role created successfully with specified permissions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        // Eager load the permissions associated with this specific role
        $role->load('permissions');
        return Inertia::render('Roles/Show', [
            'role' => $role, // Role object will now include its permissions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Eager load the current permissions for this role
        $role->load('permissions');
        // Fetch all available permissions to allow changing assignments
        $allPermissions = Permission::orderBy('name')->get();

        return Inertia::render('Roles/Edit', [
            'role' => $role, // Role with its currently assigned permissions
            'allPermissions' => $allPermissions, // All available permissions for the form
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array', // Expect an array of permission IDs or names
            'permissions.*' => 'exists:permissions,id', // Validate that each permission ID exists (if sending IDs)
                                                       // If sending names: 'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $request->name]);

        // Sync the permissions. This will add new ones, remove deselected ones.
        // If 'permissions' key is not in the request, it means all permissions should be removed.
        // If you want to only add and never remove unless explicitly told, use givePermissionTo or revokePermissionTo.
        $role->syncPermissions($request->input('permissions', []));


        return redirect()->route('roles.index')
                         ->with('success', 'Role updated successfully with specified permissions.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Deleting a role will also detach its permissions automatically
        // due to cascading deletes or pivot table behavior defined by the Spatie package.
        $role->delete();

        return redirect()->route('roles.index')
                         ->with('success', 'Role deleted successfully.');
    }
}
