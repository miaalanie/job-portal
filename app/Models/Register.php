<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    protected $fillable = [
        'idperusahaan', 
        'idperiode', 
        'namapaket', 
        'biaya',
        'tanggalregister', 
        'aktivasi', 
        'useradd', 
        'userupdate'
    ];

    public function getNamaPaketTampilAttribute()
    {
        if ($this->namapaket == "0") {
            return "Tanpa Paket";
        }

        if (is_numeric($this->namapaket)) {
            $paket = \App\Models\EvenPaket::find($this->namapaket);
            return $paket ? $paket->nama_paket : "Paket #" . $this->namapaket;
        }

        return $this->namapaket;
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'idperusahaan');
    }

    public function even()
    {
        return $this->belongsTo(Even::class, 'idperiode');
    }

    public function lowongans()
    {
        return $this->hasMany(Lowongan::class, 'idregister');
    }

    public function payment()
    {
        return $this->hasOne(RegisterPayment::class, 'idregister');
    }
}
