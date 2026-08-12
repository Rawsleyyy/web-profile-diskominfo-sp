<?php

namespace App\Services;

use App\Models\CustomPage;
use App\Models\HomepageSection;
use App\Models\MenuItem;
use App\Models\SiteModule;
use App\Models\SiteSetting;
use App\Models\ThemeSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class SiteConfigBuilder
{
    public function build(): array
    {
        $settings = SiteSetting::query()->first();
        $theme = ThemeSetting::query()->first();
        $modules = SiteModule::query()->orderBy('sort_order')->get();
        $enabledMap = $modules->pluck('is_enabled', 'slug')->map(fn ($value) => (bool) $value)->all();

        return [
            'settings' => $this->settingsPayload($settings),
            'theme' => $this->themePayload($theme),
            'modules' => $modules->map(fn (SiteModule $module) => [
                'name' => $module->name,
                'slug' => $module->slug,
                'public_route' => $module->public_route,
                'is_enabled' => (bool) $module->is_enabled,
            ])->values()->all(),
            'navigation' => $this->navigationPayload($enabledMap),
            'homepage_sections' => $this->homepagePayload($enabledMap),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    private function navigationPayload(array $enabledModules): array
    {
        return MenuItem::query()
            ->with(['page:id,title,slug,is_published,published_at', 'children.page:id,title,slug,is_published,published_at'])
            ->whereNull('parent_id')
            ->visibleNow()
            ->orderBy('sort_order')->orderBy('id')
            ->get()
            ->map(fn (MenuItem $item) => $this->menuItemPayload($item, $enabledModules))
            ->filter()->values()->all();
    }

    private function menuItemPayload(MenuItem $item, array $enabledModules): ?array
    {
        if ($item->type === 'module' && ! ($enabledModules[$item->module_slug] ?? false)) return null;
        if ($item->type === 'page' && (! $item->page || ! $item->page->is_published || ($item->page->published_at && $item->page->published_at->isFuture()))) return null;

        $children = $item->children
            ->filter(function (MenuItem $child) use ($enabledModules) {
                if (! $child->is_active) return false;
                if ($child->visible_from && $child->visible_from->isFuture()) return false;
                if ($child->visible_until && $child->visible_until->isPast()) return false;
                if ($child->type === 'module' && ! ($enabledModules[$child->module_slug] ?? false)) return false;
                if ($child->type === 'page' && (! $child->page || ! $child->page->is_published || ($child->page->published_at && $child->page->published_at->isFuture()))) return false;
                return true;
            })
            ->map(fn (MenuItem $child) => $this->menuItemPayload($child, $enabledModules))
            ->filter()->values()->all();

        $url = match ($item->type) {
            'module' => SiteModule::query()->where('slug', $item->module_slug)->value('public_route') ?: '#',
            'page' => $item->page ? '/page/'.$item->page->slug : '#',
            'dropdown' => '#',
            default => $item->url ?: '#',
        };

        return [
            'id' => $item->id,
            'label' => $item->label,
            'type' => $item->type,
            'url' => $url,
            'module_slug' => $item->module_slug,
            'page_slug' => $item->page?->slug,
            'target' => $item->target,
            'is_external' => $item->type === 'external' || $item->target === '_blank',
            'children' => $children,
        ];
    }

    private function homepagePayload(array $enabledMap): array
    {
        $sections = HomepageSection::query()->where('is_enabled', true)->orderBy('sort_order')->get()
            ->filter(fn (HomepageSection $section) => ! $section->module_slug || ($enabledMap[$section->module_slug] ?? false))->values();

        $pageIds = $sections->where('section_type', HomepageSection::TYPE_PAGE_HIGHLIGHT)->pluck('source_id')->filter()->unique()->values();
        $pageMap = CustomPage::query()->whereIn('id', $pageIds)->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))->get()->keyBy('id');

        return $sections->map(fn (HomepageSection $section) => $this->homepageSectionPayload($section, $pageMap))->filter()->values()->all();
    }

    private function homepageSectionPayload(HomepageSection $section, Collection $pageMap): ?array
    {
        $settings = $section->settings ?? [];
        if ($section->section_type === HomepageSection::TYPE_PAGE_HIGHLIGHT) {
            $page = $pageMap->get($section->source_id);
            if (! $page) return null;
            return [
                'key' => $section->section_key, 'type' => HomepageSection::TYPE_PAGE_HIGHLIGHT, 'label' => $section->label,
                'layout' => $section->layout, 'sort_order' => $section->sort_order,
                'settings' => [
                    'title' => ($settings['title'] ?? null) ?: $page->title,
                    'subtitle' => ($settings['subtitle'] ?? null) ?: $page->excerpt,
                    'button_text' => ($settings['button_text'] ?? null) ?: 'Lihat Selengkapnya',
                ],
                'source_page_slug' => $page->slug,
                'page' => [
                    'id' => $page->id, 'title' => $page->title, 'slug' => $page->slug, 'excerpt' => $page->excerpt,
                    'banner_url' => $page->banner_path ? url(Storage::url($page->banner_path)) : null,
                    'url' => '/page/'.$page->slug,
                ],
            ];
        }

        if ($section->section_type === HomepageSection::TYPE_CUSTOM_CONTENT && ! empty($settings['image_path'])) {
            $settings['image_url'] = url(Storage::url($settings['image_path']));
        }

        return [
            'key' => $section->section_key, 'type' => $section->section_type ?: HomepageSection::TYPE_BUILTIN,
            'label' => $section->label, 'module_slug' => $section->module_slug,
            'layout' => $section->layout ?: 'default', 'sort_order' => $section->sort_order, 'settings' => $settings,
        ];
    }

    private function settingsPayload(?SiteSetting $settings): array
    {
        $storageUrl = fn (?string $path) => $path ? url(Storage::url($path)) : null;
        if (! $settings) {
            return [
                'site_name' => 'Diskominfo SP Kota Surakarta', 'site_short_name' => 'Diskominfo SP', 'site_description' => null,
                'tagline' => null, 'logo_url' => null, 'logo_footer_url' => null, 'logo_dark_url' => null, 'favicon_url' => null,
                'phone' => null, 'whatsapp' => null, 'email' => null, 'address' => null, 'service_hours' => null, 'maps_url' => null,
                'socials' => [], 'footer_text' => null,
                'seo' => ['title' => 'Diskominfo SP Kota Surakarta', 'description' => null, 'og_image_url' => null, 'canonical_url' => null, 'robots_index' => true],
            ];
        }

        return [
            'site_name' => $settings->site_name, 'site_short_name' => $settings->site_short_name,
            'site_description' => $settings->site_description, 'tagline' => $settings->tagline,
            'logo_url' => $storageUrl($settings->logo_path), 'logo_footer_url' => $storageUrl($settings->logo_footer_path),
            'logo_dark_url' => $storageUrl($settings->logo_dark_path), 'favicon_url' => $storageUrl($settings->favicon_path),
            'phone' => $settings->phone, 'whatsapp' => $settings->whatsapp, 'email' => $settings->email,
            'address' => $settings->address, 'service_hours' => $settings->service_hours, 'maps_url' => $settings->maps_url,
            'socials' => ['instagram' => $settings->instagram_url, 'facebook' => $settings->facebook_url, 'youtube' => $settings->youtube_url, 'tiktok' => $settings->tiktok_url],
            'footer_text' => $settings->footer_text,
            'seo' => [
                'title' => $settings->seo_title ?: $settings->site_name,
                'description' => $settings->meta_description ?: $settings->site_description,
                'og_image_url' => $storageUrl($settings->default_og_path ?: $settings->logo_path),
                'canonical_url' => $settings->canonical_url,
                'robots_index' => (bool) $settings->robots_index,
            ],
        ];
    }

    private function themePayload(?ThemeSetting $theme): array
    {
        return $theme?->publishedConfig() ?? [
            'primary_color_hex' => '#1E3A8A', 'secondary_color_hex' => '#0F172A', 'accent_color_hex' => '#DC2626',
            'background_color_hex' => '#F8FAFC', 'surface_color_hex' => '#FFFFFF', 'text_primary_hex' => '#0F172A', 'text_secondary_hex' => '#64748B',
            'preset_key' => 'government-blue', 'font_heading' => 'Inter', 'font_body' => 'Inter', 'radius_style' => 'rounded',
            'button_style' => 'solid', 'card_style' => 'soft', 'container_width' => '1280', 'navbar_style' => 'solid', 'color_mode' => 'auto',
        ];
    }
}
