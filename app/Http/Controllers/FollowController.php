<?php

namespace App\Http\Controllers;

use App\Jobs\EnviarNotificacion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(User $user): RedirectResponse
    {
        $auth = Auth::user();

        if ($auth->id === $user->id) {
            return back();
        }

        $isFollowing = $auth->following()->whereKey($user->id)->exists();

        if ($isFollowing) {
            $auth->following()->detach($user->id);
        } else {
            $auth->following()->attach($user->id);
            EnviarNotificacion::dispatch($user->id, $auth->id, 'seguir');
        }

        return back();
    }
}
