<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSkill extends Model
{
    protected $table = 'masterskills';

    protected $fillable = [
        'namaskill',
    ];

    public function lowongans()
    {
        return $this->hasMany(LowonganSkill::class, 'idskill');
    }
}