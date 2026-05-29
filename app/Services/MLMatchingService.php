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
        // URL ML service dari .env
        // ML_SERVICE_URL=http://localhost:8001
        $this->baseUrl = config('services.ml.url', 'http://localhost:8001');
        $this->timeout = config('services.ml.timeout', 60);
    }

    /**
     * Kirim data pelamar + loker ke ML service dan return hasil ranking.
     *
     * @param  \App\Models\Pelamar  $pelamar  (sudah di-load dengan relasi)
     * @param  \Illuminate\Support\Collection  $lowongans
     * @return array
     */
    public function match($pelamar, $lowongans): array
    {
        try {
            $payload = $this->buildPayload($pelamar, $lowongans);

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

    /**
     * Bangun payload JSON yang sesuai dengan schema ML service.
     *
     * Struktur payload:
     * {
     *   "pelamar": {
     *     "id": 1,
     *     "namalengkap": "...",
     *     "deskripsidiri": "...",
     *     "skills": [{"namaskill": "Python", "keterangan": "Baik"}],
     *     "pendidikans": [{"kategori": "S1", "jurusan": "Informatika", ...}],
     *     "pengalamans": [{"posisi": "Backend Dev", "tahunawal": 2020, ...}]
     *   },
     *   "lowongans": [
     *     {
     *       "id": 1,
     *       "namalowongan": "...",
     *       "deskripsi": "<p>HTML...</p>",
     *       "kategori": {"id": 1, "nama": "IT & Software"},
     *       "kategorilokasi": "Dalam Negeri",
     *       "gaji_awal": 5000000,
     *       "gaji_akhir": 8000000,
     *       "perusahaan_nama": "PT ABC",
     *       "perusahaan_logo": "logos/abc.png"
     *     }
     *   ]
     * }
     */
    private function buildPayload($pelamar, $lowongans): array
    {
        return [
            'pelamar'   => $this->buildPelamarPayload($pelamar),
            'lowongans' => $this->buildLowongansPayload($lowongans),
        ];
    }

    private function buildPelamarPayload($pelamar): array
    {
        return [
            'id'            => $pelamar->id,
            'namalengkap'   => $pelamar->namalengkap,
            'deskripsidiri' => $pelamar->deskripsidiri,

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

    private function buildLowongansPayload($lowongans): array
    {
        return $lowongans->map(fn($lo) => [
            'id'             => $lo->id,
            'namalowongan'   => $lo->namalowongan,
            'deskripsi'      => $lo->deskripsi,  // HTML — di-strip di Python
            'kategorilokasi' => $lo->kategorilokasi,
            'gaji_awal'      => $lo->gaji_awal ? (float) $lo->gaji_awal : null,
            'gaji_akhir'     => $lo->gaji_akhir ? (float) $lo->gaji_akhir : null,

            // Kategori sebagai object sesuai KategoriSchema di Python
            'kategori' => [
                'id'   => $lo->kategori->id,
                'nama' => $lo->kategori->nama,
            ],

            // Info perusahaan untuk ditampilkan di UI
            // Diambil dari relasi register.perusahaan
            'perusahaan_nama' => $lo->register->perusahaan->nama ?? null,
            'perusahaan_logo' => $lo->register->perusahaan->logo ?? null,
        ])->toArray();
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
            'lowongan' => [
                'id'             => $loker->id,
                'namalowongan'   => $loker->namalowongan,
                'deskripsi'      => $loker->deskripsi,
                'kategorilokasi' => $loker->kategorilokasi,
                'gaji_awal'      => $loker->gaji_awal  ? (float) $loker->gaji_awal  : null,
                'gaji_akhir'     => $loker->gaji_akhir ? (float) $loker->gaji_akhir : null,
                'kategori' => [
                    'id'   => $loker->kategori->id,
                    'nama' => $loker->kategori->nama,
                ],
            ],
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
