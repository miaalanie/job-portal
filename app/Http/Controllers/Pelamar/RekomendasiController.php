<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Services\MLMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RekomendasiController extends Controller
{
    public function __construct(
        private MLMatchingService $mlService
    ) {}

    public function getRekomendasi(): JsonResponse
    {
        $user = Auth::user();

        // Validasi: harus punya profil pelamar
        if (!$user->idpelamar) {
            return response()->json([
                'success' => false,
                'message' => 'Profil pelamar belum dilengkapi.',
            ], 422);
        }

        $pelamar = $user->pelamar->load([
            'skills',
            'pendidikans',
            'pengalamans',
        ]);

        $lowongans = Lowongan::with([
            'register.perusahaan',
            'register.even',
            'kategori',
        ])
            ->whereHas('register', function ($q) {
                $q->where('aktivasi', 1);
            })
            ->whereHas('register.even', function ($q) {
                $q->where('statusaktif', 1);
            })
            ->latest()
            ->limit(100)
            ->get();

        if ($lowongans->isEmpty()) {
            return response()->json([
                'success'         => true,
                'total'           => 0,
                'recommendations' => [],
                'message'         => 'Belum ada lowongan aktif saat ini.',
            ]);
        }

        // ============================================================
        // KIRIM KE ML SERVICE
        // ============================================================
        $result = $this->mlService->match($pelamar, $lowongans);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal mendapatkan rekomendasi.',
            ], 500);
        }

        return response()->json($result);
    }
}