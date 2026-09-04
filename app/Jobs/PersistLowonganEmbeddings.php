<?php

namespace App\Jobs;

use App\Models\Lowongan;
use App\Services\MLMatchingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PersistLowonganEmbeddings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public function __construct(public int $lowonganId)
    {
        $this->onQueue('embeddings');
    }

    public function handle(MLMatchingService $mlMatchingService): void
    {
        $lowongan = Lowongan::with([
            'register.perusahaan',
            'kategori',
            'skills.skill',
            'jurusans.jurusan',
        ])->findOrFail($this->lowonganId);

        $mlMatchingService->persistLowonganEmbeddings($lowongan);
    }
}