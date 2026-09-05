<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\RecommendationSetting;
use App\Models\User;
use App\Services\ItemBasedRecommendationService;
use App\Services\MLMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    public function __construct(
        private readonly MLMatchingService $mlService,
        private readonly ItemBasedRecommendationService $itemBasedService
    )
    {
    }

    public function settings()
    {
        $this->ensureSuperadmin();
        $settings = RecommendationSetting::current();

        return view('admin.recommendation.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $this->ensureSuperadmin();

        $data = $request->validate([
            'weight_semantic' => ['required', 'numeric', 'min:0', 'max:1'],
            'weight_skill' => ['required', 'numeric', 'min:0', 'max:1'],
            'weight_education' => ['required', 'numeric', 'min:0', 'max:1'],
            'weight_experience' => ['required', 'numeric', 'min:0', 'max:1'],
            'skill_threshold' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $weightTotal = collect($data)->only([
            'weight_semantic',
            'weight_skill',
            'weight_education',
            'weight_experience',
        ])->sum();

        if (abs($weightTotal - 1) > 0.0001) {
            return back()->withInput()->withErrors([
                'weight_semantic' => 'Total bobot harus tepat 100% (1.00). Saat ini: ' . number_format($weightTotal * 100, 2) . '%.',
            ]);
        }

        RecommendationSetting::current()->update($data);

        return back()->with('success', 'Pengaturan rekomendasi berhasil disimpan dan akan dipakai pada pencocokan berikutnya.');
    }

    public function health()
    {
        $this->ensureSuperadmin();
        $health = $this->mlService->healthDetails();

        return view('admin.recommendation.health', compact('health'));
    }

    public function evaluation()
    {
        $this->ensureSuperadmin();

        return $this->renderEvaluation(false);
    }

    public function runEvaluation()
    {
        $this->ensureSuperadmin();

        return $this->renderEvaluation(true);
    }

    private function renderEvaluation(bool $run): \Illuminate\Contracts\View\View
    {
        $users = $this->evaluationUsers();
        $activeVacancies = $this->evaluationVacancies();
        $metrics = null;

        if ($run) {
            $cbfMetrics = $this->evaluateCbf($users, $activeVacancies);
            $cfMetrics = $this->evaluateCf($users, $activeVacancies);
            $metrics = collect([5, 10])->mapWithKeys(fn (int $k) => [
                $k => ['cbf' => $cbfMetrics[$k], 'cf' => $cfMetrics[$k]],
            ]);
        }

        return view('admin.recommendation.evaluation', [
            'metrics' => $metrics,
            'sampleCount' => $users->count(),
            'activeVacancyCount' => $activeVacancies->count(),
            'hasRun' => $run,
        ]);
    }

    private function evaluationUsers()
    {
        return Pelamar::query()
            ->whereHas('lamarans', fn ($query) => $query->where('statusditerima', 1))
            ->with([
                'skills',
                'pendidikans',
                'pengalamans',
                'lamarans:id,idpelamar,idlowongan,statusditerima',
            ])
            ->limit(20)
            ->get();
    }

    private function evaluationVacancies()
    {
        return Lowongan::with([
            'register.perusahaan',
            'kategori',
            'skills.skill',
            'jurusans.jurusan',
        ])
            ->whereHas('register', fn ($query) => $query->where('aktivasi', 1))
            ->whereHas('register.even', fn ($query) => $query->where('statusaktif', 1))
            ->latest()
            ->limit(100)
            ->get();
    }

    private function evaluateCbf($users, $activeVacancies): array
    {
        $rows = [5 => [], 10 => []];
        $predictedCoverage = [5 => collect(), 10 => collect()];
        $activeIds = $activeVacancies->pluck('id')->map(fn ($id) => (int) $id);

        foreach ($users as $pelamar) {
            $relevant = $pelamar->lamarans
                ->where('statusditerima', 1)
                ->pluck('idlowongan')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $activeIds->contains($id))
                ->unique()
                ->values();
            $training = $this->trainingItems($pelamar->id, $relevant);
            $candidates = $activeVacancies->whereNotIn('id', $training)->values();

            if ($relevant->isEmpty() || $candidates->isEmpty()) {
                continue;
            }

            $result = $this->mlService->match($pelamar, $candidates);
            if (empty($result['success'])) {
                continue;
            }

            $ranked = collect($result['recommendations'] ?? [])
                ->pluck('lowongan_id')
                ->map(fn ($id) => (int) $id)
                ->reject(fn ($id) => in_array($id, $training, true));

            foreach ([5, 10] as $k) {
                $predicted = $ranked->take($k)->values();
                $predictedCoverage[$k] = $predictedCoverage[$k]->merge($predicted);
                $rows[$k][] = $this->scoreAtK($predicted, $relevant, $k);
            }
        }

        return collect([5, 10])->mapWithKeys(fn (int $k) => [
            $k => $this->summarizeMetrics($rows[$k], $predictedCoverage[$k], $activeVacancies->count()),
        ])->all();
    }

    private function evaluateCf($users, $activeVacancies): array
    {
        $rows = [5 => [], 10 => []];
        $predictedCoverage = [5 => collect(), 10 => collect()];
        $activeIds = $activeVacancies->pluck('id')->map(fn ($id) => (int) $id);

        foreach ($users as $pelamar) {
            $relevant = $pelamar->lamarans
                ->where('statusditerima', 1)
                ->pluck('idlowongan')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $activeIds->contains($id))
                ->unique()
                ->values();
            $training = $this->trainingItems($pelamar->id, $relevant);

            if ($relevant->isEmpty() || empty($training)) {
                continue;
            }

            $ranked = $this->itemBasedService
                ->recommendForEvaluation($pelamar->id, $training, 10)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();

            foreach ([5, 10] as $k) {
                $predicted = $ranked->take($k)->values();
                $predictedCoverage[$k] = $predictedCoverage[$k]->merge($predicted);
                $rows[$k][] = $this->scoreAtK($predicted, $relevant, $k);
            }
        }

        return collect([5, 10])->mapWithKeys(fn (int $k) => [
            $k => $this->summarizeMetrics($rows[$k], $predictedCoverage[$k], $activeVacancies->count()),
        ])->all();
    }

    private function trainingItems(int $idPelamar, $heldOut): array
    {
        $applied = Lamaran::where('idpelamar', $idPelamar)->pluck('idlowongan');
        $wishlisted = DB::table('wishlists')->where('idpelamar', $idPelamar)->pluck('idlowongan');

        return $applied->merge($wishlisted)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->reject(fn ($id) => $heldOut->contains($id))
            ->values()
            ->all();
    }

    private function scoreAtK($predicted, $relevant, int $k): array
    {
        $hits = $predicted->intersect($relevant)->unique()->count();
        $precision = $k > 0 ? $hits / $k : 0;
        $recall = $relevant->count() > 0 ? $hits / $relevant->count() : 0;
        $f1 = ($precision + $recall) > 0
            ? (2 * $precision * $recall) / ($precision + $recall)
            : 0;

        return [
            'precision' => $precision,
            'recall' => $recall,
            'f1' => $f1,
            'hit_rate' => $hits > 0 ? 1 : 0,
        ];
    }

    private function summarizeMetrics(array $rows, $predictedCoverage, ?int $candidateCount): array
    {
        $count = count($rows);
        $average = fn (string $metric) => $count > 0
            ? round(collect($rows)->avg($metric) * 100, 2)
            : 0;

        return [
            'evaluated_users' => $count,
            'precision' => $average('precision'),
            'recall' => $average('recall'),
            'f1' => $average('f1'),
            'hit_rate' => $average('hit_rate'),
            'coverage' => $candidateCount > 0
                ? round($predictedCoverage->unique()->count() / $candidateCount * 100, 2)
                : null,
        ];
    }

    private function ensureSuperadmin(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user?->hasRole('Superadmin'), 403);
    }
}
