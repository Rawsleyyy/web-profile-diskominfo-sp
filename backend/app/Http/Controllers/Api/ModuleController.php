<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteModule;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => SiteModule::query()
                ->orderBy('sort_order')
                ->get(['name', 'slug', 'public_route', 'is_enabled']),
        ]);
    }
}
