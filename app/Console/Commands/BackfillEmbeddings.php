<?php

namespace App\Console\Commands;

use App\Jobs\PersistLowonganEmbeddings;
use App\Jobs\PersistPelamarEmbeddings;
use App\Models\Lowongan;
use App\Models\Pelamar;
use Illuminate\Console\Command;

class BackfillEmbeddings extends Command
{
    protected $signature = 'embeddings:backfill
                            {--type=all : Target: all, pelamar, or lowongan}
                            {--chunk=100 : Number of records loaded per database chunk}';

    protected $description = 'Queue embedding generation for existing applicants and vacancies.';

    public function handle(): int
    {
        $type = strtolower((string) $this->option('type'));
        $chunkSize = (int) $this->option('chunk');

        if (! in_array($type, ['all', 'pelamar', 'lowongan'], true)) {
            $this->error('Invalid --type. Use: all, pelamar, or lowongan.');

            return self::INVALID;
        }

        if ($chunkSize < 1 || $chunkSize > 1000) {
            $this->error('The --chunk option must be between 1 and 1000.');

            return self::INVALID;
        }

        $pelamarCount = 0;
        $lowonganCount = 0;

        if (in_array($type, ['all', 'pelamar'], true)) {
            $this->info('Queueing applicant embeddings...');

            Pelamar::query()
                ->select('id')
                ->chunkById($chunkSize, function ($pelamars) use (&$pelamarCount): void {
                    foreach ($pelamars as $pelamar) {
                        PersistPelamarEmbeddings::dispatch($pelamar->id);
                        $pelamarCount++;
                    }
                });
        }

        if (in_array($type, ['all', 'lowongan'], true)) {
            $this->info('Queueing vacancy embeddings...');

            Lowongan::query()
                ->select('id')
                ->chunkById($chunkSize, function ($lowongans) use (&$lowonganCount): void {
                    foreach ($lowongans as $lowongan) {
                        PersistLowonganEmbeddings::dispatch($lowongan->id);
                        $lowonganCount++;
                    }
                });
        }

        $total = $pelamarCount + $lowonganCount;

        $this->newLine();
        $this->info("Queued {$total} embedding job(s): {$pelamarCount} applicant(s), {$lowonganCount} vacancy/vacancies.");
        $this->warn('Generation runs asynchronously. Ensure a worker consumes the embeddings queue and the ML service is running.');

        return self::SUCCESS;
    }
}
