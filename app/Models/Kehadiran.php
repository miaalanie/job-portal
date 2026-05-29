<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    protected $fillable = [
        'idlamaran', 
        'statushadir', 
        'jam', 
        'tanggal', 
        'useradd', 
        'userupdate'
    ];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'idlamaran');
    }
}
