<?php

namespace App\Providers;

use App\Models\Comentario;
use App\Models\Publicacion;
use App\Policies\ComentarioPolicy;
use App\Policies\PublicacionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Publicacion::class, PublicacionPolicy::class);
        Gate::policy(Comentario::class, ComentarioPolicy::class);
    }
}
