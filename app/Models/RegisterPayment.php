<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisterPayment extends Model
{
    protected $fillable = [
        'idregister',
        'bank_asal',
        'nama_pengirim',
        'jumlah_bayar',
        'bukti_bayar',
        'tanggal_bayar',
        'status',
        'catatan'
    ];

    public function register()
    {
        return $this->belongsTo(Register::class, 'idregister');
    }
}
