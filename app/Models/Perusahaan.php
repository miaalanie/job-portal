<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $fillable = [
        'nama', 
        'alamatlengkap', 
        'idkelurahan', 
        'idkategori', 
        'bentuk', 
        'logo', 
        'gambaranumum', 
        'telp', 
        'email', 
        'npwp',
        'nib',
        'website',
        'jumlah_karyawan',
        'tahunberdiri',
        'namapimpinan', 
        'pic', 
        'is_verified',
        'verified_at',
        'useradd', 
        'userupdate'
    ];

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'idkelurahan');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategoriperusahaan::class, 'idkategori');
    }

    public function registers()
    {
        return $this->hasMany(Register::class, 'idperusahaan');
    }

    public function dokumen()
    {
        return $this->hasMany(PerusahaanDokumen::class, 'idperusahaan');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'idperusahaan');
    }
}
