<?php

namespace App\Http\Controllers;

use App\Models\Etiqueta;
use App\Models\Imagen;
use App\Models\Publicacion;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicacionController extends Controller
{
    public function index(): View
    {
        if (!Auth::check()) {
            return view('welcome');
        }

        $user = Auth::user();

        // Si el usuario sigue a alguien, mostramos primero las zonas de su red
        $followingIds = $user->following()->pluck('users.id');

        $query = Publicacion::with(['user', 'imagenes', 'etiquetas', 'likes', 'comentarios'])
            ->where('user_id', '!=', $user->id);

        // Las zonas de personas seguidas aparecen primero; el resto se ordena por fecha
        if ($followingIds->isNotEmpty()) {
            $query->orderByRaw('CASE WHEN user_id IN (' . $followingIds->join(',') . ') THEN 0 ELSE 1 END');
        }

        $publicaciones = $query->latest()->paginate(20);

        return view('publicaciones.index', compact('publicaciones'));
    }

    public function buscar(Request $request): View
    {
        $q         = trim($request->get('q', ''));
        $etiquetas = Etiqueta::orderBy('nombre')->get();
        $temporadas = Publicacion::TEMPORADAS;
        $licencias  = Publicacion::LICENCIAS;

        $query = Publicacion::with(['user', 'imagenes', 'etiquetas', 'likes'])->latest();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('titulo', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        $publicaciones = $query->get();

        return view('publicaciones.buscar', compact(
            'publicaciones', 'q', 'etiquetas', 'temporadas', 'licencias'
        ));
    }

    public function create(): View
    {
        $etiquetas = Etiqueta::orderBy('nombre')->get();

        return view('publicaciones.create', compact('etiquetas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->reglasPublicacion());

        $publicacion = Publicacion::create([
            'user_id'     => Auth::id(),
            'titulo'      => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'latitud'     => $validated['latitud'],
            'longitud'    => $validated['longitud'],
            'temporada'   => $validated['temporada'] ?? null,
            'licencia'    => $validated['licencia'] ?? null,
        ]);

        if (!empty($validated['etiquetas'])) {
            $publicacion->etiquetas()->sync($validated['etiquetas']);
        }

        $this->guardarImagenes($request, $publicacion);

        // Invalidamos el caché para que el feed se reconstruya con la nueva publicación
        Cache::tags('feed')->flush();

        return redirect()->route('publicaciones.show', $publicacion)
            ->with('success', 'Zona de pesca publicada.');
    }

    public function show(Publicacion $publicacion): View
    {
        // Cargamos solo comentarios raíz (sin parent) con sus respuestas anidadas hasta 3 niveles
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
            $userId       = Auth::id();
            $esLiked      = $publicacion->likes()->where('user_id', $userId)->exists();
            $esReposteado = $publicacion->repostes()->where('user_id', $userId)->exists();
            $esFavorito   = $publicacion->favoritos()->where('user_id', $userId)->exists();
        }

        return view('publicaciones.show', compact(
            'publicacion', 'esLiked', 'esReposteado', 'esFavorito'
        ));
    }

    public function edit(Publicacion $publicacion): View
    {
        $this->authorize('update', $publicacion);

        $etiquetas             = Etiqueta::orderBy('nombre')->get();
        $etiquetasSeleccionadas = $publicacion->etiquetas->pluck('id')->toArray();

        return view('publicaciones.edit', compact('publicacion', 'etiquetas', 'etiquetasSeleccionadas'));
    }

    public function update(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $this->authorize('update', $publicacion);

        $validated = $request->validate($this->reglasPublicacion());

        $publicacion->update([
            'titulo'      => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'latitud'     => $validated['latitud'],
            'longitud'    => $validated['longitud'],
            'temporada'   => $validated['temporada'] ?? null,
            'licencia'    => $validated['licencia'] ?? null,
        ]);

        $publicacion->etiquetas()->sync($validated['etiquetas'] ?? []);

        $this->guardarImagenes($request, $publicacion, $publicacion->imagenes()->count());

        // Invalidamos el caché para que el feed muestre los datos actualizados
        Cache::tags('feed')->flush();

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

        // Invalidamos el caché para que el feed deje de mostrar la publicación eliminada
        Cache::tags('feed')->flush();

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

    // ─── Helpers privados ─────────────────────────────────────────────────────

    /** Reglas de validación compartidas entre store() y update(). */
    private function reglasPublicacion(): array
    {
        return [
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'required|string|max:2000',
            'latitud'     => 'required|numeric|between:-90,90',
            'longitud'    => 'required|numeric|between:-180,180',
            'temporada'   => 'nullable|in:invierno,primavera,verano,otono',
            'licencia'    => 'nullable|in:interauton,auton_1,auton_5,coto,mar',
            'etiquetas'   => 'nullable|array',
            'etiquetas.*' => 'exists:etiquetas,id',
            'imagenes'    => 'nullable|array|max:8',
            'imagenes.*'  => ['file', 'mimes:jpeg,jpg,png,webp,gif', $this->reglaTamanoImagen()],
        ];
    }

    /** Cierre de validación para el tamaño máximo de imagen (5 MB, GIFs 15 MB). */
    private function reglaTamanoImagen(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if (!$value) {
                return;
            }
            $maxKb = $value->getMimeType() === 'image/gif' ? 15360 : 5120;
            if ($value->getSize() / 1024 > $maxKb) {
                $fail('Las imágenes admiten hasta 5 MB (GIFs hasta 15 MB).');
            }
        };
    }

    /** Guarda los archivos de imagen subidos y los asocia a la publicación. */
    private function guardarImagenes(Request $request, Publicacion $publicacion, int $ordenBase = 0): void
    {
        if (!$request->hasFile('imagenes')) {
            return;
        }

        foreach ($request->file('imagenes') as $orden => $archivo) {
            $ruta = ImageService::store($archivo, 'publicaciones');
            Imagen::create([
                'publicacion_id' => $publicacion->id,
                'ruta'           => $ruta,
                'orden'          => $ordenBase + $orden,
            ]);
        }
    }
}
