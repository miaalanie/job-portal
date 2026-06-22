<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterJurusan extends Model
{
    protected $table = 'masterjurusans';

    protected $fillable = [
        'namajurusan',
    ];

    public function lowongans()
    {
        return $this->hasMany(LowonganJurusan::class, 'idjurusan');
    }
}