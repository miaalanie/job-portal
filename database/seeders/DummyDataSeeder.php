<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Even;
use App\Models\EvenSesi;
use App\Models\Perusahaan;
use App\Models\Register;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Lamaran;
use App\Models\User;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\EvenSponsor;
use App\Models\EvenPaket;
use App\Models\PengaturanPerusahaan;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // 0. System Settings
        PengaturanPerusahaan::updateOrCreate(
            ['id' => 1],
            [
                'nama_perusahaan' => 'FindTalen Solution',
                'primary_color' => '#7f1d1d',
                'secondary_color' => '#111827'
            ]
        );

        // 1. Geography Setup
        $prov = Provinsi::updateOrCreate(['nama' => 'Jawa Barat']);
        $kota = Kota::updateOrCreate(['nama' => 'Sukabumi', 'idprovinsi' => $prov->id]);
        $kec = Kecamatan::updateOrCreate(['nama' => 'Cikole', 'idkota' => $kota->id]);
        $kel = Kelurahan::updateOrCreate(['nama' => 'Selabatu', 'idkecamatan' => $kec->id]);

        // 2. Categories
        DB::table('kategoriperusahaans')->updateOrInsert(['id' => 1], ['nama' => 'Teknologi Informasi']);
        DB::table('kategorilowongans')->updateOrInsert(['id' => 1], ['nama' => 'Engineering']);

        // 3. User Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@findtalen.com'],
            [
                'name' => 'Super Admin', 
                'password' => Hash::make('password'),
                'statusaktif' => 1,
            ]
        );
        if (!$admin->hasRole('Superadmin')) {
            $admin->assignRole('Superadmin');
        }

        // 4. Event
        $event = Even::updateOrCreate(
            ['namaperiode' => 'Grand Job Fair Sukabumi 2026'],
            [
                'visi' => 'Menghubungkan Talenta Muda dengan Industri Masa Depan melalui Inovasi Tanpa Batas.',
                'tanggalawal' => Carbon::now()->addDays(5),
                'tanggalselesai' => Carbon::now()->addDays(7),
                'lokasi' => 'Gedung Juang 45 Sukabumi',
                'alamat_lengkap' => 'Jl. Veteran No.6, Selabatu, Kec. Cikole, Kota Sukabumi, Jawa Barat',
                'latitude' => '-6.919702',
                'longitude' => '106.927218',
                'statusaktif' => 1,
                'statusheadline' => 1,
                'statuspaket' => 1,
                'status_sesi' => 1,
                'kuota_maksimum' => 1000,
                'maksimum_apply' => 5,
                'keterangan' => 'Event terbesar di Sukabumi dengan lebih dari 50 perusahaan ternama.',
                'useradd' => $admin->id
            ]
        );

        // 5. Sponsors & Packages
        EvenSponsor::updateOrCreate(['ideven' => $event->id, 'nama' => 'Bank Mandiri']);
        EvenSponsor::updateOrCreate(['ideven' => $event->id, 'nama' => 'Telkom Indonesia']);
        EvenSponsor::updateOrCreate(['ideven' => $event->id, 'nama' => 'Astra International']);
        EvenSponsor::updateOrCreate(['ideven' => $event->id, 'nama' => 'Pertamina']);

        EvenPaket::updateOrCreate(['ideven' => $event->id, 'nama_paket' => 'Gold'], ['harga' => 5000000, 'fasilitas' => 'Booth Utama, Logo di Poster']);
        EvenPaket::updateOrCreate(['ideven' => $event->id, 'nama_paket' => 'Silver'], ['harga' => 2500000, 'fasilitas' => 'Booth Standar, Logo Kecil']);

        // 6. Sessions
        EvenSesi::updateOrCreate(
            ['even_id' => $event->id, 'nama_sesi' => 'Sesi Pagi'],
            ['jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'kuota' => 500]
        );

        // 7. Companies
        $companies = [
            ['nama' => 'PT Teknologi Maju', 'email' => 'hrd@tekmadu.com'],
            ['nama' => 'Bank Sejahtera', 'email' => 'career@bsi.co.id'],
        ];

        foreach ($companies as $c) {
            $p = Perusahaan::updateOrCreate(
                ['email' => $c['email']],
                [
                    'nama' => $c['nama'],
                    'alamatlengkap' => 'Jl. Jend Sudirman Kav 1',
                    'idkelurahan' => $kel->id,
                    'idkategori' => 1,
                    'bentuk' => 'PT',
                    'telp' => '021-1234567',
                    'namapimpinan' => 'John Doe',
                    'pic' => 'Jane Doe',
                    'is_verified' => 1,
                    'verified_at' => Carbon::now()
                ]
            );

            $r = Register::updateOrCreate(
                ['idperusahaan' => $p->id, 'idperiode' => $event->id],
                [
                    'namapaket' => 'Gold', 
                    'tanggalregister' => Carbon::now(), 
                    'aktivasi' => 1
                ]
            );

            Lowongan::updateOrCreate(
                ['idregister' => $r->id, 'namalowongan' => 'Web Developer at ' . $c['nama']],
                [
                    'deskripsi' => 'Fullstack developer role.',
                    'status' => 'Aktif',
                    'kisaran_gaji' => 'Rp 8jt - 12jt',
                    'kategorilokasi' => 'Dalam Negeri',
                    'kuota' => 5,
                    'idkategorilowongan' => 1
                ]
            );
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
