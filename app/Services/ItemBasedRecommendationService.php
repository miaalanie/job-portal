<?php

namespace App\Services;

use App\Models\Lowongan;
use App\Models\MlRequestLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ItemBasedRecommendationService
{
    public function recommend(int $idPelamar, int $limit = 12): Collection
    {
        $startedAt = microtime(true);
        $log = Log::channel('rekomendasi_cf');
        $log->info(" ");
        $log->info("========== REKOMENDASI CF | Pelamar #{$idPelamar} ==========");

        $userItems = $this->getUserItems($idPelamar);
        $result = $this->recommendFromItems($idPelamar, $userItems, $limit, $log);

        // Performance metrics for CF
        $durationMs = round((microtime(true) - $startedAt) * 1000, 2);

        Log::channel('rekomendasi_cf')->info('CF request metric', [
            'request_type' => 'cf_recommendation',
            'pelamar_id' => $idPelamar,
            'user_items_count' => $userItems->count(),
            'result_count' => $result->count(),
            'duration_ms' => $durationMs,
            'status' => 'success',
        ]);

        // Persist to database for admin dashboard
        try {
            MlRequestLog::create([
                'request_id' => (string) \Illuminate\Support\Str::uuid(),
                'request_type' => 'cf_recommendation',
                'endpoint' => 'cf_recommendation',
                'status' => 'success',
                'duration_ms' => $durationMs,
                'pelamar_id' => $idPelamar,
                'user_items_count' => $userItems->count(),
                'result_count' => $result->count(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan CF request log ke database', [
                'error' => $e->getMessage(),
            ]);
        }

        return $result;
    }

    /**
     * Replays CF with a supplied training history for offline evaluation.
     * The held-out items are deliberately absent from this history.
     */
    public function recommendForEvaluation(int $idPelamar, array $trainingItems, int $limit = 10): Collection
    {
        $log = Log::channel('rekomendasi_cf');
        return $this->recommendFromItems($idPelamar, collect($trainingItems), $limit, $log);
    }

    private function recommendFromItems(int $idPelamar, Collection $userItems, int $limit, $log): Collection
    {
        $namaLowongan = $this->getNamaLowonganMap();

        // ===================== STEP 1 =====================
        $log->info(" ");
        $log->info("===================== STEP 1: Histori Pelamar Target =====================");
        $log->info("Pelamar #{$idPelamar} pernah melamar/wishlist " . $userItems->count() . " lowongan:");
        foreach ($userItems as $id) {
            $log->info("  - #{$id} (" . ($namaLowongan[$id] ?? 'tidak diketahui') . ")");
        }

        if ($userItems->isEmpty()) {
            $log->warning("Pelamar #{$idPelamar} tidak punya histori interaksi. Skip (cold-start).");
            return collect();
        }

        // ===================== STEP 2 =====================
        $allInteractions = $this->getAllInteractions();
        $log->info(" ");
        $log->info("===================== STEP 2: Histori Seluruh Pelamar =====================");
        $log->info("Total pelamar lain dalam sistem: " . $allInteractions->count());
        $log->info("Contoh 20 pelamar pertama (sample, bukan semua):");
        foreach ($allInteractions->take(20) as $idLain => $jobs) {
            $namaJobs = array_map(fn($id) => ($namaLowongan[$id] ?? "ID#$id") . " (#$id)", $jobs);
            $log->info("  - Pelamar #{$idLain}: [" . implode(', ', $namaJobs) . "]");
        }

        // ===================== STEP 3 =====================
        [$scores, $contributors, $jumlahIrisan, $sampelIrisan] =
            $this->computeCoOccurrence($userItems->toArray(), $allInteractions, $idPelamar, $namaLowongan);

        $log->info(" ");
        $log->info("===================== STEP 3: Deteksi Kemiripan (Co-occurrence) =====================");
        $log->info("Logika: pelamar lain dianggap 'mirip' kalau minimal 1 lowongan di histori mereka sama dengan histori Pelamar #{$idPelamar}.");
        $log->info("Total pelamar yang terdeteksi mirip: {$jumlahIrisan} dari {$allInteractions->count()} total pelamar lain.");
        $log->info("Contoh " . count($sampelIrisan) . " dari {$jumlahIrisan} pelamar mirip tersebut:");
        foreach ($sampelIrisan as $line) {
            $log->info("  - {$line}");
        }

        if (empty($scores)) {
            $log->warning("Tidak ada kandidat lowongan ditemukan buat pelamar #{$idPelamar}.");
            return collect();
        }

        // ===================== STEP 4 =====================
        $log->info(" ");
        $log->info("===================== STEP 4: Voting / Scoring Kandidat =====================");
        $log->info("Logika: setiap 1 pelamar mirip menyumbang +1 skor ke tiap lowongan LAIN yang pernah mereka lamar (di luar histori Pelamar #{$idPelamar}).");
        $log->info("Total kandidat lowongan yang terkumpul skornya: " . count($scores));
        $log->info(" ");
        $log->info("Contoh perhitungan lengkap untuk 1 kandidat (biar keliatan cara skornya kebentuk):");

        $contohId = array_key_first($scores);
        $contohNama = $namaLowongan[$contohId] ?? "ID#$contohId";
        $totalVote = $scores[$contohId];
        $log->info("  Kandidat: {$contohNama} — total skor akhir: {$totalVote}");
        $log->info("  Rincian {$totalVote} vote didapat dari:");
        foreach ($contributors[$contohId] as $v) {
            $log->info("    + 1 dari {$v}");
        }

        $log->info(" ");
        $log->info("Sample skor kandidat lain (belum diurutkan):");
        foreach (array_slice($scores, 1, 20, true) as $id => $skor) {
            $log->info("  - " . ($namaLowongan[$id] ?? "ID#$id") . ": {$skor} vote");
        }

        // ===================== STEP 5 =====================
        arsort($scores);
        $topScores = array_slice($scores, 0, $limit, true);
        $topIds    = array_keys($topScores);

        $log->info(" ");
        $log->info("===================== STEP 5: Ranking & Top-{$limit} =====================");
        $log->info("Semua kandidat diurutkan dari skor tertinggi ke terendah, diambil {$limit} teratas.");
        foreach ($topScores as $idLowongan => $skor) {
            $nama = $namaLowongan[$idLowongan] ?? "ID#$idLowongan";
            $semua = implode('; ', $contributors[$idLowongan] ?? []);
            $log->info("  → {$nama} (skor: {$skor}) — via: {$semua}");
        }

        // Log CF co-occurrence metrics
        $log->info('CF co-occurrence detail', [
            'similar_users_count' => $jumlahIrisan,
            'candidate_count' => count($scores),
        ]);

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
    
    private function getNamaLowonganMap(): array
    {
        return Cache::remember('nama_lowongan_map', now()->addMinutes(30), function () {
            return Lowongan::pluck('namalowongan', 'id')->toArray();
        });
    }

    /**
     * @return array [scores, contributors, jumlahIrisan, sampelIrisan]
     */
    private function computeCoOccurrence(array $userItems, Collection $allInteractions, int $idPelamar, array $namaLowongan): array
    {
        $userSet      = array_flip($userItems);
        $scores       = [];
        $contributors = [];
        $jumlahIrisan = 0;
        $sampelIrisan = [];

        foreach ($allInteractions as $idPelamarLain => $jobs) {
            if ($idPelamarLain == $idPelamar) {
                continue; // skip diri sendiri
            }

            $shared = array_intersect($jobs, $userItems);
            if (empty($shared)) {
                continue;
            }

            $jumlahIrisan++;

            if (count($sampelIrisan) < 40) {
                $namaShared = array_map(fn($id) => $namaLowongan[$id] ?? "ID#$id", $shared);
                $sampelIrisan[] = "Pelamar #{$idPelamarLain} punya kesamaan: [" . implode(', ', $namaShared) . "]";
            }

            foreach ($shared as $seedJob) {
                foreach ($jobs as $candidateJob) {
                    if ($seedJob === $candidateJob || isset($userSet[$candidateJob])) {
                        continue;
                    }
                    $scores[$candidateJob] = ($scores[$candidateJob] ?? 0) + 1;
                    $contributors[$candidateJob][] = "Pelamar#{$idPelamarLain} (via " . ($namaLowongan[$seedJob] ?? "ID#$seedJob") . ")";
                }
            }
        }

        return [$scores, $contributors, $jumlahIrisan, $sampelIrisan];
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
