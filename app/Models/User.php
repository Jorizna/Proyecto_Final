<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'banner',
        'bio',
        'rol',
    ];

    public function esModerador(): bool
    {
        return $this->rol === 'moderador';
    }

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

    // ─── Helpers de perfil ────────────────────────────────────────────────────

    /** Carga todas las relaciones necesarias para renderizar la vista de perfil. */
    public function cargarRelacionesPerfil(): static
    {
        return $this->load([
            'publicaciones.imagenes',
            'publicaciones.etiquetas',
            'publicaciones.likes',
            'publicaciones.repostes',
            'repostes.publicacion.imagenes',
            'repostes.publicacion.user',
            'repostes.publicacion.etiquetas',
            'repostes.publicacion.likes',
            'repostes.publicacion.repostes',
            'comentarios.publicacion',
            'publicacionesLiked.imagenes',
            'publicacionesLiked.user',
            'publicacionesLiked.likes',
            'publicacionesLiked.repostes',
        ]);
    }

    /** Construye el feed combinado (publicaciones propias + repostes) ordenado por fecha. */
    public function feedCombinado(): \Illuminate\Support\Collection
    {
        $propias = $this->publicaciones->map(function ($publicacion) {
            return ['tipo' => 'publicacion', 'fecha' => $publicacion->created_at, 'item' => $publicacion];
        });

        // Filtramos repostes cuya publicación fue eliminada para evitar errores en la vista
        $repostes = $this->repostes
            ->filter(function ($reposte) {
                return $reposte->publicacion !== null;
            })
            ->map(function ($reposte) {
                return ['tipo' => 'reposte', 'fecha' => $reposte->created_at, 'item' => $reposte->publicacion];
            });

        return $propias->concat($repostes)->sortByDesc('fecha')->values();
    }
}
