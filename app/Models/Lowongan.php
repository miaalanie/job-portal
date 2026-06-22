<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lowongan extends Model
{
    protected $fillable = [
        'idregister',
        'namalowongan',
        'deskripsi',
        'gaji_awal',
        'gaji_akhir',
        'status',
        'kategorilokasi',
        'kuota',
        'idkategorilowongan',

        // Tambahan
        'minimal_pendidikan',
        'minimal_pengalaman_bulan',
        'preferensi_gender',
        'usia_min',
        'usia_max',

        'useradd',
        'userupdate'
    ];

    public function register()
    {
        return $this->belongsTo(Register::class, 'idregister');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategorilowongan::class, 'idkategorilowongan');
    }

    public function klasifikasis()
    {
        return $this->hasMany(Lowonganklasifikasi::class, 'idlowongan');
    }

    public function lamarans()
    {
        return $this->hasMany(Lamaran::class, 'idlowongan');
    }

    // Tambahan
    public function skills()
    {
        return $this->hasMany(LowonganSkill::class, 'idlowongan');
    }

    public function jurusans()
    {
        return $this->hasMany(LowonganJurusan::class, 'idlowongan');
    }
}