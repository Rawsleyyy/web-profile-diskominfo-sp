<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\SiteSetting;
use App\Models\ThemeSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class HeaderSettingsManager extends Component
{
    use WithFileUploads;

    public ?int $settingId = null;
    public $header_logo = null;
    public ?string $existingLogo = null;

    public string $header_style = 'theme_gradient';
    public string $header_width_mode = 'adaptive';
    public bool $header_show_site_name = true;
    public int $header_logo_height = 44;
    public bool $header_topbar_enabled = true;
    public string $header_topbar_color = '#1C2030';
    public bool $header_search_enabled = true;
    public bool $header_dark_toggle_enabled = true;
    public bool $header_glass_enabled = true;
    public bool $header_shadow_enabled = true;
    public string $header_custom_color_start = '#0B8A3B';
    public string $header_custom_color_end = '#46535B';
    public int $header_gradient_angle = 90;

    public string $site_short_name = 'Diskominfo SP';
    public string $themePrimary = '#1E3A8A';
    public string $themeAccent = '#DC2626';

    public function mount(): void
    {
        $theme = ThemeSetting::query()->first();
        if ($theme) {
            $this->themePrimary = strtoupper($theme->primary_color_hex ?: $this->themePrimary);
            $this->themeAccent = strtoupper($theme->accent_color_hex ?: $this->themeAccent);
        }

        $setting = SiteSetting::query()->first();
        if (! $setting) {
            return;
        }

        $this->settingId = $setting->id;
        $this->existingLogo = $setting->logo_path;
        $this->site_short_name = (string) ($setting->site_short_name ?: $this->site_short_name);

        $this->header_style = (string) ($setting->header_style ?: 'theme_gradient');
        $this->header_width_mode = (string) ($setting->header_width_mode ?: 'adaptive');
        $this->header_show_site_name = (bool) ($setting->header_show_site_name ?? true);
        $this->header_logo_height = (int) ($setting->header_logo_height ?: 44);
        $this->header_topbar_enabled = (bool) ($setting->header_topbar_enabled ?? true);
        $this->header_topbar_color = strtoupper((string) ($setting->header_topbar_color ?: '#1C2030'));
        $this->header_search_enabled = (bool) ($setting->header_search_enabled ?? true);
        $this->header_dark_toggle_enabled = (bool) ($setting->header_dark_toggle_enabled ?? true);
        $this->header_glass_enabled = (bool) ($setting->header_glass_enabled ?? true);
        $this->header_shadow_enabled = (bool) ($setting->header_shadow_enabled ?? true);
        $this->header_custom_color_start = strtoupper((string) ($setting->header_custom_color_start ?: '#0B8A3B'));
        $this->header_custom_color_end = strtoupper((string) ($setting->header_custom_color_end ?: '#46535B'));
        $this->header_gradient_angle = (int) ($setting->header_gradient_angle ?? 90);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'header_logo' => ['nullable', 'image', 'max:4096'],
            'header_style' => ['required', Rule::in(['theme_gradient', 'custom_gradient', 'solid'])],
            'header_width_mode' => ['required', Rule::in(['adaptive', 'full', 'boxed'])],
            'header_show_site_name' => ['boolean'],
            'header_logo_height' => ['required', 'integer', 'min:28', 'max:72'],
            'header_topbar_enabled' => ['boolean'],
            'header_topbar_color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'header_search_enabled' => ['boolean'],
            'header_dark_toggle_enabled' => ['boolean'],
            'header_glass_enabled' => ['boolean'],
            'header_shadow_enabled' => ['boolean'],
            'header_custom_color_start' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'header_custom_color_end' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'header_gradient_angle' => ['required', 'integer', 'min:0', 'max:360'],
        ]);

        $setting = SiteSetting::query()->firstOrNew(['id' => $this->settingId]);
        $logoPath = $setting->logo_path;

        if ($this->header_logo) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $this->header_logo->store('site', 'public');
        }

        unset($validated['header_logo']);

        foreach (['header_topbar_color', 'header_custom_color_start', 'header_custom_color_end'] as $colorField) {
            $validated[$colorField] = strtoupper($validated[$colorField]);
        }

        $setting->fill(array_merge($validated, [
            'logo_path' => $logoPath,
            'updated_by' => auth()->id(),
        ]));
        $setting->save();

        $this->settingId = $setting->id;
        $this->existingLogo = $logoPath;
        $this->header_logo = null;

        ActivityLogger::log(
            'Header Settings',
            'UPDATE',
            'success',
            auth()->id(),
            $setting->header_style.' / '.$setting->header_width_mode
        );

        session()->flash('header-settings-message', 'Pengaturan header berhasil disimpan. Muat ulang website publik untuk melihat perubahan.');
    }

    public function render()
    {
        return view('livewire.admin.header-settings-manager');
    }
}
