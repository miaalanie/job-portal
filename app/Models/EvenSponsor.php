<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvenSponsor extends Model
{
    protected $fillable = [
        'ideven', 
        'nama', 
        'logo'
    ];

    public function even()
    {
        return $this->belongsTo(Even::class, 'ideven');
    }
}
