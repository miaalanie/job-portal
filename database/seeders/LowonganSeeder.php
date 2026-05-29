<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lowongan;
use App\Models\Register;

class LowonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idRegister = 1;

        Lowongan::create([
            'idregister' => $idRegister,
            'namalowongan' => 'Senior Backend Developer',
            'deskripsi' => 'Kami mencari Senior Backend Developer berpengalaman dengan Laravel dan PostgreSQL.',
            'gaji_awal' => 8000000,
            'gaji_akhir' => 15000000,
            'status' => 'Aktif',
            'kategorilokasi' => 'On-site',
            'kuota' => 2,
            'idkategorilowongan' => 2, // Backend Developer
            'useradd' => 1
        ]);

        Lowongan::create([
            'idregister' => $idRegister,
            'namalowongan' => 'Lead Fullstack Developer',
            'deskripsi' => 'Dicari Lead Fullstack Developer untuk memimpin tim engineering dalam pengembangan platform SaaS.',
            'gaji_awal' => 12000000,
            'gaji_akhir' => 20000000,
            'status' => 'Aktif',
            'kategorilokasi' => 'Hybrid',
            'kuota' => 1,
            'idkategorilowongan' => 4, // Fullstack Developer
            'useradd' => 1
        ]);
    }
}
