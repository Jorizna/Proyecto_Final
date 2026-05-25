<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Notificacion;
use App\Models\Publicacion;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function store(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $validated = $request->validate([
            'texto'      => 'nullable|string|max:1000',
            'parent_id'  => 'nullable|exists:comentarios,id',
            'imagenes'   => 'nullable|array|max:4',
            'imagenes.*' => [
                'file',
                'mimes:jpeg,jpg,png,webp,gif',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    $maxKb = $value->getMimeType() === 'image/gif' ? 15360 : 5120;
                    if ($value->getSize() / 1024 > $maxKb) {
                        $fail('Las imágenes admiten hasta 5 MB (GIFs hasta 15 MB).');
                    }
                },
            ],
        ]);

        if (empty($validated['texto']) && !$request->hasFile('imagenes')) {
            return back()->withErrors(['texto' => 'Escribe un mensaje o adjunta una imagen.']);
        }

        $comentario = Comentario::create([
            'publicacion_id' => $publicacion->id,
            'user_id'        => Auth::id(),
            'parent_id'      => $validated['parent_id'] ?? null,
            'texto'          => $validated['texto'] ?? null,
        ]);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $orden => $file) {
                $ruta = ImageService::store($file, 'comentarios', maxWidth: 1200, quality: 80);
                $comentario->imagenes()->create(['ruta' => $ruta, 'orden' => $orden]);
            }
        }

        // Notify: post owner if top-level reply; parent comment owner if nested
        if ($comentario->parent_id) {
            $parent = Comentario::find($comentario->parent_id);
            if ($parent) {
                Notificacion::crear($parent->user_id, Auth::id(), 'comentario', $publicacion->id);
            }
        } else {
            Notificacion::crear($publicacion->user_id, Auth::id(), 'comentario', $publicacion->id);
        }

        return redirect()->route('publicaciones.show', $publicacion)
            ->with('success', 'Respuesta añadida.')
            ->withFragment('respuestas');
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
