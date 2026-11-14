<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    //
    public function index(){
        $permissions=Permission::all();
        return view('permission.index',compact('permissions'));
    }
    public function create(){
        return view('permission.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'group' => 'required|string',
        ]);

        Permission::create(['name' => $request->name, 'group' => $request->group]);

        // clear Spatie permission cache so new permission is available immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');

    }

    public function edit($id){
        $permission=Permission::findOrFail($id);
        return view('permission.edit', compact('permission'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id,
            'group' => 'required|string',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'group' => $request->group,
        ]);

        // IMPORTANT: clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permissions.index')->with('update', 'Permission updated successfully.');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // detach from roles
        $permission->roles()->detach();

        $permission->delete();

        // clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('delete', 'Permission deleted successfully');
    }

}
