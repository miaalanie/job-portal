<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelamar extends Model
{
    protected $fillable = [
        'noktp', 
        'nokartu_kuning', 
        'namalengkap', 
        'nohp',
        'alamatlengkap', 
        'idkelurahan', 
        'foto', 
        'deskripsidiri', 
        'tempatlahir', 
        'tanggallahir', 
        'jeniskelamin', 
        'tinggibadan', 
        'beratbadan', 
        'useradd', 
        'userupdate'
    ];

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'idkelurahan');
    }

    public function pendidikans()
    {
        return $this->hasMany(Pelamarpendidikan::class, 'idpelamar');
    }

    public function pengalamans()
    {
        return $this->hasMany(Pelamarpengalaman::class, 'idpelamar');
    }

    public function dokumens()
    {
        return $this->hasMany(Pelamardokumen::class, 'idpelamar');
    }

    public function skills()
    {
        return $this->hasMany(Pelamarskill::class, 'idpelamar');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'idpelamar');
    }

    public function lamarans()
    {
        return $this->hasMany(Lamaran::class, 'idpelamar');
    }
}
