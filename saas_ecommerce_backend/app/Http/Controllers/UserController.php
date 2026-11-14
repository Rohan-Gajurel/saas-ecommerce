<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //
    public function index(){
        $users = User::all();
        $roles = Role::all();
         return view('user.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User role updated successfully.');
    }
}
