<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
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
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $instagram_url = '';
    public string $facebook_url = '';
    public string $youtube_url = '';
    public string $tiktok_url = '';
    public string $footer_text = '';
    public $logo = null;
    public $favicon = null;
    public ?string $existingLogo = null;
    public ?string $existingFavicon = null;

    public function mount(): void
    {
        $setting = SiteSetting::query()->first();
        if (! $setting) {
            return;
        }

        $this->settingId = $setting->id;

        foreach ([
            'site_name', 'site_short_name', 'site_description', 'phone', 'email', 'address',
            'instagram_url', 'facebook_url', 'youtube_url', 'tiktok_url', 'footer_text',
        ] as $field) {
            $this->{$field} = (string) ($setting->{$field} ?? '');
        }

        $this->existingLogo = $setting->logo_path;
        $this->existingFavicon = $setting->favicon_path;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_short_name' => ['required', 'string', 'max:100'],
            'site_description' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'instagram_url' => ['nullable', 'url', 'max:1000'],
            'facebook_url' => ['nullable', 'url', 'max:1000'],
            'youtube_url' => ['nullable', 'url', 'max:1000'],
            'tiktok_url' => ['nullable', 'url', 'max:1000'],
            'footer_text' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'max:3072'],
            'favicon' => ['nullable', 'image', 'max:1024'],
        ]);

        $setting = SiteSetting::firstOrNew(['id' => $this->settingId]);
        $logoPath = $setting->logo_path;
        $faviconPath = $setting->favicon_path;

        if ($this->logo) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $this->logo->store('site', 'public');
        }

        if ($this->favicon) {
            if ($faviconPath) {
                Storage::disk('public')->delete($faviconPath);
            }
            $faviconPath = $this->favicon->store('site', 'public');
        }

        $payload = $validated;
        unset($payload['logo'], $payload['favicon']);

        $setting->fill(array_merge($payload, [
            'logo_path' => $logoPath,
            'favicon_path' => $faviconPath,
            'updated_by' => auth()->id(),
        ]));
        $setting->save();

        $this->settingId = $setting->id;
        $this->existingLogo = $logoPath;
        $this->existingFavicon = $faviconPath;
        $this->logo = null;
        $this->favicon = null;

        ActivityLogger::log('Site Settings', 'UPDATE', 'success', auth()->id(), $setting->site_name);
        session()->flash('site-settings-message', 'Identitas website berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings-manager');
    }
}
