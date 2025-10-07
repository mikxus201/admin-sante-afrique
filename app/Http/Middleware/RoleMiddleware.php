<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Utilisation:
     * ->middleware('role:admin,moderator') sur un groupe de routes
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (!in_array(strtolower($user->role), array_map('strtolower', $roles), true)) {
            abort(403);
        }

        return $next($request);
    }
}
