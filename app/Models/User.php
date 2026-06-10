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

    /**
     * Construye el feed combinado (publicaciones propias + repostes) ordenado por fecha.
     * Requiere haber llamado a cargarRelacionesPerfil() antes.
     */
    public function feedCombinado(): \Illuminate\Support\Collection
    {
        $propias = $this->publicaciones
            ->map(fn($p) => ['tipo' => 'publicacion', 'fecha' => $p->created_at, 'item' => $p]);

        $repostes = $this->repostes
            ->filter(fn($r) => $r->publicacion !== null)
            ->map(fn($r) => ['tipo' => 'reposte', 'fecha' => $r->created_at, 'item' => $r->publicacion]);

        return $propias->concat($repostes)->sortByDesc('fecha')->values();
    }
}
