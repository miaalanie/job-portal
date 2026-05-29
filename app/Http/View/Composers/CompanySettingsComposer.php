<?php

namespace App\Http\View\Composers;

use App\Models\PengaturanPerusahaan;
use Illuminate\View\View;

class CompanySettingsComposer
{
    public function compose(View $view)
    {
        $company = PengaturanPerusahaan::first() ?: (object)[
            'nama_perusahaan' => 'FindTalen Platform',
            'primary_color' => '#e11d48',
            'secondary_color' => '#0f172a',
            'logo' => null,
            'deskripsi' => 'Platform Rekrutmen & Job Fair',
            'alamat_lengkap' => '-',
            'email' => '-',
            'telp' => '-'
        ];

        $view->with('company', $company);
    }
}
