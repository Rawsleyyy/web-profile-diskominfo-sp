<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\MenuItem;
use App\Models\SiteModule;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SiteConfigController extends Controller
{
    public function index(NavigationController $navigationController): JsonResponse
    {
        $settings = SiteSetting::query()->first();
        $modules = SiteModule::query()->orderBy('sort_order')->get();
        $enabledMap = $modules->pluck('is_enabled', 'slug')->map(fn ($value) => (bool) $value);

        $homepageSections = HomepageSection::query()
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (HomepageSection $section) => ! $section->module_slug || ($enabledMap[$section->module_slug] ?? false))
            ->values()
            ->map(fn (HomepageSection $section) => [
                'key' => $section->section_key,
                'label' => $section->label,
                'module_slug' => $section->module_slug,
                'sort_order' => $section->sort_order,
            ]);

        $navigationPayload = $navigationController->index()->getData(true)['data'] ?? [];

        return response()->json([
            'data' => [
                'settings' => $this->settingsPayload($settings),
                'modules' => $modules->map(fn (SiteModule $module) => [
                    'name' => $module->name,
                    'slug' => $module->slug,
                    'public_route' => $module->public_route,
                    'is_enabled' => $module->is_enabled,
                ])->values(),
                'navigation' => $navigationPayload,
                'homepage_sections' => $homepageSections,
            ],
        ]);
    }

    private function settingsPayload(?SiteSetting $settings): array
    {
        if (! $settings) {
            return [
                'site_name' => 'Diskominfo SP Kota Surakarta',
                'site_short_name' => 'Diskominfo SP',
                'site_description' => null,
                'logo_url' => null,
                'favicon_url' => null,
                'phone' => null,
                'email' => null,
                'address' => null,
                'socials' => [],
                'footer_text' => null,
                'announcement' => null,
            ];
        }

        return [
            'site_name' => $settings->site_name,
            'site_short_name' => $settings->site_short_name,
            'site_description' => $settings->site_description,
            'logo_url' => $settings->logo_path ? url(Storage::url($settings->logo_path)) : null,
            'favicon_url' => $settings->favicon_path ? url(Storage::url($settings->favicon_path)) : null,
            'phone' => $settings->phone,
            'email' => $settings->email,
            'address' => $settings->address,
            'socials' => [
                'instagram' => $settings->instagram_url,
                'facebook' => $settings->facebook_url,
                'youtube' => $settings->youtube_url,
                'tiktok' => $settings->tiktok_url,
            ],
            'footer_text' => $settings->footer_text,
            'announcement' => $settings->announcementIsVisible() ? [
                'text' => $settings->announcement_text,
                'url' => $settings->announcement_url,
                'color' => $settings->announcement_color,
            ] : null,
        ];
    }
}
