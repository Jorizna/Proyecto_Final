<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Publicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function store(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $validated = $request->validate([
            'texto'      => 'required|string|max:1000',
            'imagenes'   => 'nullable|array|max:4',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $comentario = Comentario::create([
            'publicacion_id' => $publicacion->id,
            'user_id'        => Auth::id(),
            'texto'          => $validated['texto'],
        ]);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $orden => $file) {
                $ruta = $file->store('comentarios', 'public');
                $comentario->imagenes()->create(['ruta' => $ruta, 'orden' => $orden]);
            }
        }

        return redirect()->route('publicaciones.show', $publicacion)
            ->with('success', 'Respuesta añadida.');
    }

    public function destroy(Publicacion $publicacion, Comentario $comentario): RedirectResponse
    {
        if ($comentario->user_id !== Auth::id()) {
            abort(403);
        }

        $comentario->delete();

        return redirect()->route('publicaciones.show', $publicacion)
            ->with('success', 'Respuesta eliminada.');
    }
}
