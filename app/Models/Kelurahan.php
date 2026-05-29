<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $fillable = ['nama', 'idkecamatan', 'useradd', 'userupdate'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'idkecamatan');
    }
}
