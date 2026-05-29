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

    /**
     * Endpoint AJAX untuk rekomendasi lowongan.
     *
     * Dipanggil dari frontend via fetch() setelah dashboard selesai load.
     * Bukan dipanggil langsung dari server-side render — supaya dashboard
     * tetap responsif meski ML service butuh 5-10 detik.
     *
     * Flow:
     *   1. Ambil data pelamar lengkap (skills, edu, exp)
     *   2. Ambil loker aktif dari event yang sedang berjalan
     *   3. Kirim ke ML service → dapat ranking + score
     *   4. Return JSON ke frontend
     */
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

        // ============================================================
        // AMBIL LOKER AKTIF DARI EVENT YANG SEDANG BERJALAN
        //
        // Filter:
        //   - register.aktivasi = 1 (perusahaan sudah aktif di event)
        //   - register.even.statusaktif = 1 (event sedang aktif)
        //
        // Tidak filter berdasarkan tanggal karena career day bisa
        // berlangsung 1 hari saja, statusaktif yang jadi penentu.
        //
        // Limit 100: sweet spot antara coverage dan performa ML service.
        // Dengan 58 loker di data real, limit ini tidak akan tercapai.
        // ============================================================
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