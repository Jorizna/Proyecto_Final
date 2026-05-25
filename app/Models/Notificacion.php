<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'actor_id',
        'tipo',
        'publicacion_id',
        'tutorial_id',
        'leida',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }

    public function tutorial(): BelongsTo
    {
        return $this->belongsTo(Tutorial::class);
    }

    public function textoHtml(): string
    {
        $actor   = $this->actor   ? '<strong>' . e($this->actor->name) . '</strong>' : 'Alguien';
        $zona    = $this->publicacion ? '&ldquo;' . e($this->publicacion->titulo) . '&rdquo;' : 'tu zona';
        $tuto    = $this->tutorial    ? '&ldquo;' . e($this->tutorial->titulo)    . '&rdquo;' : 'un tutorial';

        return match ($this->tipo) {
            'like'       => "A {$actor} le ha gustado tu zona {$zona}",
            'comentario' => "{$actor} ha respondido en tu zona {$zona}",
            'favorito'   => "{$actor} ha compartido tu publicación {$zona}",
            'tutorial'   => "{$actor} ha publicado un nuevo tutorial: {$tuto}",
            'seguir'     => "{$actor} ha comenzado a seguirte",
            default      => "Nueva actividad de {$actor}",
        };
    }

    public function enlace(): string
    {
        if ($this->tipo === 'seguir' && $this->actor_id) {
            return route('usuarios.show', $this->actor_id);
        }
        if ($this->publicacion_id) {
            return route('publicaciones.show', $this->publicacion_id);
        }
        if ($this->tutorial_id) {
            return route('tutoriales.show', $this->tutorial_id);
        }
        return route('publicaciones.index');
    }

    public static function crear(int $userId, int $actorId, string $tipo, ?int $publicacionId = null, ?int $tutorialId = null): void
    {
        if ($userId === $actorId) {
            return;
        }

        self::create([
            'user_id'        => $userId,
            'actor_id'       => $actorId,
            'tipo'           => $tipo,
            'publicacion_id' => $publicacionId,
            'tutorial_id'    => $tutorialId,
        ]);
    }
}
