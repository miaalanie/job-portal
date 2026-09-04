<?php

namespace App\Services;

use App\Models\Embedding;
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
            $this->loadPersistedEmbeddings($pelamar, $lowongans);
            $payload = [
                'pelamar'   => $this->buildPelamarPayload($pelamar, false, true),
                'lowongans' => $this->buildLowongansPayload($lowongans, true),
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

    /**
     * Generates all applicant vectors in the ML service and persists them locally.
     * This is intended for queued execution after the profile transaction commits.
     */
    public function persistPelamarEmbeddings($pelamar): void
    {
        $response = Http::timeout($this->timeout)
            ->post("{$this->baseUrl}/embeddings/pelamar", [
                'pelamar' => $this->buildPelamarPayload($pelamar, true),
            ]);

        if ($response->failed()) {
            Log::error('ML Service applicant embedding error', [
                'pelamar_id' => $pelamar->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('ML service gagal membuat embedding pelamar.');
        }

        $result = $response->json();

        if (empty($result['success']) || empty($result['embeddings'])) {
            throw new \RuntimeException('ML service mengembalikan hasil embedding pelamar yang tidak valid.');
        }

        $modelVersion = $result['model_version'] ?? 'paraphrase-multilingual-MiniLM-L12-v2';

        foreach ($result['embeddings'] as $item) {
            Embedding::updateOrCreate(
                [
                    'embeddable_type' => $item['embeddable_type'],
                    'embeddable_id' => $item['embeddable_id'],
                    'model_version' => $modelVersion,
                ],
                [
                    'vector' => $item['vector'],
                    'source_hash' => hash('sha256', $item['source_text']),
                    'status' => Embedding::STATUS_DONE,
                ]
            );
        }

    }

    /**
     * Generates all vacancy vectors in the ML service and persists them locally.
     */
    public function persistLowonganEmbeddings($lowongan): void
    {
        $response = Http::timeout($this->timeout)
            ->post("{$this->baseUrl}/embeddings/lowongan", [
                'lowongan' => $this->buildLowonganPayload($lowongan, true),
            ]);

        if ($response->failed()) {
            Log::error('ML Service vacancy embedding error', [
                'lowongan_id' => $lowongan->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('ML service gagal membuat embedding lowongan.');
        }

        $result = $response->json();

        if (empty($result['success']) || empty($result['embeddings'])) {
            throw new \RuntimeException('ML service mengembalikan hasil embedding lowongan yang tidak valid.');
        }

        $modelVersion = $result['model_version'] ?? 'paraphrase-multilingual-MiniLM-L12-v2';

        foreach ($result['embeddings'] as $item) {
            Embedding::updateOrCreate(
                [
                    'embeddable_type' => $item['embeddable_type'],
                    'embeddable_id' => $item['embeddable_id'],
                    'model_version' => $modelVersion,
                ],
                [
                    'vector' => $item['vector'],
                    'source_hash' => hash('sha256', $item['source_text']),
                    'status' => Embedding::STATUS_DONE,
                ]
            );
        }
    }

    private function loadPersistedEmbeddings($pelamar, $lowongans): void
    {
        $pelamars = $pelamar instanceof \Illuminate\Support\Collection
            ? $pelamar
            : collect([$pelamar]);

        $lowongans = $lowongans instanceof \Illuminate\Support\Collection
            ? $lowongans
            : collect($lowongans);

        foreach ($pelamars as $item) {
            $item->loadMissing(['skills', 'pendidikans', 'pengalamans']);
        }
        foreach ($lowongans as $item) {
            $item->loadMissing(['register.perusahaan', 'kategori', 'skills.skill', 'jurusans.jurusan']);
        }

        $keys = collect();
        foreach ($pelamars as $item) {
            $keys->push([Embedding::TYPE_PELAMAR_CV, $item->id]);
            foreach ($item->skills as $child) {
                $keys->push([Embedding::TYPE_PELAMAR_SKILL, $child->id]);
            }
            foreach ($item->pendidikans as $child) {
                $keys->push([Embedding::TYPE_PELAMAR_EDUCATION, $child->id]);
            }
            foreach ($item->pengalamans as $child) {
                $keys->push([Embedding::TYPE_PELAMAR_PENGALAMAN, $child->id]);
            }
        }
        foreach ($lowongans as $item) {
            $keys->push([Embedding::TYPE_LOWONGAN_REQUIREMENT, $item->id]);
            $keys->push([Embedding::TYPE_LOWONGAN_TITLE, $item->id]);
            foreach ($item->skills as $child) {
                $keys->push([Embedding::TYPE_LOWONGAN_SKILL, $child->id]);
            }
            foreach ($item->jurusans as $child) {
                $keys->push([Embedding::TYPE_LOWONGAN_EDUCATION, $child->id]);
            }
        }

        $keys = $keys->unique(fn ($key) => $key[0] . ':' . $key[1]);
        $embeddings = Embedding::query()
            ->where('model_version', Embedding::MODEL_VERSION)
            ->where('status', Embedding::STATUS_DONE)
            ->where(function ($query) use ($keys): void {
                foreach ($keys as [$type, $id]) {
                    $query->orWhere(function ($condition) use ($type, $id): void {
                        $condition->where('embeddable_type', $type)
                            ->where('embeddable_id', $id);
                    });
                }
            })
            ->get()
            ->keyBy(fn ($embedding) => $embedding->embeddable_type . ':' . $embedding->embeddable_id);

        foreach ($pelamars as $item) {
            $item->embedding = $embeddings->get(Embedding::TYPE_PELAMAR_CV . ':' . $item->id)?->vector;
            foreach ($item->skills as $child) {
                $child->embedding = $embeddings->get(Embedding::TYPE_PELAMAR_SKILL . ':' . $child->id)?->vector;
            }
            foreach ($item->pendidikans as $child) {
                $child->embedding = $embeddings->get(Embedding::TYPE_PELAMAR_EDUCATION . ':' . $child->id)?->vector;
            }
            foreach ($item->pengalamans as $child) {
                $child->embedding = $embeddings->get(Embedding::TYPE_PELAMAR_PENGALAMAN . ':' . $child->id)?->vector;
            }
        }
        foreach ($lowongans as $item) {
            $item->embedding = $embeddings->get(Embedding::TYPE_LOWONGAN_REQUIREMENT . ':' . $item->id)?->vector;
            $item->title_embedding = $embeddings->get(Embedding::TYPE_LOWONGAN_TITLE . ':' . $item->id)?->vector;
            foreach ($item->skills as $child) {
                $child->embedding = $embeddings->get(Embedding::TYPE_LOWONGAN_SKILL . ':' . $child->id)?->vector;
            }
            foreach ($item->jurusans as $child) {
                $child->embedding = $embeddings->get(Embedding::TYPE_LOWONGAN_EDUCATION . ':' . $child->id)?->vector;
            }
        }
    }

    private function buildPelamarPayload($pelamar, bool $includeRelationIds = false, bool $includeEmbeddings = false): array
    {
        return [
            'id'            => $pelamar->id,
            'namalengkap'   => $pelamar->namalengkap,
            'deskripsidiri' => $pelamar->deskripsidiri,
            'tanggallahir'  => $pelamar->tanggallahir,
            'jeniskelamin'  => $pelamar->jeniskelamin,

            'skills' => $pelamar->skills->map(fn($s) => array_filter([
                'id'         => $includeRelationIds ? $s->id : null,
                'namaskill'  => $s->namaskill,
                'keterangan' => $s->keterangan,
                'embedding'  => $includeEmbeddings ? $s->embedding : null,
            ], fn ($value) => $value !== null))->toArray(),

            'pendidikans' => $pelamar->pendidikans->map(fn($p) => array_filter([
                'id'           => $includeRelationIds ? $p->id : null,
                'kategori'     => $p->kategori,
                'jurusan'      => $p->jurusan,
                'tahunawal'    => (int) $p->tahunawal,
                'tahunselesai' => $p->tahunselesai ? (int) $p->tahunselesai : null,
                'embedding'    => $includeEmbeddings ? $p->embedding : null,
            ], fn ($value) => $value !== null))->toArray(),

            'pengalamans' => $pelamar->pengalamans->map(fn($e) => array_filter([
                'id'           => $includeRelationIds ? $e->id : null,
                'posisi'       => $e->posisi,
                'bulanawal'    => (int) $e->bulanawal,
                'tahunawal'    => (int) $e->tahunawal,
                'bulanselesai' => (int) $e->bulanselesai,
                'tahunselesai' => $e->tahunselesai ? (int) $e->tahunselesai : null,
                'aktif'        => (int) $e->aktif,
                'embedding'    => $includeEmbeddings ? $e->embedding : null,
            ], fn ($value) => $value !== null))->toArray(),

            'total_pengalaman_bulan' => $this->hitungTotalPengalaman($pelamar->pengalamans),
            'embedding' => $includeEmbeddings ? $pelamar->embedding : null,
        ];
    }

    private function buildLowonganPayload($lowongan, bool $useRelationIds = false, bool $includeEmbeddings = false): array
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
                'id'   => $useRelationIds ? $ls->id : $ls->skill->id,
                'nama' => $ls->skill->namaskill,
                'embedding' => $includeEmbeddings ? $ls->embedding : null,
            ])->toArray(),

            // hasMany ke LowonganJurusan → perlu ->jurusan untuk ke MasterJurusan
            'jurusans' => $lowongan->jurusans->map(fn($lj) => [
                'id'   => $useRelationIds ? $lj->id : $lj->jurusan->id,
                'nama' => $lj->jurusan->namajurusan,
                'embedding' => $includeEmbeddings ? $lj->embedding : null,
            ])->toArray(),

            'perusahaan_nama' => $lowongan->register->perusahaan->nama ?? null,
            'perusahaan_logo' => $lowongan->register->perusahaan->logo ?? null,
            'embedding' => $includeEmbeddings ? $lowongan->embedding : null,
            'title_embedding' => $includeEmbeddings ? $lowongan->title_embedding : null,
        ];
    }

    private function buildLowongansPayload($lowongans, bool $includeEmbeddings = false): array
    {
        return $lowongans
            ->map(fn($lo) => $this->buildLowonganPayload($lo, false, $includeEmbeddings))
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
            $pelamars = $loker->lamarans->pluck('pelamar')->filter()->values();
            $this->loadPersistedEmbeddings($pelamars, collect([$loker]));
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
            'lowongan' => $this->buildLowonganPayload($loker, false, true),

            'pelamars' => $loker->lamarans
                ->filter(fn($l) => $l->pelamar !== null)
                ->map(fn($l) => $this->buildPelamarPayload($l->pelamar, false, true))
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
