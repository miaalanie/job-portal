<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gambar',
        'statusaktif',
        'activation_token',
        'is_active',
        'activated_at',
        'idperusahaan',
        'idpelamar',
        'ideven',
        'statusaktif',
        'statusvalidasi',
        'is_active',
        'useradd',
        'userupdate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'activated_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'idperusahaan');
    }

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'idpelamar');
    }

    public function even()
    {
        return $this->belongsTo(Even::class, 'ideven');
    }
}
