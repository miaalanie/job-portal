<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Even;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with(['roles', 'even']);
            
            if ($request->filled('role')) {
                $query->role($request->role);
            }

            $users = $query->get();
            $data = $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'encrypted_id' => encrypt($user->id),
                    'name' => $user->name,
                    'email' => $user->email,
                    'gambar' => $user->gambar,
                    'roles' => $user->roles->pluck('name'),
                    'even_name' => $user->even->namaperiode ?? '-',
                    'created_at' => $user->created_at->format('d M Y'),
                    'statusaktif' => $user->statusaktif,
                ];
            });
            return response()->json(['data' => $data]);
        }

        $roles = Role::all();
        return view('admin.users.index', compact('roles'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Admin Aplikasi', 'Admin Laporan', 'Admin Event'])->get();
        $events = Even::all();
        return view('admin.users.create', compact('roles', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|exists:roles,name',
            'ideven' => 'nullable|exists:evens,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'gambar' => 'no-image',
            'statusaktif' => 0,
            'ideven' => ($request->role == 'Admin Event') ? $request->ideven : null,
        ];

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('users', 'public');
            $userData['gambar'] = $path;
        }

        $user = User::create($userData);
        $user->assignRole($request->role);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pengguna berhasil ditambahkan.',
                'redirect' => route('admin.users.index')
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function resetPassword($id)
    {
        $id = decrypt($id);
        $user = User::findOrFail($id);
        $user->password = \Illuminate\Support\Facades\Hash::make('password');
        $user->save();

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password user ' . $user->name . ' berhasil direset.'
            ]);
        }

        return back()->with('success', 'Password user ' . $user->name . ' berhasil direset ke "password".');
    }

    public function toggleStatus($id)
    {
        $id = decrypt($id);
        $user = User::findOrFail($id);

        // Toggle: jika saat ini aktif (1), nonaktifkan semua (0); dan sebaliknya
        $newStatus = $user->statusaktif == 1 ? 0 : 1;

        $user->statusaktif    = $newStatus;
        $user->statusvalidasi = $newStatus;
        $user->is_active      = $newStatus;
        $user->save();

        $status = $newStatus == 1 ? 'diaktifkan' : 'dinonaktifkan';

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pengguna ' . $user->name . ' berhasil ' . $status . '.'
            ]);
        }

        return back()->with('success', 'Pengguna ' . $user->name . ' berhasil ' . $status . '.');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $user = User::findOrFail($id);
        $roles = Role::whereIn('name', ['Admin Aplikasi', 'Admin Laporan', 'Admin Event'])->get();
        $events = Even::all();
        
        if (!$roles->contains('name', $user->roles->first()?->name)) {
             $roles = Role::all();
        }
        return view('admin.users.edit', compact('user', 'roles', 'events'));
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'ideven' => 'nullable|exists:evens,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'ideven' => ($request->role == 'Admin Event') ? $request->ideven : null,
        ];

        if ($request->hasFile('gambar')) {
            if ($user->gambar && $user->gambar !== 'no-image') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->gambar);
            }
            $path = $request->file('gambar')->store('users', 'public');
            $userData['gambar'] = $path;
        }

        $user->update($userData);
        $user->syncRoles([$request->role]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data pengguna ' . $user->name . ' berhasil diperbarui.',
                'redirect' => route('admin.users.index')
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $id = decrypt($id);
            $user = User::findOrFail($id);
            $user->delete();

            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pengguna berhasil dihapus dari sistem.'
                ]);
            }

            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghapus pengguna. Data mungkin masih digunakan.'
                ], 500);
            }
            return redirect()->route('admin.users.index')->with('error', 'Gagal menghapus pengguna.');
        }
    }
}
