<?php

namespace App\Services;

use App\Models\CustomPage;
use App\Models\HomepageSection;
use App\Models\MenuItem;
use App\Models\SiteModule;
use App\Models\SiteSetting;
use App\Models\ThemeSetting;
use Illuminate\Support\Facades\DB;

class SiteConfigImporter
{
    public function apply(array $payload): void
    {
        DB::transaction(function () use ($payload) {
            if (isset($payload['theme']) && is_array($payload['theme'])) {
                $allowed = (new ThemeSetting())->getFillable();
                $theme = ThemeSetting::query()->firstOrCreate([], ['primary_color_hex'=>'#1E3A8A','accent_color_hex'=>'#DC2626']);
                $theme->fill(array_intersect_key($payload['theme'], array_flip($allowed)));
                $theme->draft_config = $payload['theme'];
                $theme->updated_by = auth()->id();
                $theme->save();
            }

            if (isset($payload['settings']) && is_array($payload['settings'])) {
                $s = $payload['settings'];
                $setting = SiteSetting::query()->firstOrNew();
                $setting->fill([
                    'site_name'=>$s['site_name'] ?? $setting->site_name ?? 'Website Instansi',
                    'site_short_name'=>$s['site_short_name'] ?? $setting->site_short_name ?? 'Instansi',
                    'site_description'=>$s['site_description'] ?? null,'tagline'=>$s['tagline'] ?? null,
                    'phone'=>$s['phone'] ?? null,'whatsapp'=>$s['whatsapp'] ?? null,'email'=>$s['email'] ?? null,'address'=>$s['address'] ?? null,
                    'service_hours'=>$s['service_hours'] ?? null,'maps_url'=>$s['maps_url'] ?? null,'footer_text'=>$s['footer_text'] ?? null,
                    'instagram_url'=>$s['socials']['instagram'] ?? null,'facebook_url'=>$s['socials']['facebook'] ?? null,'youtube_url'=>$s['socials']['youtube'] ?? null,'tiktok_url'=>$s['socials']['tiktok'] ?? null,
                    'seo_title'=>$s['seo']['title'] ?? null,'meta_description'=>$s['seo']['description'] ?? null,'canonical_url'=>$s['seo']['canonical_url'] ?? null,'robots_index'=>$s['seo']['robots_index'] ?? true,
                    'updated_by'=>auth()->id(),
                ]);
                $setting->save();
            }

            foreach ($payload['modules'] ?? [] as $module) {
                if (!isset($module['slug'])) continue;
                SiteModule::query()->where('slug',$module['slug'])->update(['is_enabled'=>(bool)($module['is_enabled'] ?? false)]);
            }
            SiteModule::flushCache();

            if (isset($payload['navigation']) && is_array($payload['navigation'])) {
                MenuItem::query()->delete();
                $this->insertNavigation($payload['navigation'], null);
            }

            if (isset($payload['homepage_sections']) && is_array($payload['homepage_sections'])) {
                HomepageSection::query()->delete();
                foreach ($payload['homepage_sections'] as $index=>$section) {
                    $sectionType = $section['type'] ?? HomepageSection::TYPE_BUILTIN;
                    $sourceId = $sectionType === HomepageSection::TYPE_PAGE_HIGHLIGHT && !empty($section['source_page_slug'])
                        ? CustomPage::query()->where('slug', $section['source_page_slug'])->value('id')
                        : null;
                    HomepageSection::create([
                        'section_key'=>$section['key'] ?? 'imported-'.($index+1),
                        'label'=>$section['label'] ?? ($section['key'] ?? 'Section'),
                        'section_type'=>$sectionType,
                        'module_slug'=>$section['module_slug'] ?? null,
                        'source_type'=>$sourceId ? 'custom_page' : null,'source_id'=>$sourceId,'layout'=>$section['layout'] ?? 'default',
                        'settings'=>$section['settings'] ?? [],'is_enabled'=>true,'sort_order'=>($index+1)*10,
                    ]);
                }
            }
        });
    }

    private function insertNavigation(array $items, ?int $parentId): void
    {
        foreach ($items as $index=>$item) {
            $type = in_array($item['type'] ?? 'route', ['route','module','page','external','dropdown'], true) ? $item['type'] : 'route';
            $url = $item['url'] ?? null;
            $pageId = $type === 'page' && !empty($item['page_slug']) ? CustomPage::query()->where('slug', $item['page_slug'])->value('id') : null;
            if ($type === 'page' && ! $pageId) { $type = 'route'; }
            $record = MenuItem::create([
                'label'=>$item['label'] ?? 'Menu','type'=>$type,
                'url'=>in_array($type,['route','external'],true)?$url:null,
                'module_slug'=>$type==='module'?($item['module_slug'] ?? null):null,
                'page_id'=>$pageId,'parent_id'=>$parentId,'sort_order'=>($index+1)*10,
                'target'=>$item['target'] ?? '_self','is_active'=>true,
            ]);
            if (!empty($item['children']) && is_array($item['children'])) $this->insertNavigation($item['children'],$record->id);
        }
    }
}
