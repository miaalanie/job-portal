<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'superadmin@talent.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'gambar' => 'no-image',
                'statusaktif' => 1,
            ]
        );
        $user->assignRole('Superadmin');

        // Dummy Users
        $roles = ['Admin Aplikasi', 'Admin Perusahaan', 'Pelamar', 'Admin Laporan'];
        foreach ($roles as $roleName) {
            $u = User::create([
                'name' => 'User ' . $roleName,
                'email' => strtolower(str_replace(' ', '', $roleName)) . '@talent.id',
                'password' => Hash::make('password'),
                'gambar' => 'no-image',
            ]);
            $u->assignRole($roleName);
        }
    }
}
