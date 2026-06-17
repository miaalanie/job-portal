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
            'tanggallahir' => $pelamar->tanggallahir,
            'jeniskelamin' => $pelamar->jeniskelamin,

            // Skills: hanya namaskill + keterangan yang dibutuhkan ML
            'skills' => $pelamar->skills->map(fn($s) => [
                'namaskill'  => $s->namaskill,
                'keterangan' => $s->keterangan,
            ])->toArray(),

            // Pendidikan: kategori + jurusan + tahun
            'pendidikans' => $pelamar->pendidikans->map(fn($p) => [
                'kategori'    => $p->kategori,
                'jurusan'     => $p->jurusan,
                'tahunawal'   => (int) $p->tahunawal,
                'tahunselesai' => $p->tahunselesai ? (int) $p->tahunselesai : null,
            ])->toArray(),

            // Pengalaman: posisi + tahun + aktif
            // Tidak ada field deskripsi di DB → kirim apa yang ada
            'pengalamans' => $pelamar->pengalamans->map(fn($e) => [
                'posisi'      => $e->posisi,
                'tahunawal'   => (int) $e->tahunawal,
                'tahunselesai' => $e->tahunselesai ? (int) $e->tahunselesai : null,
                'aktif'       => (int) $e->aktif,
            ])->toArray(),
        ];
    }

    private function buildLowonganPayload($lowongan): array
    {
        return [
            'id'             => $lowongan->id,
            'namalowongan'   => $lowongan->namalowongan,
            'deskripsi'      => $lowongan->deskripsi,
            'kategorilokasi' => $lowongan->kategorilokasi,

            'gaji_awal' => $lowongan->gaji_awal
                ? (float) $lowongan->gaji_awal
                : null,

            'gaji_akhir' => $lowongan->gaji_akhir
                ? (float) $lowongan->gaji_akhir
                : null,

            'kategori' => [
                'id'   => $lowongan->kategori->id,
                'nama' => $lowongan->kategori->nama,
            ],

            'perusahaan_nama' =>
            $lowongan->register->perusahaan->nama ?? null,

            'perusahaan_logo' =>
            $lowongan->register->perusahaan->logo ?? null,
        ];
    }

    private function buildLowongansPayload($lowongans): array
    {
        return $lowongans
            ->map(fn($lo) => $this->buildLowonganPayload($lo))
            ->toArray();
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
