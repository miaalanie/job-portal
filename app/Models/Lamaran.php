<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    protected $fillable = [
        'idpelamar', 
        'idlowongan', 
        'ideven', 
        'idsesi',
        'tanggal_datang',
        'tanggalmelamar', 
        'statusditerima', 
        'useradd', 
        'userupdate'
    ];

    public function sesi()
    {
        return $this->belongsTo(EvenSesi::class, 'idsesi');
    }

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'idpelamar');
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'idlowongan');
    }

    public function even()
    {
        return $this->belongsTo(Even::class, 'ideven');
    }

    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class, 'idlamaran');
    }
}
