<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Register;
use App\Models\Lowongan;
use App\Models\Kategoriperusahaan;
use App\Models\Kategorilowongan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BulkCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idPeriode = 1; // Karir Day Sukabumi 2026
        $role = Role::where('name', 'Admin Perusahaan')->first();
        
        $kategoriPers = Kategoriperusahaan::pluck('id')->toArray();
        $kategoriLoks = Kategorilowongan::pluck('id')->toArray();

        $companies = [
            ['nama' => 'PT Teknologi Digital Nusantara', 'bidang' => 'IT & Software'],
            ['nama' => 'CV Maju Mandiri Sejahtera', 'bidang' => 'Manufaktur'],
            ['nama' => 'PT Logistik Cepat Indonesia', 'bidang' => 'Transportasi & Logistik'],
            ['nama' => 'PT Finance Solution Asia', 'bidang' => 'Perbankan & Keuangan'],
            ['nama' => 'CV Kreatif Media Utama', 'bidang' => 'Media & Periklanan'],
            ['nama' => 'PT Sehat Sentosa Pharmacy', 'bidang' => 'Kesehatan & Farmasi'],
        ];

        $ts = time();
        foreach ($companies as $index => $cData) {
            // 1. Create Perusahaan
            $perusahaan = Perusahaan::create([
                'nama' => $cData['nama'],
                'email' => strtolower(str_replace([' ', '.'], '', $cData['nama'])) . $ts . '@example.com',
                'telp' => '0812345678' . $index,
                'idkategori' => $kategoriPers[array_rand($kategoriPers)] ?? 1,
                'bentuk' => (strpos($cData['nama'], 'PT') !== false) ? 'PT' : 'CV',
                'namapimpinan' => 'Pimpinan ' . $cData['nama'],
                'pic' => 'PIC ' . $cData['nama'],
                'alamatlengkap' => 'Jl. Industri No. ' . ($index + 1) . ', Sukabumi',
                'idkelurahan' => 1101010001, // Validated ID
                'useradd' => 1
            ]);

            // 2. Create User
            $email = $perusahaan->email;
            $user = User::create([
                'name' => 'Admin ' . $cData['nama'],
                'email' => $email,
                'password' => Hash::make('password'),
                'idperusahaan' => $perusahaan->id,
                'statusaktif' => 1,
                'is_active' => true,
                'useradd' => 1
            ]);
            
            if ($role) {
                $user->assignRole($role);
            }

            // 3. Create Register (Free Event for simplicity or use fixed cost)
            $register = Register::create([
                'idperusahaan' => $perusahaan->id,
                'idperiode' => $idPeriode,
                'namapaket' => 'Bronze Package',
                'biaya' => 0,
                'tanggalregister' => now(),
                'aktivasi' => 1,
                'useradd' => 1
            ]);

            // 4. Create 1 Lowongan for each
            Lowongan::create([
                'idregister' => $register->id,
                'namalowongan' => 'Staff ' . $cData['bidang'],
                'deskripsi' => 'Dibutuhkan segera Staff profesional untuk bidang ' . $cData['bidang'],
                'gaji_awal' => 4500000,
                'gaji_akhir' => 6000000,
                'status' => 'Aktif',
                'kategorilokasi' => 'On-site',
                'kuota' => 2,
                'idkategorilowongan' => $kategoriLoks[array_rand($kategoriLoks)] ?? 1,
                'useradd' => $user->id
            ]);
        }
    }
}
