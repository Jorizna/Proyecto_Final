<?php

use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ReposteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Inicio — mapa
Route::get('/', [PublicacionController::class, 'index'])->name('publicaciones.index');

// Perfiles públicos (cualquier usuario)
Route::get('/u/{user}', [UsuarioController::class, 'show'])->name('usuarios.show');

// Rutas protegidas — segmentos fijos ANTES de parámetros
Route::middleware('auth')->group(function () {

    // Follow / unfollow
    Route::post('/u/{user}/seguir', [FollowController::class, 'toggle'])->name('follows.toggle');

    // Perfil propio
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/contrasena', [PerfilController::class, 'updatePassword'])->name('perfil.password');

    // Publicaciones
    Route::get('/zonas/crear', [PublicacionController::class, 'create'])->name('publicaciones.create');
    Route::post('/zonas', [PublicacionController::class, 'store'])->name('publicaciones.store');
    Route::get('/zonas/{publicacion}/editar', [PublicacionController::class, 'edit'])->name('publicaciones.edit');
    Route::put('/zonas/{publicacion}', [PublicacionController::class, 'update'])->name('publicaciones.update');
    Route::delete('/zonas/{publicacion}', [PublicacionController::class, 'destroy'])->name('publicaciones.destroy');
    Route::delete('/zonas/{publicacion}/imagenes/{imagen}', [PublicacionController::class, 'destroyImagen'])->name('publicaciones.imagenes.destroy');

    // Interacciones
    Route::post('/zonas/{publicacion}/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::delete('/zonas/{publicacion}/comentarios/{comentario}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');
    Route::post('/zonas/{publicacion}/like', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::post('/zonas/{publicacion}/reposte', [ReposteController::class, 'toggle'])->name('repostes.toggle');
    Route::post('/zonas/{publicacion}/guardar', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
});

// Detalle de zona — parámetro al final
Route::get('/zonas/{publicacion}', [PublicacionController::class, 'show'])->name('publicaciones.show');

require __DIR__ . '/auth.php';
