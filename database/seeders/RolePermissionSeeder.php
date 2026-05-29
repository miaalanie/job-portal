<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Superadmin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin Aplikasi']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin Perusahaan']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Pelamar']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin Laporan']);
    }
}
