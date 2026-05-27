<?php

use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReposteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Política de privacidad y términos de uso (pública, sin auth)
Route::view('/privacidad', 'legal.privacidad')->name('privacidad');

// Inicio — mapa (gated: returns splash for guests)
Route::get('/', [PublicacionController::class, 'index'])->name('publicaciones.index');

// Rutas protegidas — segmentos fijos ANTES de parámetros
Route::middleware('auth')->group(function () {

    // Buscador
    Route::get('/buscar', [PublicacionController::class, 'buscar'])->name('buscar');

    // Buscador de usuarios
    Route::get('/u', [UsuarioController::class, 'buscar'])->name('usuarios.buscar');

    // Perfiles públicos (requieren auth)
    Route::get('/u/{user}', [UsuarioController::class, 'show'])->name('usuarios.show');

    // Follow / unfollow
    Route::post('/u/{user}/seguir', [FollowController::class, 'toggle'])->name('follows.toggle');

    // Perfil propio
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/contrasena', [PerfilController::class, 'updatePassword'])->name('perfil.password');

    // Publicaciones — estáticos ANTES del parámetro
    Route::get('/zonas/crear', [PublicacionController::class, 'create'])->name('publicaciones.create');
    Route::post('/zonas', [PublicacionController::class, 'store'])->name('publicaciones.store');
    Route::get('/zonas/{publicacion}/editar', [PublicacionController::class, 'edit'])->name('publicaciones.edit');
    Route::put('/zonas/{publicacion}', [PublicacionController::class, 'update'])->name('publicaciones.update');
    Route::delete('/zonas/{publicacion}', [PublicacionController::class, 'destroy'])->name('publicaciones.destroy');
    Route::delete('/zonas/{publicacion}/imagenes/{imagen}', [PublicacionController::class, 'destroyImagen'])->name('publicaciones.imagenes.destroy');

    // Detalle de zona
    Route::get('/zonas/{publicacion}', [PublicacionController::class, 'show'])->name('publicaciones.show');

    // Interacciones
    Route::post('/zonas/{publicacion}/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::delete('/zonas/{publicacion}/comentarios/{comentario}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');
    Route::post('/zonas/{publicacion}/like', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::post('/zonas/{publicacion}/reposte', [ReposteController::class, 'toggle'])->name('repostes.toggle');
    Route::post('/zonas/{publicacion}/guardar', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
    Route::get('/guardados', [FavoritoController::class, 'index'])->name('guardados.index');

    // Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/leer', [NotificacionController::class, 'markAllRead'])->name('notificaciones.read');

    // Equipamiento y Guías — hub + UGC tutorials
    Route::get('/guias', [TutorialController::class, 'index'])->name('guias.index');
    Route::get('/guias/nuevo', [TutorialController::class, 'create'])->name('tutoriales.create');
    Route::post('/guias/nuevo', [TutorialController::class, 'store'])->name('tutoriales.store');
    Route::get('/guias/{tutorial}', [TutorialController::class, 'show'])->name('tutoriales.show');
    Route::delete('/guias/{tutorial}', [TutorialController::class, 'destroy'])->name('tutoriales.destroy');
});

require __DIR__ . '/auth.php';
