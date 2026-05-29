<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        $users = User::role($role->name)->paginate(10);
        return view('admin.roles.show', compact('role', 'users'));
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting Superadmin or roles with users
        if ($role->name === 'Superadmin') {
            return back()->with('error', 'Role Superadmin tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role tidak dapat dihapus karena masih memiliki pengguna.');
        }

        $role->delete();
        return redirect()->route('admin.roles')->with('success', 'Role berhasil dihapus.');
    }
}
