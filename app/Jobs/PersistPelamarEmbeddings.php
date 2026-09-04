<?php

namespace App\Jobs;

use App\Models\Pelamar;
use App\Services\MLMatchingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PersistPelamarEmbeddings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public function __construct(public int $pelamarId)
    {
        $this->onQueue('embeddings');
    }

    public function handle(MLMatchingService $mlMatchingService): void
    {
        $pelamar = Pelamar::with(['skills', 'pendidikans', 'pengalamans'])
            ->findOrFail($this->pelamarId);

        $mlMatchingService->persistPelamarEmbeddings($pelamar);
    }
}