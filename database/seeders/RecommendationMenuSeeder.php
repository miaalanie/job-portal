<?php

namespace Database\Seeders;

use App\Models\Aksesmenu;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class RecommendationMenuSeeder extends Seeder
{
    public function run(): void
    {
        $parent = Menu::updateOrCreate(
            ['namaroute' => 'admin.recommendation.index'],
            [
                'namamenu' => 'Rekomendasi Sistem',
                'alamat_url' => '#',
                'icon' => 'auto_awesome',
                'submenu' => 1,
                'idmenu' => 0,
            ]
        );

        $items = [
            ['Pengaturan Skor', '/admin/rekomendasi/pengaturan', 'admin.recommendation.settings', 'tune'],
            ['Health ML Service', '/admin/rekomendasi/health', 'admin.recommendation.health', 'health_and_safety'],
            ['Evaluasi Sistem', '/admin/rekomendasi/evaluasi', 'admin.recommendation.evaluation', 'analytics'],
        ];

        foreach ($items as [$name, $url, $route, $icon]) {
            $menu = Menu::updateOrCreate(
                ['namaroute' => $route],
                [
                    'namamenu' => $name,
                    'alamat_url' => $url,
                    'icon' => $icon,
                    'submenu' => 0,
                    'idmenu' => $parent->id,
                ]
            );

            Aksesmenu::updateOrCreate(['idmenu' => $menu->id, 'idrole' => 1]);
        }

        Aksesmenu::updateOrCreate(['idmenu' => $parent->id, 'idrole' => 1]);
    }
}
