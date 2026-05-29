<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelamardokumen extends Model
{
    protected $fillable = [
        'idpelamar', 
        'namadokumen', 
        'filedokumen', 
        'useradd', 
        'userupdate'
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'idpelamar');
    }
}
