<?php

namespace App\Http\Controllers;

use App\Models\Etiqueta;
use App\Models\Imagen;
use App\Models\Like;
use App\Models\Publicacion;
use App\Models\Reposte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicacionController extends Controller
{
    public function index(): View
    {
        if (!Auth::check()) {
            return view('welcome');
        }

        $user = Auth::user();
        $followingIds = $user->following()->pluck('users.id');

        if ($followingIds->isNotEmpty()) {
            $ownIds     = Publicacion::where('user_id', $user->id)->pluck('id');
            $followIds  = Publicacion::whereIn('user_id', $followingIds)->pluck('id');
            $likedIds   = Like::whereIn('user_id', $followingIds)->pluck('publicacion_id');
            $repostIds  = Reposte::whereIn('user_id', $followingIds)->pluck('publicacion_id');
            $allIds     = $ownIds->merge($followIds)->merge($likedIds)->merge($repostIds)->unique();

            $publicaciones = Publicacion::with(['user', 'imagenes', 'etiquetas', 'likes', 'comentarios'])
                ->whereIn('id', $allIds)
                ->latest()
                ->get();
        } else {
            $publicaciones = Publicacion::with(['user', 'imagenes', 'etiquetas', 'likes', 'comentarios'])
                ->latest()
                ->get();
        }

        return view('publicaciones.index', compact('publicaciones'));
    }

    public function buscar(Request $request): View
    {
        $q = trim($request->get('q', ''));
        $etiquetas = Etiqueta::orderBy('nombre')->get();

        $publicaciones = Publicacion::with(['user', 'imagenes', 'etiquetas', 'likes'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('titulo', 'like', "%{$q}%")
                       ->orWhere('descripcion', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->get();

        return view('publicaciones.buscar', compact('publicaciones', 'q', 'etiquetas'));
    }

    public function create(): View
    {
        $etiquetas = Etiqueta::orderBy('nombre')->get();

        return view('publicaciones.create', compact('etiquetas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'required|string|max:2000',
            'latitud'     => 'required|numeric|between:-90,90',
            'longitud'    => 'required|numeric|between:-180,180',
            'etiquetas'   => 'nullable|array',
            'etiquetas.*' => 'exists:etiquetas,id',
            'imagenes'    => 'nullable|array|max:8',
            'imagenes.*'  => [
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

        $publicacion = Publicacion::create([
            'user_id'     => Auth::id(),
            'titulo'      => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'latitud'     => $validated['latitud'],
            'longitud'    => $validated['longitud'],
        ]);

        if (!empty($validated['etiquetas'])) {
            $publicacion->etiquetas()->sync($validated['etiquetas']);
        }

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $orden => $imagen) {
                $ruta = $imagen->store('publicaciones', 'public');
                Imagen::create([
                    'publicacion_id' => $publicacion->id,
                    'ruta'           => $ruta,
                    'orden'          => $orden,
                ]);
            }
        }

        return redirect()->route('publicaciones.show', $publicacion)
            ->with('success', 'Zona de pesca publicada.');
    }

    public function show(Publicacion $publicacion): View
    {
        $publicacion->load([
            'user',
            'imagenes',
            'etiquetas',
            'likes',
            'repostes',
            'favoritos',
            'comentarios' => function ($q) {
                $q->whereNull('parent_id')
                  ->with([
                      'user', 'imagenes',
                      'children.user', 'children.imagenes',
                      'children.children.user', 'children.children.imagenes',
                      'children.children.children.user', 'children.children.children.imagenes',
                  ])
                  ->latest();
            },
        ]);

        $esLiked      = false;
        $esReposteado = false;
        $esFavorito   = false;

        if (Auth::check()) {
            $esLiked      = $publicacion->likes()->where('user_id', Auth::id())->exists();
            $esReposteado = $publicacion->repostes()->where('user_id', Auth::id())->exists();
            $esFavorito   = $publicacion->favoritos()->where('user_id', Auth::id())->exists();
        }

        return view('publicaciones.show', compact(
            'publicacion',
            'esLiked',
            'esReposteado',
            'esFavorito'
        ));
    }

    public function edit(Publicacion $publicacion): View
    {
        $this->authorize('update', $publicacion);

        $etiquetas = Etiqueta::orderBy('nombre')->get();
        $etiquetasSeleccionadas = $publicacion->etiquetas->pluck('id')->toArray();

        return view('publicaciones.edit', compact('publicacion', 'etiquetas', 'etiquetasSeleccionadas'));
    }

    public function update(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $this->authorize('update', $publicacion);

        $validated = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'required|string|max:2000',
            'latitud'     => 'required|numeric|between:-90,90',
            'longitud'    => 'required|numeric|between:-180,180',
            'etiquetas'   => 'nullable|array',
            'etiquetas.*' => 'exists:etiquetas,id',
            'imagenes'    => 'nullable|array|max:8',
            'imagenes.*'  => [
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

        $publicacion->update([
            'titulo'      => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'latitud'     => $validated['latitud'],
            'longitud'    => $validated['longitud'],
        ]);

        $publicacion->etiquetas()->sync($validated['etiquetas'] ?? []);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $orden => $imagen) {
                $ruta = $imagen->store('publicaciones', 'public');
                Imagen::create([
                    'publicacion_id' => $publicacion->id,
                    'ruta'           => $ruta,
                    'orden'          => $publicacion->imagenes()->count() + $orden,
                ]);
            }
        }

        return redirect()->route('publicaciones.show', $publicacion)
            ->with('success', 'Zona de pesca actualizada.');
    }

    public function destroy(Publicacion $publicacion): RedirectResponse
    {
        $this->authorize('delete', $publicacion);

        foreach ($publicacion->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->ruta);
        }

        $publicacion->delete();

        return redirect()->route('publicaciones.index')
            ->with('success', 'Zona de pesca eliminada.');
    }

    public function destroyImagen(Publicacion $publicacion, Imagen $imagen): RedirectResponse
    {
        $this->authorize('update', $publicacion);

        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();

        return back()->with('success', 'Imagen eliminada.');
    }
}
