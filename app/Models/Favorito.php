<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';

    protected $fillable = [
        'user_id',
        'publicacion_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }
}
