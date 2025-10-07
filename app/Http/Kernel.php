<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     * Exécutés pour TOUTES les requêtes.
     */
    protected $middleware = [
        // Sécurise l’hôte courant (optionnel mais recommandé)
        \App\Http\Middleware\TrustHosts::class,

        // Fait confiance aux proxies (utile en local/production derrière un proxy)
        \App\Http\Middleware\TrustProxies::class,

        // CORS : applique config/cors.php (doit être présent globalement)
        \Illuminate\Http\Middleware\HandleCors::class,

        // Middlewares “coeur” Laravel
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Groupes de middleware.
     */
    protected $middlewareGroups = [
        'web' => [
            // Cookies + Session + CSRF (indispensable pour Sanctum SPA)
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            /**
             * Sanctum (SPA): traite les requêtes venant des domaines "stateful"
             * (ex: http://127.0.0.1:3000) avec cookies + CSRF.
             * IMPORTANT : doit apparaître AVANT throttle/substitute.
             */
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

            // API légère : pas de session ici
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Aliases middleware (Laravel 10+).
     */
    protected $middlewareAliases = [
        'auth'             => \App\Http\Middleware\Authenticate::class,
        'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'              => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive'     => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed'           => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
