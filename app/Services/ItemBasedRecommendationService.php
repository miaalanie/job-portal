<?php

namespace App\Services;

use App\Models\Lowongan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ItemBasedRecommendationService
{
    public function recommend(int $idPelamar, int $limit = 6): Collection
    {
        $log = Log::channel('rekomendasi_cf');
        $log->info("========== REKOMENDASI CF | Pelamar #{$idPelamar} ==========");

        $userItems = $this->getUserItems($idPelamar);
        $log->info("Item milik pelamar: [" . $userItems->implode(', ') . "]");

        if ($userItems->isEmpty()) {
            $log->warning("Pelamar #{$idPelamar} tidak punya histori interaksi. Skip (cold-start).");
            return collect();
        }

        $allInteractions = $this->getAllInteractions();

        [$scores, $contributors] = $this->computeCoOccurrence($userItems->toArray(), $allInteractions, $idPelamar, $log);

        if (empty($scores)) {
            $log->warning("Tidak ada kandidat lowongan ditemukan buat pelamar #{$idPelamar}.");
            return collect();
        }

        arsort($scores);
        $topScores = array_slice($scores, 0, $limit, true);
        $topIds    = array_keys($topScores);

        $log->info("Hasil akhir (top {$limit}):");
        foreach ($topScores as $idLowongan => $skor) {
            $kontribusi = implode(' | ', array_slice($contributors[$idLowongan] ?? [], 0, 3));
            $log->info("  - Lowongan #{$idLowongan} | skor={$skor} | via: {$kontribusi}");
        }

        return $this->fetchActiveLowongan($topIds, $topScores);
    }

    // -------------------------------------------------------------------------

    private function getUserItems(int $idPelamar): Collection
    {
        return Cache::remember("user_items_{$idPelamar}", now()->addMinutes(10), function () use ($idPelamar) {
            return DB::table('lamarans')
                ->where('idpelamar', $idPelamar)
                ->select('idlowongan')
                ->union(
                    DB::table('wishlists')
                        ->where('idpelamar', $idPelamar)
                        ->select('idlowongan')
                )
                ->pluck('idlowongan')
                ->unique()
                ->values();
        });
    }

    private function getAllInteractions(): Collection
    {
        return Cache::remember('item_based_interactions', now()->addMinutes(30), function () {
            return DB::table('lamarans')
                ->select('idpelamar', 'idlowongan')
                ->union(
                    DB::table('wishlists')->select('idpelamar', 'idlowongan')
                )
                ->get()
                ->groupBy('idpelamar')
                ->map(fn($rows) => $rows->pluck('idlowongan')->unique()->values()->toArray());
        });
    }

    /**
     * @return array [scores, contributors]
     */
    private function computeCoOccurrence(array $userItems, Collection $allInteractions, int $idPelamar, $log): array
    {
        $userSet      = array_flip($userItems);
        $scores       = [];
        $contributors = [];
        $jumlahIrisan = 0;

        foreach ($allInteractions as $idPelamarLain => $jobs) {
            if ($idPelamarLain == $idPelamar) {
                continue; // skip diri sendiri
            }

            $shared = array_intersect($jobs, $userItems);
            if (empty($shared)) {
                continue; // <-- ini yang bikin 40rb baris kalau di-print semua, jadi kita skip & gak dicatat
            }

            $jumlahIrisan++;
            // catat cuma ringkasan, bukan detail tiap orang (biar log gak membengkak)
            if ($jumlahIrisan <= 20) { // batasi cuma 20 contoh pertama biar log tetap ringkas
                $log->debug("  Pelamar #{$idPelamarLain} punya irisan: [" . implode(',', $shared) . "] dari histori [" . implode(',', $jobs) . "]");
            }

            foreach ($shared as $seedJob) {
                foreach ($jobs as $candidateJob) {
                    if ($seedJob === $candidateJob || isset($userSet[$candidateJob])) {
                        continue;
                    }
                    $scores[$candidateJob] = ($scores[$candidateJob] ?? 0) + 1;
                    $contributors[$candidateJob][] = "Pelamar#{$idPelamarLain}(via lowongan {$seedJob})";
                }
            }
        }

        $log->info("Total pelamar lain yang punya irisan histori: {$jumlahIrisan}");
        if ($jumlahIrisan > 20) {
            $log->info("(Cuma 20 contoh pertama yang dicatat detailnya, biar log tidak membengkak)");
        }

        return [$scores, $contributors];
    }

    private function fetchActiveLowongan(array $orderedIds, array $scoreMap = []): Collection
    {
        $lowongans = Lowongan::with(['register.perusahaan', 'kategori'])
            ->whereIn('id', $orderedIds)
            ->whereHas('register', fn($q) => $q->where('aktivasi', 1))
            ->whereHas('register.even', fn($q) => $q->where('statusaktif', 1))
            ->get()
            ->sortBy(fn($l) => array_search($l->id, $orderedIds))
            ->values();

        $lowongans->each(function ($l) use ($scoreMap) {
            $l->similar_count = $scoreMap[$l->id] ?? 0;
        });

        return $lowongans;
    }

    public function clearCache(int $idPelamar): void
    {
        Cache::forget("user_items_{$idPelamar}");
        Cache::forget('item_based_interactions');
    }
}