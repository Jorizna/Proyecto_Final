<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagenComentario extends Model
{
    protected $table = 'imagenes_comentario';

    protected $fillable = ['comentario_id', 'ruta', 'orden'];

    public function comentario(): BelongsTo
    {
        return $this->belongsTo(Comentario::class);
    }
}
