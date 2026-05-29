<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks for faster seeding and to allow truncating
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Seed Provinces
        $this->command->info('Seeding Provinsis...');
        $this->seedFromCsv(public_path('csv/provinces.csv'), 'provinsis', ['id', 'nama']);

        // 2. Seed Cities
        $this->command->info('Seeding Kotas...');
        $this->seedFromCsv(public_path('csv/regencies.csv'), 'kotas', ['id', 'idprovinsi', 'nama']);

        // 3. Seed Districts
        $this->command->info('Seeding Kecamatans...');
        $this->seedFromCsv(public_path('csv/districts.csv'), 'kecamatans', ['id', 'idkota', 'nama']);

        // 4. Seed Villages
        $this->command->info('Seeding Kelurahans...');
        $this->seedFromCsv(public_path('csv/villages.csv'), 'kelurahans', ['id', 'idkecamatan', 'nama']);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Helper function to seed from CSV
     */
    private function seedFromCsv($filePath, $table, $columns)
    {
        if (!File::exists($filePath)) {
            $this->command->error("File not found: $filePath");
            return;
        }

        // Truncate table before seeding
        DB::table($table)->truncate();

        $file = fopen($filePath, 'r');
        $batch = [];
        $batchSize = 2500;
        $totalCount = 0;
        $seenIds = []; // Global tracker for deduplication

        while (($row = fgetcsv($file)) !== false) {
            if (empty($row[0])) continue;

            $id = $row[0];
            if (isset($seenIds[$id])) continue;
            $seenIds[$id] = true;

            $data = [];
            foreach ($columns as $index => $column) {
                $data[$column] = $row[$index];
            }
            
            // Add metadata
            $data['created_at'] = now();
            $data['updated_at'] = now();
            $data['useradd'] = 1;

            $batch[] = $data;
            $totalCount++;

            if (count($batch) >= $batchSize) {
                DB::table($table)->insert($batch);
                $batch = [];
                $this->command->getOutput()->write('.');
            }
        }

        if (!empty($batch)) {
            DB::table($table)->insert($batch);
        }

        fclose($file);
        $this->command->info("\nFinished seeding $totalCount rows into $table.");
    }
}
