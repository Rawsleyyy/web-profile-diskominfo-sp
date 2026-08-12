<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use App\Services\PreviewTokenService;
use App\Services\PublishedSiteConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThemeSettingController extends Controller
{
    public function index(Request $request, PublishedSiteConfig $config): JsonResponse
    {
        $draft = PreviewTokenService::valid($request->query('preview_token'));
        $payload = $config->payload($draft);
        return response()->json(['data' => $payload['theme'] ?? []]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'primary_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'accent_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        $theme = ThemeSetting::query()->firstOrCreate([], ['primary_color_hex' => '#1E3A8A', 'accent_color_hex' => '#DC2626']);
        $theme->update([
            'primary_color_hex' => strtoupper($validated['primary_color_hex']),
            'accent_color_hex' => strtoupper($validated['accent_color_hex']),
            'updated_by' => $request->user()->id,
        ]);

        ActivityLogger::log('Theme Settings', 'UPDATE', 'success', $request->user()->id, 'API update');
        return response()->json(['message' => 'Tema berhasil diperbarui.', 'data' => $theme->fresh()->publishedConfig()]);
    }
}
