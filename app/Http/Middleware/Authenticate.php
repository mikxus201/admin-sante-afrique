<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * IMPORTANT : pour l'API, on NE redirige PAS ; on laisse Laravel renvoyer 401 JSON.
     */
    protected function redirectTo($request)
    {
        // Requêtes API / AJAX => pas de redirection HTML
        if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
            return null; // => 401 JSON
        }

        // Zone web/admin
        return route('admin.login');
    }
}
