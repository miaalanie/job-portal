<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Aksesmenu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PerusahaanMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'Admin Perusahaan')->first();
        if (!$role) {
            $role = Role::where('name', 'Perusahaan')->first();
        }

        if (!$role) {
            $this->command->error('Role Admin Perusahaan not found.');
            return;
        }

        // 1. Dashboard Perusahaan
        $this->createMenuWithAkses('Dashboard Perusahaan', '/admin/perusahaan/dashboard', 'admin.perusahaan.dashboard', 0, null, 'dashboard', $role);

        // 2. Hub Rekrutmen (Parent)
        $hub = $this->createMenuWithAkses('Hub Rekrutmen', '#', null, 1, null, 'work', $role);
        $this->createMenuWithAkses('Event Job Fair', '/admin/perusahaan/event', 'admin.perusahaan.event', 0, $hub->id, 'event', $role);
        $this->createMenuWithAkses('Daftar Lowongan', '/admin/perusahaan/dataloker', 'admin.perusahaan.loker.index', 0, $hub->id, 'list_alt', $role);
        $this->createMenuWithAkses('Daftar Pelamar', '/admin/perusahaan/pelamar', 'admin.perusahaan.pelamar.index', 0, $hub->id, 'people', $role);

        // 3. Pengaturan (Parent)
        $setting = $this->createMenuWithAkses('Pengaturan', '#', null, 1, null, 'settings', $role);
        $this->createMenuWithAkses('Profil Perusahaan', '/admin/perusahaan/profile', 'admin.perusahaan.profile', 0, $setting->id, 'domain', $role);
        $this->createMenuWithAkses('Profil Saya', '/admin/profile', 'admin.profile.index', 0, $setting->id, 'account_circle', $role);
    }

    private function createMenuWithAkses($nama, $url, $route, $isParent, $parentId, $icon, $role)
    {
        $menu = Menu::create([
            'namamenu' => $nama,
            'alamat_url' => $url,
            'namaroute' => $route,
            'icon' => $icon,
            'submenu' => $isParent,
            'idmenu' => $parentId,
            'useradd' => 1
        ]);

        if ($role) {
            Aksesmenu::create([
                'idmenu' => $menu->id,
                'idrole' => $role->id
            ]);
        }

        return $menu;
    }
}
