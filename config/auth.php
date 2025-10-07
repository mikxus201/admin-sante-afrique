<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard'     => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Guards utilisés par l’appli. "web" pour les utilisateurs du site,
    | "admin" pour le back-office (même provider par défaut, mais tu peux
    | le séparer en changeant ADMIN_AUTH_MODEL dans l’ENV).
    */
    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins', // peut être le même modèle que users
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => env('AUTH_MODEL', App\Models\User::class),
        ],

        // Provider pour le guard "admin" (par défaut, même modèle que users).
        'admins' => [
            'driver' => 'eloquent',
            'model'  => env('ADMIN_AUTH_MODEL', App\Models\User::class),
        ],

        // Alternative BDD (non utilisée ici)
        // 'users' => ['driver' => 'database', 'table' => 'users'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,
            'throttle' => 60,
        ],

        // Si tu veux un flux "mot de passe oublié" distinct pour les admins :
        // 'admins' => [
        //     'provider' => 'admins',
        //     'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
        //     'expire'   => 60,
        //     'throttle' => 60,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
