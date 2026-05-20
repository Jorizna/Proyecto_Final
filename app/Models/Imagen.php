<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Imagen extends Model
{
    use HasFactory;

    protected $table = 'imagenes';

    protected $fillable = [
        'publicacion_id',
        'ruta',
        'orden',
    ];

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }
}
