<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelamarskill extends Model
{
    protected $fillable = [
        'idpelamar',
        'namaskill',
        'keterangan',
        'useradd'
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'idpelamar');
    }
}
