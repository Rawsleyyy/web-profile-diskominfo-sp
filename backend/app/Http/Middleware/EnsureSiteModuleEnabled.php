<?php

namespace App\Http\Middleware;

use App\Models\SiteModule;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteModuleEnabled
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (Schema::hasTable('site_modules') && ! SiteModule::isEnabled($module)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Modul ini sedang dinonaktifkan.'], 404);
            }
            abort(404);
        }

        return $next($request);
    }
}
