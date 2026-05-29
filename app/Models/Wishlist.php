<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $guarded = [];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'idpelamar');
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'idlowongan');
    }
}
