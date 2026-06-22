<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LowonganJurusan extends Model
{
    protected $table = 'lowonganjurusans';

    protected $fillable = [
        'idlowongan',
        'idjurusan',
    ];

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'idlowongan');
    }

    public function jurusan()
    {
        return $this->belongsTo(MasterJurusan::class, 'idjurusan');
    }
}