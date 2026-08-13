<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use App\Models\HomepageSection;
use App\Models\SiteModule;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class SiteConfigController extends Controller
{
    public function index(NavigationController $navigationController): JsonResponse
    {
        $settings = SiteSetting::query()->first();
        $modules = SiteModule::query()->orderBy('sort_order')->get();
        $enabledMap = $modules->pluck('is_enabled', 'slug')->map(fn ($value) => (bool) $value);

        $sections = HomepageSection::query()
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (HomepageSection $section) => ! $section->module_slug || ($enabledMap[$section->module_slug] ?? false))
            ->values();

        $pageIds = $sections
            ->where('section_type', HomepageSection::TYPE_PAGE_HIGHLIGHT)
            ->pluck('source_id')
            ->filter()
            ->unique()
            ->values();

        $pageMap = CustomPage::query()
            ->whereIn('id', $pageIds)
            ->where('is_published', true)
            ->where(function ($query) {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->get()
            ->keyBy('id');

        $homepageSections = $sections
            ->map(fn (HomepageSection $section) => $this->homepageSectionPayload($section, $pageMap))
            ->filter()
            ->values();

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

    private function homepageSectionPayload(HomepageSection $section, Collection $pageMap): ?array
    {
        $settings = $section->settings ?? [];

        if ($section->section_type === HomepageSection::TYPE_PAGE_HIGHLIGHT) {
            $page = $pageMap->get($section->source_id);

            if (! $page) {
                return null;
            }

            return [
                'key' => $section->section_key,
                'type' => HomepageSection::TYPE_PAGE_HIGHLIGHT,
                'label' => $section->label,
                'layout' => $section->layout,
                'sort_order' => $section->sort_order,
                'settings' => [
                    'title' => ($settings['title'] ?? null) ?: $page->title,
                    'subtitle' => ($settings['subtitle'] ?? null) ?: $page->excerpt,
                    'button_text' => ($settings['button_text'] ?? null) ?: 'Lihat Selengkapnya',
                ],
                'page' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'excerpt' => $page->excerpt,
                    'banner_url' => $page->banner_path ? url(Storage::url($page->banner_path)) : null,
                    'url' => '/page/'.$page->slug,
                ],
            ];
        }

        if ($section->section_type === HomepageSection::TYPE_CUSTOM_CONTENT && ! empty($settings['image_path'])) {
            $settings['image_url'] = url(Storage::url($settings['image_path']));
        }

        return [
            'key' => $section->section_key,
            'type' => $section->section_type ?: HomepageSection::TYPE_BUILTIN,
            'label' => $section->label,
            'module_slug' => $section->module_slug,
            'layout' => $section->layout ?: 'default',
            'sort_order' => $section->sort_order,
            'settings' => $settings,
        ];
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
                'header' => [
                    'style' => 'theme_gradient',
                    'width_mode' => 'adaptive',
                    'show_site_name' => true,
                    'logo_height' => 44,
                    'topbar_enabled' => true,
                    'topbar_color' => '#1C2030',
                    'search_enabled' => true,
                    'dark_toggle_enabled' => true,
                    'glass_enabled' => true,
                    'shadow_enabled' => true,
                    'custom_color_start' => '#0B8A3B',
                    'custom_color_end' => '#46535B',
                    'gradient_angle' => 90,
                ],
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
            'header' => [
                'style' => $settings->header_style ?: 'theme_gradient',
                'width_mode' => $settings->header_width_mode ?: 'adaptive',
                'show_site_name' => (bool) ($settings->header_show_site_name ?? true),
                'logo_height' => (int) ($settings->header_logo_height ?: 44),
                'topbar_enabled' => (bool) ($settings->header_topbar_enabled ?? true),
                'topbar_color' => $settings->header_topbar_color ?: '#1C2030',
                'search_enabled' => (bool) ($settings->header_search_enabled ?? true),
                'dark_toggle_enabled' => (bool) ($settings->header_dark_toggle_enabled ?? true),
                'glass_enabled' => (bool) ($settings->header_glass_enabled ?? true),
                'shadow_enabled' => (bool) ($settings->header_shadow_enabled ?? true),
                'custom_color_start' => $settings->header_custom_color_start ?: '#0B8A3B',
                'custom_color_end' => $settings->header_custom_color_end ?: '#46535B',
                'gradient_angle' => (int) ($settings->header_gradient_angle ?? 90),
            ],
        ];
    }
}
