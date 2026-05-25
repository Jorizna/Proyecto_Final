<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(): View
    {
        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->with(['actor', 'publicacion', 'tutorial'])
            ->latest()
            ->get();

        // Mark all unread as read — after loading so the view still sees the original state
        Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->update(['leida' => true]);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function markAllRead(): RedirectResponse
    {
        Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->update(['leida' => true]);

        return back();
    }
}
