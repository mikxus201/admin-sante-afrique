<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'pay/cinetpay/start',   // ← on autorise ce POST sans CSRF
        'pay/cinetpay/notify',  // ← CinetPay webhook (déjà sans CSRF)
    ];
}
