<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Aksesmenu extends Model
{
    protected $fillable = ['idmenu', 'idrole'];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idmenu');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'idrole');
    }
}
