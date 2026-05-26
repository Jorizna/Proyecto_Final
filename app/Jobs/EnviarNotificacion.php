<?php

namespace App\Jobs;

use App\Models\Notificacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnviarNotificacion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $destinatario_id,
        public readonly int $actor_id,
        public readonly string $tipo,
        public readonly ?int $publicacion_id = null,
        public readonly ?int $tutorial_id = null,
    ) {}

    public function handle(): void
    {
        Notificacion::crear(
            $this->destinatario_id,
            $this->actor_id,
            $this->tipo,
            $this->publicacion_id,
            $this->tutorial_id,
        );
    }
}
