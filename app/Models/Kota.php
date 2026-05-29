<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    protected $fillable = ['nama', 'idprovinsi', 'useradd', 'userupdate'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'idprovinsi');
    }

    public function kecamatans()
    {
        return $this->hasMany(Kecamatan::class, 'idkota');
    }
}
