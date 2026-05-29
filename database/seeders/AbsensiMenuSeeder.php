<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Aksesmenu;

class AbsensiMenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin/Superadmin (Parent: Kelola Event ID 8)
        $adminMenu = Menu::updateOrCreate(
            ['namaroute' => 'admin.absensi.index'],
            [
                'namamenu' => 'Absensi Kehadiran',
                'alamat_url' => '/admin/absensi',
                'icon' => 'fact_check',
                'submenu' => 0,
                'idmenu' => 8 
            ]
        );
        Aksesmenu::updateOrCreate(['idmenu' => $adminMenu->id, 'idrole' => 1]);
        Aksesmenu::updateOrCreate(['idmenu' => $adminMenu->id, 'idrole' => 2]);

        // 2. Perusahaan (Parent: Same as portal?)
        // Let's find a likely parent for Perusahaan (not Dashboard)
        $perusahaanParent = Menu::where('idmenu', 0)
            ->whereHas('aksesmenus', function($q){ $q->where('idrole', 3); })
            ->where('namamenu', '!=', 'Dashboard')
            ->where('namamenu', '!=', 'Dashboard Perusahaan')
            ->first();

        $absensiPerusahaan = Menu::updateOrCreate(
            ['namaroute' => 'admin.perusahaan.absensi.index'],
            [
                'namamenu' => 'Absensi & Scanner QR',
                'alamat_url' => '/admin/perusahaan/absensi',
                'icon' => 'qr_code_scanner',
                'submenu' => 0,
                'idmenu' => $perusahaanParent ? $perusahaanParent->id : 0
            ]
        );
        Aksesmenu::updateOrCreate(['idmenu' => $absensiPerusahaan->id, 'idrole' => 3]);
    }
}
