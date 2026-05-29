<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lowonganklasifikasi extends Model
{
    protected $fillable = ['idlowongan', 'nama', 'useradd', 'userupdate'];

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'idlowongan');
    }
}
