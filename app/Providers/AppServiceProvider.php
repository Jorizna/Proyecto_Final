<?php

namespace App\Providers;

use App\Models\Publicacion;
use App\Policies\PublicacionPolicy;
use App\Services\AemetService;
use App\Services\OverpassService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AemetService::class);
        $this->app->singleton(OverpassService::class);
    }

    public function boot(): void
    {
        Gate::policy(Publicacion::class, PublicacionPolicy::class);
    }
}
