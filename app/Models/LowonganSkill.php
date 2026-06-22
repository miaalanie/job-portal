<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LowonganSkill extends Model
{
    protected $table = 'lowonganskills';

    protected $fillable = [
        'idlowongan',
        'idskill',
    ];

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'idlowongan');
    }

    public function skill()
    {
        return $this->belongsTo(MasterSkill::class, 'idskill');
    }
}