<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Lowongan;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $user = Auth::user();
        if (!$user->idpelamar) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Lengkapi profil Anda sebelum menggunakan fitur Wishlist.'
            ], 403);
        }

        $request->validate([
            'idlowongan' => 'required|exists:lowongans,id'
        ]);

        try {
            $wishlist = Wishlist::where('idpelamar', $user->idpelamar)
                ->where('idlowongan', $request->idlowongan)
                ->first();

            if ($wishlist) {
                $wishlist->delete();
                return response()->json([
                    'status' => 'success',
                    'action' => 'removed',
                    'message' => 'Lowongan dihapus dari wishlist.'
                ]);
            } else {
                Wishlist::create([
                    'idpelamar' => $user->idpelamar,
                    'idlowongan' => $request->idlowongan
                ]);
                return response()->json([
                    'status' => 'success',
                    'action' => 'added',
                    'message' => 'Lowongan berhasil disimpan ke wishlist.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
