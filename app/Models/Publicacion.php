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

    const TEMPORADAS = [
        'invierno'  => 'Invierno / Aguas Frías',
        'primavera' => 'Primavera / Media Estación',
        'verano'    => 'Verano / Alta Actividad',
        'otono'     => 'Otoño / Depredadores',
    ];

    const LICENCIAS = [
        'interauton' => 'Licencia Interautonómica',
        'auton_1'    => 'Licencia Autonómica (1 Año)',
        'auton_5'    => 'Licencia Autonómica (5 Años)',
        'coto'       => 'Permiso de Coto Federado',
        'mar'        => 'Licencia de Mar / Costa',
    ];

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'latitud',
        'longitud',
        'temporada',
        'licencia',
    ];

    protected $casts = [
        'latitud'  => 'float',
        'longitud' => 'float',
    ];

    public function temporadaLabel(): string
    {
        return self::TEMPORADAS[$this->temporada] ?? '—';
    }

    public function licenciaLabel(): string
    {
        return self::LICENCIAS[$this->licencia] ?? '—';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class);
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
