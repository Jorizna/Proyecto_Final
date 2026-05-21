<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentarios';

    protected $fillable = [
        'publicacion_id',
        'user_id',
        'parent_id',
        'texto',
    ];

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comentario::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comentario::class, 'parent_id')->latest();
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenComentario::class)->orderBy('orden');
    }
}
