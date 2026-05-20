<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Publicacion $publicacion): RedirectResponse
    {
        $like = $publicacion->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
        } else {
            $publicacion->likes()->create(['user_id' => Auth::id()]);
        }

        return back();
    }
}
