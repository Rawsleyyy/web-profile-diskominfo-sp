<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PreviewTokenService;
use App\Services\PublishedSiteConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteConfigController extends Controller
{
    public function index(Request $request, PublishedSiteConfig $config): JsonResponse
    {
        $draft = PreviewTokenService::valid($request->query('preview_token'));
        return response()->json(['data' => $config->payload($draft)]);
    }
}
