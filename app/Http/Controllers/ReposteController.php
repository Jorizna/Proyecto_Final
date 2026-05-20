<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReposteController extends Controller
{
    public function toggle(Publicacion $publicacion): RedirectResponse
    {
        $reposte = $publicacion->repostes()->where('user_id', Auth::id())->first();

        if ($reposte) {
            $reposte->delete();
        } else {
            $publicacion->repostes()->create(['user_id' => Auth::id()]);
        }

        return back();
    }
}
