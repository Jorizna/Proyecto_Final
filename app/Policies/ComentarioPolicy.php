<?php

namespace App\Policies;

use App\Models\Comentario;
use App\Models\User;

class ComentarioPolicy
{
    public function delete(User $user, Comentario $comentario): bool
    {
        // El autor puede borrar su propio comentario; el moderador puede borrar cualquiera
        return $user->id === $comentario->user_id || $user->esModerador();
    }
}
