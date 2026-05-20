<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use App\Models\Publicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    public function toggle(Publicacion $publicacion): RedirectResponse
    {
        $favorito = Favorito::where('user_id', Auth::id())
            ->where('publicacion_id', $publicacion->id)
            ->first();

        if ($favorito) {
            $favorito->delete();
            $mensaje = 'Zona eliminada de favoritos.';
        } else {
            Favorito::create([
                'user_id'        => Auth::id(),
                'publicacion_id' => $publicacion->id,
            ]);
            $mensaje = 'Zona añadida a favoritos.';
        }

        return redirect()->route('publicaciones.show', $publicacion)
            ->with('success', $mensaje);
    }
}
