<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8|confirmed',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'gambar.max' => 'Ukuran foto maksimal 2MB.'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('gambar')) {
            // Delete old image if exists and not 'no-image'
            if ($user->gambar && $user->gambar !== 'no-image') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->gambar);
            }
            $path = $request->file('gambar')->store('users', 'public');
            $user->gambar = $path;
        }
        
        $user->save();

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
