<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvenPaket extends Model
{
    protected $fillable = [
        'ideven', 
        'nama_paket', 
        'fasilitas', 
        'harga'
    ];

    public function even()
    {
        return $this->belongsTo(Even::class, 'ideven');
    }
}
