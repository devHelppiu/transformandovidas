<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        if (! $user->is_active) {
            abort(403, 'Tu cuenta ha sido desactivada.');
        }

        return $next($request);
    }
}
