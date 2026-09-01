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
            'primary_color' => '#1e3a8a',
            'secondary_color' => '#1e293b',
            'logo' => null,
            'deskripsi' => 'Platform Rekrutmen & Job Fair',
            'alamat_lengkap' => '-',
            'email' => '-',
            'telp' => '-'
        ];

        $view->with('company', $company);
    }
}
