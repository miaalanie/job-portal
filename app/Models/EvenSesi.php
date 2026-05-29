<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvenSesi extends Model
{
    protected $fillable = [
        'even_id',
        'nama_sesi',
        'jam_mulai',
        'jam_selesai',
        'kuota'
    ];

    public function even()
    {
        return $this->belongsTo(Even::class, 'even_id');
    }
}
