<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Aksesmenu;

class LaporanMenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Parent Menu: Laporan
        $parent = Menu::updateOrCreate(
            ['namamenu' => 'Laporan Management'],
            [
                'alamat_url' => '#',
                'namaroute' => '',
                'icon' => 'assessment',
                'submenu' => 1,
                'idmenu' => 0
            ]
        );

        // Assign to Superadmin (1) and Admin Aplikasi (2)
        Aksesmenu::updateOrCreate(['idmenu' => $parent->id, 'idrole' => 1]);
        Aksesmenu::updateOrCreate(['idmenu' => $parent->id, 'idrole' => 2]);

        // 2. Sub: Pelamar per Lowongan
        $s1 = Menu::updateOrCreate(
            ['namaroute' => 'admin.laporan.pelamar-loker'],
            [
                'namamenu' => 'Pelamar per Lowongan',
                'alamat_url' => '/admin/laporan/pelamar-loker',
                'icon' => 'people',
                'submenu' => 0,
                'idmenu' => $parent->id
            ]
        );
        Aksesmenu::updateOrCreate(['idmenu' => $s1->id, 'idrole' => 1]);
        Aksesmenu::updateOrCreate(['idmenu' => $s1->id, 'idrole' => 2]);

        // 3. Sub: Lowongan per Event
        $s2 = Menu::updateOrCreate(
            ['namaroute' => 'admin.laporan.loker-event'],
            [
                'namamenu' => 'Lowongan per Event',
                'alamat_url' => '/admin/laporan/loker-event',
                'icon' => 'event_note',
                'submenu' => 0,
                'idmenu' => $parent->id
            ]
        );
        Aksesmenu::updateOrCreate(['idmenu' => $s2->id, 'idrole' => 1]);
        Aksesmenu::updateOrCreate(['idmenu' => $s2->id, 'idrole' => 2]);

        // 4. Sub: Kehadiran Pelamar
        $s3 = Menu::updateOrCreate(
            ['namaroute' => 'admin.laporan.kehadiran'],
            [
                'namamenu' => 'Kehadiran Pelamar',
                'alamat_url' => '/admin/laporan/kehadiran',
                'icon' => 'rule',
                'submenu' => 0,
                'idmenu' => $parent->id
            ]
        );
        Aksesmenu::updateOrCreate(['idmenu' => $s3->id, 'idrole' => 1]);
        Aksesmenu::updateOrCreate(['idmenu' => $s3->id, 'idrole' => 2]);

        // 5. Sub: Detail Data Pelamar
        $s4 = Menu::updateOrCreate(
            ['namaroute' => 'admin.laporan.pelamar-detail'],
            [
                'namamenu' => 'Data Diri Pelamar',
                'alamat_url' => '/admin/laporan/pelamar-detail',
                'icon' => 'contact_page',
                'submenu' => 0,
                'idmenu' => $parent->id
            ]
        );
        Aksesmenu::updateOrCreate(['idmenu' => $s4->id, 'idrole' => 1]);
        Aksesmenu::updateOrCreate(['idmenu' => $s4->id, 'idrole' => 2]);
    }
}
