<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // In cPanel, public_html is outside project root
        if (app()->environment('production')) {
            $this->app->usePublicPath(base_path('../public_html'));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Forzar HTTPS en producción (FIX 56)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Morph map para polimorfismo de comisiones (FIX 27)
        Relation::enforceMorphMap([
            'Comercial' => \App\Models\Comercial::class,
            'Lider' => \App\Models\Lider::class,
            'Coordinador' => \App\Models\Coordinador::class,
            'User' => \App\Models\User::class,
        ]);
    }
}
