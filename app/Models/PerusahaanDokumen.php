<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerusahaanDokumen extends Model
{
    protected $table = 'perusahaan_dokumens';

    protected $fillable = [
        'idperusahaan',
        'nama_dokumen',
        'file_path',
        'status',
        'keterangan'
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'idperusahaan');
    }
}
