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
        $user->cargarRelacionesPerfil();
        $feed = $user->feedCombinado();

        $isOwnProfile = Auth::check() && Auth::id() === $user->id;
        $esSeguido    = !$isOwnProfile && Auth::check()
            && Auth::user()->following()->whereKey($user->id)->exists();

        $seguidores = $user->followers()->count();
        $seguidos   = $user->following()->count();

        return view('usuarios.show', compact(
            'user', 'feed', 'isOwnProfile', 'esSeguido', 'seguidores', 'seguidos'
        ));
    }
}
