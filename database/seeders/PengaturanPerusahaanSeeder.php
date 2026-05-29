<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaturanPerusahaan;

class PengaturanPerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PengaturanPerusahaan::updateOrCreate(
            ['id' => 1],
            [
                'nama_perusahaan' => 'FindTalen Indonesia',
                'alamat_lengkap' => 'Sentral Senayan II, Jl. Asia Afrika No.8, Jakarta Pusat',
                'email' => 'hello@findtalen.id',
                'telp' => '021-500-999',
                'primary_color' => '#e11d48', // Modern Crimson Red
                'secondary_color' => '#0f172a', // Elegant Dark Slate
                'deskripsi' => 'Platform Rekrutmen & Job Fair Paling Modern, Elegan, dan Terpercaya untuk Karir Masa Depan Anda.'
            ]
        );
    }
}
