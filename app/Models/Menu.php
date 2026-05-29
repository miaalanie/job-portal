<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'namamenu', 
        'alamat_url',
        'namaroute',
        'icon',
        'submenu', 
        'idmenu', 
        'useradd', 
        'userupdate'
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'idmenu');
    }

    public function subMenus()
    {
        return $this->hasMany(Menu::class, 'idmenu');
    }

    public function aksesmenus()
    {
        return $this->hasMany(Aksesmenu::class, 'idmenu');
    }
}
