<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function buscar(Request $request): View
    {
        $q = trim($request->input('q', ''));

        $usuarios = $q
            ? User::where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orderBy('name')
                  ->take(30)
                  ->get()
            : collect();

        return view('usuarios.buscar', compact('usuarios', 'q'));
    }

    public function show(User $user): View
    {
        $user->load([
            'publicaciones.imagenes',
            'publicaciones.etiquetas',
            'publicaciones.likes',
            'publicaciones.repostes',
            'repostes.publicacion.imagenes',
            'repostes.publicacion.user',
            'repostes.publicacion.etiquetas',
            'repostes.publicacion.likes',
            'repostes.publicacion.repostes',
            'comentarios.publicacion',
            'publicacionesLiked.imagenes',
            'publicacionesLiked.user',
            'publicacionesLiked.likes',
            'publicacionesLiked.repostes',
        ]);

        $feed = $user->publicaciones
            ->map(fn($p) => ['tipo' => 'publicacion', 'fecha' => $p->created_at, 'item' => $p])
            ->concat(
                $user->repostes
                    ->filter(fn($r) => $r->publicacion !== null)
                    ->map(fn($r) => ['tipo' => 'reposte', 'fecha' => $r->created_at, 'item' => $r->publicacion])
            )
            ->sortByDesc('fecha')
            ->values();

        $isOwnProfile = Auth::check() && Auth::id() === $user->id;
        $esSeguido    = false;

        if (Auth::check() && !$isOwnProfile) {
            $esSeguido = Auth::user()->following()->whereKey($user->id)->exists();
        }

        $seguidores = $user->followers()->count();
        $seguidos   = $user->following()->count();

        return view('usuarios.show', compact(
            'user', 'feed', 'isOwnProfile', 'esSeguido', 'seguidores', 'seguidos'
        ));
    }
}
