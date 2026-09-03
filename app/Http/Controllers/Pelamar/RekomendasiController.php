<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Services\ItemBasedRecommendationService;
use App\Services\MLMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RekomendasiController extends Controller
{
    public function __construct(
        private MLMatchingService $mlService,
        private ItemBasedRecommendationService $itemBasedService
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
            'skills.skill',      // ← nested sampai MasterSkill
            'jurusans.jurusan',
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

    // ============================================================
    // TAB: Orang Serupa Melamar (Collaborative Filtering, Item-Based)
    // ============================================================
    public function getRekomendasiSerupa(): JsonResponse
    {
        $user = Auth::user();

        if (!$user->idpelamar) {
            return response()->json([
                'success' => false,
                'message' => 'Profil pelamar belum dilengkapi.',
            ], 422);
        }

        try {
            $lowongans = $this->itemBasedService->recommend($user->idpelamar, 6);

            if ($lowongans->isEmpty()) {
                return response()->json([
                    'success'         => true,
                    'total'           => 0,
                    'recommendations' => [],
                    'message'         => 'Belum ada data pelamar serupa saat ini.',
                ]);
            }

            $data = $lowongans->map(function ($l) {
                return [
                    'encrypted_id'    => encrypt($l->id),
                    'namalowongan'    => $l->namalowongan,
                    'perusahaan_nama' => $l->register->perusahaan->nama ?? '-',
                    'perusahaan_logo' => $l->register->perusahaan->logo ?? null,
                    'kategori'        => $l->kategori->nama ?? '-',
                    'lokasi'          => $l->kategorilokasi ?? 'Dalam Negeri',
                    'gaji_awal'       => $l->gaji_awal,
                    'gaji_akhir'      => $l->gaji_akhir,
                    'similar_count'   => $l->similar_count ?? 0,
                ];
            })->values();

            return response()->json([
                'success'         => true,
                'total'           => $data->count(),
                'recommendations' => $data,
            ]);

        } catch (\Throwable $e) {
            Log::error('Rekomendasi serupa error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan rekomendasi.',
            ], 500);
        }
    }
}