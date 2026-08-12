<?php

namespace App\Http\Middleware;

use App\Services\PreviewTokenService;
use App\Services\PublishedSiteConfig;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteModuleEnabled
{
    public function handle(Request $request, Closure $next, string $slug): Response
    {
        $draft = PreviewTokenService::valid($request->query('preview_token'));
        if (! app(PublishedSiteConfig::class)->moduleEnabled($slug, $draft)) {
            return response()->json(['message' => 'Modul ini sedang tidak tersedia.'], 404);
        }
        return $next($request);
    }
}
