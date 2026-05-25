<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tutorial extends Model
{
    use HasFactory;

    protected $table = 'tutoriales';

    protected $fillable = [
        'user_id',
        'titulo',
        'categoria',
        'contenido',
        'imagen_cabecera',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoriaLabel(): string
    {
        return match($this->categoria) {
            'tecnica'  => 'Técnica',
            'equipo'   => 'Equipamiento',
            'entorno'  => 'Entorno',
            default    => 'General',
        };
    }
}
