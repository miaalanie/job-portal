<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class  MLMatchingService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.ml.url', 'http://localhost:8001');
        $this->timeout = config('services.ml.timeout', 60);
    }

    public function match($pelamar, $lowongans): array
    {
        try {
            $payload = [
                'pelamar'   => $this->buildPelamarPayload($pelamar),
                'lowongans' => $this->buildLowongansPayload($lowongans),
            ];
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/match", $payload);

            if ($response->failed()) {
                Log::error('ML Service error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'ML service tidak dapat diakses.',
                ];
            }

            $result = $response->json();

            if (!empty($result['recommendations'])) {
                $result['recommendations'] = collect($result['recommendations'])->map(function ($item) {
                    $item['encrypted_id'] = \Illuminate\Support\Facades\Crypt::encrypt($item['lowongan_id']);
                    return $item;
                })->toArray();
            }

            return $result;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ML Service connection failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Tidak dapat terhubung ke ML service.',
            ];
        } catch (\Exception $e) {
            Log::error('ML Service unexpected error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan internal.',
            ];
        }
    }

    private function buildPelamarPayload($pelamar): array
    {
        return [
            'id'            => $pelamar->id,
            'namalengkap'   => $pelamar->namalengkap,
            'deskripsidiri' => $pelamar->deskripsidiri,
            'tanggallahir'  => $pelamar->tanggallahir,
            'jeniskelamin'  => $pelamar->jeniskelamin,

            'skills' => $pelamar->skills->map(fn($s) => [
                'namaskill'  => $s->namaskill,
                'keterangan' => $s->keterangan,
            ])->toArray(),

            'pendidikans' => $pelamar->pendidikans->map(fn($p) => [
                'kategori'     => $p->kategori,
                'jurusan'      => $p->jurusan,
                'tahunawal'    => (int) $p->tahunawal,
                'tahunselesai' => $p->tahunselesai ? (int) $p->tahunselesai : null,
            ])->toArray(),

            'pengalamans' => $pelamar->pengalamans->map(fn($e) => [
                'posisi'       => $e->posisi,
                'bulanawal'    => (int) $e->bulanawal,
                'tahunawal'    => (int) $e->tahunawal,
                'bulanselesai' => (int) $e->bulanselesai,
                'tahunselesai' => $e->tahunselesai ? (int) $e->tahunselesai : null,
                'aktif'        => (int) $e->aktif,
            ])->toArray(),

            'total_pengalaman_bulan' => $this->hitungTotalPengalaman($pelamar->pengalamans),
        ];
    }

    private function buildLowonganPayload($lowongan): array
    {
        return [
            'id'             => $lowongan->id,
            'namalowongan'   => $lowongan->namalowongan,
            'deskripsi'      => $lowongan->deskripsi,
            'kategorilokasi' => $lowongan->kategorilokasi,

            'gaji_awal'  => $lowongan->gaji_awal  ? (float) $lowongan->gaji_awal  : null,
            'gaji_akhir' => $lowongan->gaji_akhir ? (float) $lowongan->gaji_akhir : null,

            'minimal_pendidikan' => $lowongan->minimal_pendidikan
                ? [
                    'kode' => (int) $lowongan->minimal_pendidikan,
                    'nama' => $this->labelPendidikan($lowongan->minimal_pendidikan),
                ]
                : null,

            'minimal_pengalaman_bulan' => (int) $lowongan->minimal_pengalaman_bulan,
            'preferensi_gender'        => $lowongan->preferensi_gender,
            'usia_min'                 => (int) $lowongan->usia_min,
            'usia_max'                 => (int) $lowongan->usia_max,

            'kategori' => [
                'id'   => $lowongan->kategori->id,
                'nama' => $lowongan->kategori->nama,
            ],

            // hasMany ke LowonganSkill → perlu ->skill untuk ke MasterSkill
            'skills' => $lowongan->skills->map(fn($ls) => [
                'id'   => $ls->skill->id,
                'nama' => $ls->skill->namaskill,
            ])->toArray(),

            // hasMany ke LowonganJurusan → perlu ->jurusan untuk ke MasterJurusan
            'jurusans' => $lowongan->jurusans->map(fn($lj) => [
                'id'   => $lj->jurusan->id,
                'nama' => $lj->jurusan->namajurusan,
            ])->toArray(),

            'perusahaan_nama' => $lowongan->register->perusahaan->nama ?? null,
            'perusahaan_logo' => $lowongan->register->perusahaan->logo ?? null,
        ];
    }

    private function buildLowongansPayload($lowongans): array
    {
        return $lowongans
            ->map(fn($lo) => $this->buildLowonganPayload($lo))
            ->toArray();
    }

    private function hitungTotalPengalaman($pengalamans): int
    {
        $now = \Carbon\Carbon::now();
        $total = 0;

        foreach ($pengalamans as $e) {
            $start = \Carbon\Carbon::create(
                $e->tahunawal,
                $e->bulanawal ?: 1,
                1
            );

            $end = $e->aktif
                ? $now
                : \Carbon\Carbon::create(
                    $e->tahunselesai ?? $now->year,
                    $e->bulanselesai ?: 1,
                    1
                );

            $total += max(0, $start->diffInMonths($end));
        }

        return $total;
    }

    private function labelPendidikan(int $kode): string
    {
        return [
            1 => 'SD',
            2 => 'SMP',
            3 => 'SMA/SMK',
            4 => 'D1',
            5 => 'D2',
            6 => 'D3',
            7 => 'D4/S1',
            8 => 'S2',
            9 => 'S3',
        ][$kode] ?? '-';
    }
    public function rankApplicants($loker): array
    {
        try {
            $payload = $this->buildRankPayload($loker);

            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/rank-applicants", $payload);

            if ($response->failed()) {
                Log::error('ML Service rank error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return [
                    'success' => false,
                    'message' => 'ML service tidak dapat diakses.',
                ];
            }

            return $response->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ML Service rank connection failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Tidak dapat terhubung ke ML service.'];
        } catch (\Exception $e) {
            Log::error('ML Service rank unexpected error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Terjadi kesalahan internal.'];
        }
    }

    private function buildRankPayload($loker): array
    {
        return [
            'lowongan' => $this->buildLowonganPayload($loker),

            'pelamars' => $loker->lamarans
                ->filter(fn($l) => $l->pelamar !== null)
                ->map(fn($l) => $this->buildPelamarPayload($l->pelamar))
                ->values()
                ->toArray(),
        ];
    }

    /**
     * Health check ke ML service.
     * Bisa dipakai untuk cek status sebelum hit /match.
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
