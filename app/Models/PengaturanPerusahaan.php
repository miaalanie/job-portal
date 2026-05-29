<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanPerusahaan extends Model
{
    protected $table = 'pengaturan_perusahaans';
    
    protected $fillable = [
        'nama_perusahaan',
        'alamat_lengkap',
        'email',
        'telp',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'deskripsi',
        'fb',
        'ig',
        'website'
    ];
}
