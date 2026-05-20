<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Valoracion extends Model
{
    use HasFactory;

    protected $table = 'valoraciones';

    protected $fillable = [
        'publicacion_id',
        'user_id',
        'puntuacion',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
    ];

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
