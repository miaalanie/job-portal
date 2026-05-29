<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Even extends Model
{
    protected $fillable = [
        'namaperiode', 
        'visi',
        'tanggalawal', 
        'tanggalselesai', 
        'lokasi', 
        'alamat_lengkap',
        'latitude',
        'longitude',
        'statusaktif', 
        'statusheadline',
        'biaya',
        'statuspaket',
        'status_sesi',
        'kuota_maksimum',
        'maksimum_apply',
        'keterangan', 
        'gambar', 
        'gambar_layout',
        'useradd', 
        'userupdate'
    ];

    public function sponsors()
    {
        return $this->hasMany(EvenSponsor::class, 'ideven');
    }

    public function pakets()
    {
        return $this->hasMany(EvenPaket::class, 'ideven');
    }

    public function sesis()
    {
        return $this->hasMany(EvenSesi::class, 'even_id');
    }

    public function registers()
    {
        return $this->hasMany(Register::class, 'idperiode');
    }

    public function lamarans()
    {
        return $this->hasMany(Lamaran::class, 'ideven');
    }

    public function lowongans()
    {
        return $this->hasManyThrough(
            Lowongan::class,
            Register::class,
            'idperiode', // Foreign key on Register table
            'idregister', // Foreign key on Lowongan table
            'id',        // Local key on Even table
            'id'         // Local key on Register table
        );
    }
}
