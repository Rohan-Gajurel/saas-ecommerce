<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy('group'); 

        return view('role.create', compact('roles', 'permissions'));
    }

     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id', // Validate permission IDs
        ]);

        $role = Role::create(['name' => $request->name]);

        // Assign permissions
        if ($request->has('permissions')) {
            $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
            $role->syncPermissions($validPermissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all()->groupBy('group');

        return view('role.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id', // Validate permission IDs
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);

        // Sync permissions
        if ($request->has('permissions')) {
            $validPermissions = Permission::whereIn('id', $request->permissions)
                ->where('guard_name', 'web') // Ensure permissions are for the 'web' guard
                ->pluck('id')
                ->toArray();
            $role->syncPermissions($validPermissions);
        } else {
            $role->syncPermissions([]); // Remove all permissions if none are selected
        }
        
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('update', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
