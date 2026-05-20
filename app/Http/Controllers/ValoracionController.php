<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Valoracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValoracionController extends Controller
{
    public function store(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $validated = $request->validate([
            'puntuacion' => 'required|integer|between:1,5',
        ]);

        Valoracion::updateOrCreate(
            [
                'publicacion_id' => $publicacion->id,
                'user_id'        => Auth::id(),
            ],
            [
                'puntuacion' => $validated['puntuacion'],
            ]
        );

        $publicacion->actualizarValoracionMedia();

        return redirect()->route('publicaciones.show', $publicacion)
            ->with('success', 'Valoración guardada correctamente.');
    }
}
