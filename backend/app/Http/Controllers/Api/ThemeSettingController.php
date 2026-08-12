<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThemeSettingController extends Controller
{
    private const DEFAULT_PRIMARY = '#1E3A8A';
    private const DEFAULT_ACCENT = '#DC2626';

    public function index(): JsonResponse
    {
        $theme = ThemeSetting::latest('id')->first();

        return response()->json($theme ?? [
            'primary_color_hex' => self::DEFAULT_PRIMARY,
            'accent_color_hex' => self::DEFAULT_ACCENT,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'primary_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'accent_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        $validated['primary_color_hex'] = strtoupper($validated['primary_color_hex']);
        $validated['accent_color_hex'] = strtoupper($validated['accent_color_hex']);
        $validated['updated_by'] = $request->user()->id;

        $theme = ThemeSetting::query()->first();
        if ($theme) {
            $theme->update($validated);
        } else {
            $theme = ThemeSetting::create($validated);
        }

        ActivityLogger::log(
            'Theme Settings',
            'UPDATE',
            'success',
            $request->user()->id,
            $theme->primary_color_hex.' / '.$theme->accent_color_hex
        );

        return response()->json([
            'message' => 'Tema berhasil diperbarui.',
            'data' => $theme->fresh(),
        ]);
    }
}
