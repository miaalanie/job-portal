<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategoriperusahaan extends Model
{
    protected $fillable = ['nama', 'useradd', 'userupdate'];

    public function perusahaans()
    {
        return $this->hasMany(Perusahaan::class, 'idkategori');
    }
}
