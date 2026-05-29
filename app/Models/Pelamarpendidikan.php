<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelamarpendidikan extends Model
{
    protected $fillable = [
        'kategori', 
        'idpelamar', 
        'namasekolah', 
        'tahunawal', 
        'tahunselesai', 
        'jurusan', 
        'useradd', 
        'userupdate'
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'idpelamar');
    }
}
