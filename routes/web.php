<?php

use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ValoracionController;
use Illuminate\Support\Facades\Route;

// Página de inicio — mapa con todas las publicaciones
Route::get('/', [PublicacionController::class, 'index'])->name('publicaciones.index');

// Publicaciones — listado y detalle públicos, CRUD protegido
Route::get('/zonas/{publicacion}', [PublicacionController::class, 'show'])->name('publicaciones.show');

Route::middleware('auth')->group(function () {
    Route::get('/zonas/crear', [PublicacionController::class, 'create'])->name('publicaciones.create');
    Route::post('/zonas', [PublicacionController::class, 'store'])->name('publicaciones.store');
    Route::get('/zonas/{publicacion}/editar', [PublicacionController::class, 'edit'])->name('publicaciones.edit');
    Route::put('/zonas/{publicacion}', [PublicacionController::class, 'update'])->name('publicaciones.update');
    Route::delete('/zonas/{publicacion}', [PublicacionController::class, 'destroy'])->name('publicaciones.destroy');
    Route::delete('/zonas/{publicacion}/imagenes/{imagen}', [PublicacionController::class, 'destroyImagen'])->name('publicaciones.imagenes.destroy');

    // Comentarios
    Route::post('/zonas/{publicacion}/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::delete('/zonas/{publicacion}/comentarios/{comentario}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');

    // Valoraciones
    Route::post('/zonas/{publicacion}/valorar', [ValoracionController::class, 'store'])->name('valoraciones.store');

    // Favoritos
    Route::post('/zonas/{publicacion}/favorito', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');

    // Perfil
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/contrasena', [PerfilController::class, 'updatePassword'])->name('perfil.password');
});

// Rutas de autenticación (Breeze)
require __DIR__ . '/auth.php';
