<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class SiteSettingsManager extends Component
{
    use WithFileUploads;

    public ?int $settingId = null;
    public string $site_name = 'Diskominfo SP Kota Surakarta';
    public string $site_short_name = 'Diskominfo SP';
    public string $site_description = '';
    public string $tagline = '';
    public string $phone = '';
    public string $whatsapp = '';
    public string $email = '';
    public string $address = '';
    public string $service_hours = '';
    public string $maps_url = '';
    public string $instagram_url = '';
    public string $facebook_url = '';
    public string $youtube_url = '';
    public string $tiktok_url = '';
    public string $footer_text = '';
    public string $seo_title = '';
    public string $meta_description = '';
    public string $canonical_url = '';
    public bool $robots_index = true;

    public $logo = null, $logo_footer = null, $logo_dark = null, $favicon = null, $default_og = null;
    public ?string $existingLogo = null, $existingLogoFooter = null, $existingLogoDark = null, $existingFavicon = null, $existingOg = null;
    public string|int $logo_media_id = '', $logo_footer_media_id = '', $logo_dark_media_id = '', $default_og_media_id = '';

    public function mount(): void
    {
        $setting = SiteSetting::query()->first();
        if (! $setting) return;
        $this->settingId = $setting->id;
        foreach ([
            'site_name','site_short_name','site_description','tagline','phone','whatsapp','email','address','service_hours','maps_url',
            'instagram_url','facebook_url','youtube_url','tiktok_url','footer_text','seo_title','meta_description','canonical_url'
        ] as $field) $this->{$field} = (string) ($setting->{$field} ?? '');
        $this->robots_index = (bool) $setting->robots_index;
        $this->existingLogo = $setting->logo_path; $this->existingLogoFooter = $setting->logo_footer_path;
        $this->existingLogoDark = $setting->logo_dark_path; $this->existingFavicon = $setting->favicon_path; $this->existingOg = $setting->default_og_path;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'site_name'=>['required','string','max:255'],'site_short_name'=>['required','string','max:100'],'site_description'=>['nullable','string','max:2000'],'tagline'=>['nullable','string','max:255'],
            'phone'=>['nullable','string','max:100'],'whatsapp'=>['nullable','string','max:100'],'email'=>['nullable','email','max:255'],'address'=>['nullable','string','max:2000'],'service_hours'=>['nullable','string','max:255'],'maps_url'=>['nullable','string','max:2000'],
            'instagram_url'=>['nullable','url','max:1000'],'facebook_url'=>['nullable','url','max:1000'],'youtube_url'=>['nullable','url','max:1000'],'tiktok_url'=>['nullable','url','max:1000'],'footer_text'=>['nullable','string','max:1000'],
            'seo_title'=>['nullable','string','max:255'],'meta_description'=>['nullable','string','max:500'],'canonical_url'=>['nullable','url','max:1000'],'robots_index'=>['boolean'],
            'logo'=>['nullable','image','max:3072'],'logo_footer'=>['nullable','image','max:3072'],'logo_dark'=>['nullable','image','max:3072'],'favicon'=>['nullable','image','max:1024'],'default_og'=>['nullable','image','max:4096'],
            'logo_media_id'=>['nullable','exists:media_assets,id'],'logo_footer_media_id'=>['nullable','exists:media_assets,id'],'logo_dark_media_id'=>['nullable','exists:media_assets,id'],'default_og_media_id'=>['nullable','exists:media_assets,id'],
        ]);

        $setting = SiteSetting::firstOrNew(['id' => $this->settingId]);
        $paths = [
            'logo_path' => $setting->logo_path, 'logo_footer_path' => $setting->logo_footer_path,
            'logo_dark_path' => $setting->logo_dark_path, 'favicon_path' => $setting->favicon_path, 'default_og_path' => $setting->default_og_path,
        ];
        $uploads = ['logo'=>'logo_path','logo_footer'=>'logo_footer_path','logo_dark'=>'logo_dark_path','favicon'=>'favicon_path','default_og'=>'default_og_path'];
        foreach ($uploads as $property=>$column) {
            if ($this->{$property}) {
                $this->deleteIfOwned($paths[$column]);
                $paths[$column] = $this->{$property}->store('site', 'public');
            }
        }
        foreach (['logo_media_id'=>'logo_path','logo_footer_media_id'=>'logo_footer_path','logo_dark_media_id'=>'logo_dark_path','default_og_media_id'=>'default_og_path'] as $selector=>$column) {
            if (! empty($validated[$selector])) $paths[$column] = MediaAsset::find($validated[$selector])?->path ?: $paths[$column];
        }

        $payload = $validated;
        foreach (array_merge(array_keys($uploads), ['logo_media_id','logo_footer_media_id','logo_dark_media_id','default_og_media_id']) as $remove) unset($payload[$remove]);
        $setting->fill(array_merge($payload, $paths, ['updated_by'=>auth()->id()]));
        $setting->save();
        $this->settingId = $setting->id;
        $this->existingLogo=$paths['logo_path']; $this->existingLogoFooter=$paths['logo_footer_path']; $this->existingLogoDark=$paths['logo_dark_path']; $this->existingFavicon=$paths['favicon_path']; $this->existingOg=$paths['default_og_path'];
        $this->reset(['logo','logo_footer','logo_dark','favicon','default_og','logo_media_id','logo_footer_media_id','logo_dark_media_id','default_og_media_id']);
        ActivityLogger::log('Site Settings','UPDATE','success',auth()->id(),$setting->site_name);
        session()->flash('site-settings-message','Identitas dan SEO website berhasil disimpan sebagai draft konfigurasi.');
    }

    private function deleteIfOwned(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'media-library/')) Storage::disk('public')->delete($path);
    }

    public function render()
    {
        return view('livewire.admin.site-settings-manager', [
            'mediaImages' => MediaAsset::query()->where('mime_type','like','image/%')->latest()->limit(100)->get(),
        ]);
    }
}
