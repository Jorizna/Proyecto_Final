<?php

namespace App\Http\Controllers;

use App\Jobs\EnviarNotificacion;
use App\Models\Tutorial;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TutorialController extends Controller
{
    public function index(): View
    {
        $tutoriales = Tutorial::with('user')->latest()->get();
        return view('guias.index', compact('tutoriales'));
    }

    public function create(): View
    {
        return view('guias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'titulo'    => 'required|string|max:160',
            'categoria' => 'required|in:tecnica,equipo,entorno',
            'contenido' => 'required|string|max:10000',
            'imagen'    => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);

        $data = $request->only('titulo', 'categoria', 'contenido');

        if ($request->hasFile('imagen')) {
            $data['imagen_cabecera'] = ImageService::store($request->file('imagen'), 'tutoriales', maxWidth: 1200);
        }

        $tutorial = $request->user()->tutoriales()->create($data);

        // Notify followers that a new tutorial was published
        foreach ($request->user()->followers as $follower) {
            EnviarNotificacion::dispatch($follower->id, $request->user()->id, 'tutorial', null, $tutorial->id);
        }

        return redirect()->route('guias.index')
            ->with('success', '¡Tutorial publicado! Ya está visible para toda la comunidad.');
    }

    public function show(Tutorial $tutorial): View
    {
        return view('guias.show', compact('tutorial'));
    }

    public function destroy(Tutorial $tutorial): RedirectResponse
    {
        abort_if(auth()->id() !== $tutorial->user_id, 403);

        if ($tutorial->imagen_cabecera) {
            Storage::disk('public')->delete($tutorial->imagen_cabecera);
        }

        $tutorial->delete();

        return redirect()->route('guias.index')
            ->with('success', 'Tutorial eliminado correctamente.');
    }
}
