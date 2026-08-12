<?php

namespace App\Services;

use App\Models\HomepageSection;
use App\Models\MenuItem;
use App\Models\SiteModule;
use App\Models\ThemeSetting;
use Illuminate\Support\Facades\DB;

class InstitutionPresetService
{
    public static function presets(): array
    {
        return [
            'diskominfo' => ['label'=>'Diskominfo / Komunikasi','description'=>'Profil, PPID, SKM, media, layanan, artikel, berita.','theme'=>'government-blue','modules'=>['berita','articles','ppid','skm','layanan','struktur','podcast','awards','faq','profil']],
            'pendidikan' => ['label'=>'Dinas Pendidikan','description'=>'Fokus informasi, artikel, struktur, layanan, penghargaan, FAQ.','theme'=>'education','modules'=>['berita','articles','layanan','struktur','awards','faq','profil']],
            'pariwisata' => ['label'=>'Dinas Pariwisata','description'=>'Fokus berita, artikel, media, layanan, galeri/event melalui Custom Page.','theme'=>'solo-red','modules'=>['berita','articles','layanan','podcast','awards','faq','profil']],
            'kecamatan' => ['label'=>'Kecamatan / Kelurahan','description'=>'Profil, layanan, berita, PPID, SKM, struktur.','theme'=>'green-public','modules'=>['berita','ppid','skm','layanan','struktur','faq','profil']],
            'minimal' => ['label'=>'Template Minimal','description'=>'Hanya profil, berita, artikel, dan layanan inti.','theme'=>'minimal','modules'=>['berita','articles','layanan','profil']],
        ];
    }

    public function apply(string $key): void
    {
        $preset = self::presets()[$key] ?? null;
        if (!$preset) throw new \InvalidArgumentException('Preset tidak ditemukan.');
        DB::transaction(function () use ($preset) {
            SiteModule::query()->update(['is_enabled'=>false]);
            SiteModule::query()->whereIn('slug',$preset['modules'])->update(['is_enabled'=>true]);
            SiteModule::flushCache();

            $themePreset = ThemePresetService::get($preset['theme']);
            if ($themePreset) {
                $theme=ThemeSetting::query()->firstOrCreate([],['primary_color_hex'=>'#1E3A8A','accent_color_hex'=>'#DC2626']);
                $theme->fill($themePreset); $theme->preset_key=$preset['theme']; $theme->draft_config=$themePreset; $theme->updated_by=auth()->id(); $theme->save();
            }

            MenuItem::query()->delete();
            $order=10;
            MenuItem::create(['label'=>'Home','type'=>'route','url'=>'/','sort_order'=>$order,'target'=>'_self','is_active'=>true]); $order+=10;
            if (in_array('profil',$preset['modules'],true)) {
                $profil=MenuItem::create(['label'=>'Profil','type'=>'dropdown','sort_order'=>$order,'target'=>'_self','is_active'=>true]); $order+=10;
                foreach ([['Visi & Misi','/visi-misi'],['Struktur Organisasi','/struktur'],['Tupoksi','/tupoksi']] as $i=>[$label,$url]) MenuItem::create(['label'=>$label,'type'=>'route','url'=>$url,'parent_id'=>$profil->id,'sort_order'=>($i+1)*10,'target'=>'_self','is_active'=>true]);
            }
            foreach ([['Berita','berita'],['Artikel','articles'],['PPID','ppid'],['SKM','skm'],['Layanan','layanan']] as [$label,$slug]) {
                if(in_array($slug,$preset['modules'],true)) { MenuItem::create(['label'=>$label,'type'=>'module','module_slug'=>$slug,'sort_order'=>$order,'target'=>'_self','is_active'=>true]); $order+=10; }
            }

            HomepageSection::query()->update(['is_enabled'=>false]);
            $keys=['hero'];
            if(in_array('layanan',$preset['modules'],true))$keys[]='services';
            if(in_array('berita',$preset['modules'],true))$keys[]='news';
            if(in_array('struktur',$preset['modules'],true))$keys[]='structure';
            if(in_array('podcast',$preset['modules'],true))$keys[]='media';
            if(in_array('skm',$preset['modules'],true))$keys[]='skm';
            if(in_array('awards',$preset['modules'],true))$keys[]='awards';
            if(in_array('faq',$preset['modules'],true))$keys[]='help';
            foreach($keys as $index=>$k) HomepageSection::query()->where('section_key',$k)->update(['is_enabled'=>true,'sort_order'=>($index+1)*10]);
        });
    }
}
