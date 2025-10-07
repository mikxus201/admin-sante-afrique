<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Enregistrer les services de l’application.
     */
    public function register(): void
    {
        //
    }

    /**
     * Démarrer les services de l’application.
     */
    public function boot(): void
    {
        // Utiliser le style Bootstrap pour la pagination
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
    }
}
