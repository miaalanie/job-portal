<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->statusaktif == 0 || !$user->is_active) {
                $errorMsg = !$user->is_active 
                    ? 'Akun Anda belum diaktivasi. Silakan cek email Anda.' 
                    : 'Akun Anda telah dinonaktifkan oleh administrator.';
                    
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $errorMsg
                    ], 403);
                }

                return back()->withErrors([
                    'email' => $errorMsg,
                ]);
            }

            $request->session()->regenerate();

            $redirectUrl = '/admin/dashboard';
            if ($user->hasRole('Pelamar')) {
                $redirectUrl = route('pelamar.dashboard');
            }

            if ($request->ajax() || $request->wantsJson()) {
                $request->session()->flash('success', 'Selamat Datang, ' . $user->name);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Berhasil masuk!',
                    'redirect' => $redirectUrl
                ]);
            }

            return redirect()->intended($redirectUrl);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Informasi akun yang dimasukkan salah.'
            ], 401);
        }

        return back()->withErrors([
            'email' => 'Informasi akun yang dimasukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
