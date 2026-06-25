<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelamarpengalaman extends Model
{
    protected $table = 'pelamarpengalamen'; // Laravel pluralization might be different

    protected $fillable = [
        'idpelamar', 
        'namaperusahaan', 
        'posisi', 
        'tahunawal', 
        'bulanawal',
        'tahunselesai', 
        'bulanselesai',
        'aktif', 
        'useradd', 
        'userupdate'
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'idpelamar');
    }
}
