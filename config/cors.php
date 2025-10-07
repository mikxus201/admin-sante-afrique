<?php

return [

    // 👉 En dev, on cible toutes les routes pour éviter un entonnoir de CORS.
    'paths' => ['*'],

    // IMPORTANT : avec credentials=true, on ne met pas "*"
    'allowed_origins' => (function () {
        $env = trim((string) env('CORS_ALLOWED_ORIGINS', ''));
        if ($env !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $env))));
        }
        return [
            'http://localhost:3000',
        ];
    })(),

    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],

    'exposed_headers' => [],
    'max_age' => 0,

    // Cookies cross-origin
    'supports_credentials' => true,
];
