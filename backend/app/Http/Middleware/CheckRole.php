<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        $userRole = $user?->role?->name;

        if (! $user || $userRole !== $role) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Akses ditolak. Fitur ini hanya tersedia untuk '.$role.'.',
                ], 403);
            }

            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
