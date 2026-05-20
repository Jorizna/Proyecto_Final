<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publicacion extends Model
{
    use HasFactory;

    protected $table = 'publicaciones';

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'latitud',
        'longitud',
        'valoracion_media',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
        'valoracion_media' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class);
    }

    public function valoraciones(): HasMany
    {
        return $this->hasMany(Valoracion::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(Imagen::class)->orderBy('orden');
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

    public function etiquetas(): BelongsToMany
    {
        return $this->belongsToMany(Etiqueta::class, 'publicacion_etiqueta');
    }

    public function imagenPrincipal(): ?Imagen
    {
        return $this->imagenes()->first();
    }
}
