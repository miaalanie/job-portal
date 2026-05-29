<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = ['nama', 'idkota', 'useradd', 'userupdate'];

    public function kota()
    {
        return $this->belongsTo(Kota::class, 'idkota');
    }

    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class, 'idkecamatan');
    }
}
