<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function publicaciones(): HasMany
    {
        return $this->hasMany(Publicacion::class);
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class);
    }

    public function valoraciones(): HasMany
    {
        return $this->hasMany(Valoracion::class);
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    public function publicacionesFavoritas()
    {
        return $this->belongsToMany(Publicacion::class, 'favoritos');
    }
}
