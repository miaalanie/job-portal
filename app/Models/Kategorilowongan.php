<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorilowongan extends Model
{
    protected $fillable = ['nama', 'useradd', 'userupdate'];

    public function lowongans()
    {
        return $this->hasMany(Lowongan::class, 'idkategorilowongan');
    }
}
