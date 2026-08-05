<?php

namespace App\Http\Middleware;

use App\Helpers\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    private const AUDITED_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Trafik GET publik tidak boleh memenuhi tabel audit administrator.
        if (! in_array($request->method(), self::AUDITED_METHODS, true)) {
            return $response;
        }

        $user = $request->user();
        if (! $user) {
            return $response;
        }

        // Login/logout sudah memiliki pesan audit yang lebih spesifik di controller.
        if ($request->routeIs('api.login', 'api.logout')) {
            return $response;
        }

        ActivityLogger::log(
            subject: $request->route()?->getName() ?? $request->path(),
            method: $request->method(),
            status: $response->getStatusCode() < 400 ? 'success' : 'failed',
            userId: $user->id,
            description: sprintf('%s %s', $request->method(), $request->path())
        );

        return $response;
    }
}
