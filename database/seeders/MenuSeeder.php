<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Aksesmenu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $superadminRole = Role::where('name', 'Superadmin')->first();

        // 1. Dashboard (Single)
        $this->createMenuWithAkses('Dashboard', '/admin/dashboard', 'admin.dashboard', 0, null, 'dashboard', $superadminRole);

        // 2. Setting (Parent)
        $setting = $this->createMenuWithAkses('Setting', '#', null, 1, null, 'settings', $superadminRole);
        $this->createMenuWithAkses('Users', '/admin/users', 'admin.users', 0, $setting->id, 'person', $superadminRole);
        $this->createMenuWithAkses('Role', '/admin/roles', 'admin.roles', 0, $setting->id, 'security', $superadminRole);
        $this->createMenuWithAkses('Menu', '/admin/menu', 'admin.menu', 0, $setting->id, 'list', $superadminRole);
        $this->createMenuWithAkses('Role Menu', '/admin/role-menu', 'admin.role-menu', 0, $setting->id, 'admin_panel_settings', $superadminRole);
        $this->createMenuWithAkses('Event Job Fair', '/admin/event', 'admin.event', 0, $setting->id, 'event', $superadminRole);

        // 3. Pendaftar Event (Parent)
        $pendaftar = $this->createMenuWithAkses('Pendaftar Event', '#', null, 1, null, 'group_add', $superadminRole);
        $this->createMenuWithAkses('Kelola Data', '/admin/pendaftar-event', 'admin.pendaftar-event', 0, $pendaftar->id, 'manage_accounts', $superadminRole);

        // 4. Perusahaan (Parent)
        $perusahaan = $this->createMenuWithAkses('Perusahaan', '#', null, 1, null, 'business', $superadminRole);
        $this->createMenuWithAkses('Kategori Perusahaan', '/admin/kategori-perusahaan', 'admin.kategori-perusahaan', 0, $perusahaan->id, 'category', $superadminRole);
        $this->createMenuWithAkses('Kelola Data', '/admin/perusahaan-data', 'admin.perusahaan.index', 0, $perusahaan->id, 'corporate_fare', $superadminRole);

        // 5. Lowongan Kerja (Parent)
        $lowongan = $this->createMenuWithAkses('Lowongan Kerja', '#', null, 1, null, 'work', $superadminRole);
        $this->createMenuWithAkses('Data Lowongan Kerja', '/admin/lowongan-kerja', 'admin.lowongan.index', 0, $lowongan->id, 'list_alt', $superadminRole);
        $this->createMenuWithAkses('Data Registrasi Lowongan', '/admin/registrasi-lowongan', 'admin.registrasi-lowongan', 0, $lowongan->id, 'app_registration', $superadminRole);

        // 6. Pencari Kerja (Parent)
        $pencari = $this->createMenuWithAkses('Pencari Kerja', '#', null, 1, null, 'group', $superadminRole);
        $this->createMenuWithAkses('Kelola Data', '/admin/pencari-kerja', 'admin.pencari-kerja.index', 0, $pencari->id, 'person_search', $superadminRole);
    }

    private function createMenuWithAkses($nama, $url, $route, $isParent, $parentId, $icon, $role)
    {
        $menu = Menu::create([
            'namamenu' => $nama,
            'alamat_url' => $url,
            'namaroute' => $route,
            'icon' => $icon,
            'submenu' => $isParent,
            'idmenu' => $parentId
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
