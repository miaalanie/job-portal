<?php

namespace App\Services;

use App\Models\Lowongan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class ItemBasedRecommendationService
{
    public function recommend(int $idPelamar, int $limit = 6): Collection
    {
        $userItems = $this->getUserItems($idPelamar);

        if ($userItems->isEmpty()) {
            return collect();
        }

        $allInteractions = $this->getAllInteractions();
        $scores          = $this->computeCoOccurrence($userItems->toArray(), $allInteractions);

        if (empty($scores)) {
            return collect();
        }

        arsort($scores);
        $topIds = array_slice(array_keys($scores), 0, $limit);

        return $this->fetchActiveLowongan($topIds);
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

    private function computeCoOccurrence(array $userItems, Collection $allInteractions): array
    {
        $userSet = array_flip($userItems); // O(1) lookup
        $scores  = [];

        foreach ($allInteractions as $jobs) {
            // skip user yang tidak punya irisan sama sekali
            $shared = array_intersect($jobs, $userItems);
            if (empty($shared)) {
                continue;
            }

            foreach ($shared as $seedJob) {
                foreach ($jobs as $candidateJob) {
                    if ($seedJob === $candidateJob || isset($userSet[$candidateJob])) {
                        continue;
                    }
                    $scores[$candidateJob] = ($scores[$candidateJob] ?? 0) + 1;
                }
            }
        }

        return $scores;
    }

    private function fetchActiveLowongan(array $orderedIds): Collection
    {
        $lowongans = Lowongan::with(['register.perusahaan', 'kategori'])
            ->whereIn('id', $orderedIds)
            ->whereHas('register', fn($q) => $q->where('aktivasi', 1))
            ->whereHas('register.even', fn($q) => $q->where('statusaktif', 1))
            ->get()
            // jaga urutan sesuai skor
            ->sortBy(fn($l) => array_search($l->id, $orderedIds))
            ->values();

        return $lowongans;
    }

    public function clearCache(int $idPelamar): void
    {
        Cache::forget("user_items_{$idPelamar}");
        Cache::forget('item_based_interactions');
    }
}
