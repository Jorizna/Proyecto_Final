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
        'banner',
        'bio',
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

    public function tutoriales(): HasMany
    {
        return $this->hasMany(Tutorial::class);
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class)->latest();
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function repostes(): HasMany
    {
        return $this->hasMany(Reposte::class);
    }

    // Publicaciones que ha guardado (favoritos)
    public function publicacionesFavoritas()
    {
        return $this->belongsToMany(Publicacion::class, 'favoritos');
    }

    // Publicaciones que ha dado like
    public function publicacionesLiked()
    {
        return $this->belongsToMany(Publicacion::class, 'likes');
    }

    // Publicaciones que ha reposteado
    public function publicacionesReposteadas()
    {
        return $this->belongsToMany(Publicacion::class, 'repostes');
    }

    // Usuarios que este usuario sigue
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    // Usuarios que siguen a este usuario
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }
}
