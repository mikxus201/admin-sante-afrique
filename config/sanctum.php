<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains (SPA)
    |--------------------------------------------------------------------------
    | IMPORTANT :
    | - Définissez SANCTUM_STATEFUL_DOMAINS dans .env (séparés par des virgules)
    |   ex: SANCTUM_STATEFUL_DOMAINS=127.0.0.1:3000,localhost:3000
    | - Le fallback ci-dessous couvre les cas locaux les plus courants.
    */
    'stateful' => (function () {
        $env = trim((string) env('SANCTUM_STATEFUL_DOMAINS', ''));
        if ($env !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $env))));
        }
        // Fallback dev
        return [
            '127.0.0.1',
            'localhost',
            '127.0.0.1:3000',
            'localhost:3000',
        ];
    })(),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    | Le guard "web" permet l’auth via cookie de session pour le mode SPA.
    */
    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration
    |--------------------------------------------------------------------------
    | Null = session basée cookies (expire via session/ navigateur).
    */
    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    | Ces middlewares doivent correspondre à ceux du groupe "web" de Kernel.
    */
    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies'   => App\Http\Middleware\EncryptCookies::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Personal Access Tokens (optionnel si vous utilisez aussi les PAT)
    |--------------------------------------------------------------------------
    */
    'token_model' => Sanctum::$personalAccessTokenModel,
];
