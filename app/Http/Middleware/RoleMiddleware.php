<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $userRole = $request->user()->role ?? 'user';

        if ($role === 'admin' && $userRole !== 'admin') {
            return response()->json([
                'message' => 'Forbidden.',
            ], 403);
        }

        if ($role === 'user' && ! in_array($userRole, ['user', 'admin'], true)) {
            return response()->json([
                'message' => 'Forbidden.',
            ], 403);
        }

        return $next($request);
    }
}
