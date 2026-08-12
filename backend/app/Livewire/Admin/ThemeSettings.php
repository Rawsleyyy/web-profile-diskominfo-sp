<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\ThemeSetting;
use App\Services\ThemePresetService;
use Livewire\Component;

class ThemeSettings extends Component
{
    public ?int $themeId = null;
    public string $preset_key = 'government-blue';
    public string $primary_color_hex = '#1E3A8A';
    public string $secondary_color_hex = '#0F172A';
    public string $accent_color_hex = '#DC2626';
    public string $background_color_hex = '#F8FAFC';
    public string $surface_color_hex = '#FFFFFF';
    public string $text_primary_hex = '#0F172A';
    public string $text_secondary_hex = '#64748B';
    public string $font_heading = 'Inter';
    public string $font_body = 'Inter';
    public string $radius_style = 'rounded';
    public string $button_style = 'solid';
    public string $card_style = 'soft';
    public string $container_width = '1280';
    public string $navbar_style = 'solid';
    public string $color_mode = 'auto';

    public function mount(): void
    {
        $theme = ThemeSetting::query()->first();
        if (! $theme) return;
        $this->themeId = $theme->id;
        $config = $theme->draft_config ?: $theme->publishedConfig();
        $this->fillFromConfig($config);
    }

    public function applyPreset(string $key): void
    {
        $preset = ThemePresetService::get($key);
        if (! $preset) return;
        $this->preset_key = $key;
        $this->fillFromConfig($preset);
    }

    public function saveDraft(): void
    {
        $config = $this->validatedConfig();
        $theme = ThemeSetting::query()->firstOrCreate([], [
            'primary_color_hex' => '#1E3A8A', 'accent_color_hex' => '#DC2626',
        ]);
        $theme->update(['draft_config' => $config, 'updated_by' => auth()->id()]);
        $this->themeId = $theme->id;
        ActivityLogger::log('Theme Builder', 'UPDATE', 'success', auth()->id(), 'Draft tema disimpan');
        session()->flash('theme-saved', 'Draft tema berhasil disimpan. Gunakan Preview Website untuk memeriksa hasilnya.');
    }

    public function applyToDraftSite(): void
    {
        $config = $this->validatedConfig();
        $theme = ThemeSetting::query()->firstOrCreate([], [
            'primary_color_hex' => '#1E3A8A', 'accent_color_hex' => '#DC2626',
        ]);
        $theme->fill($config);
        $theme->draft_config = $config;
        $theme->updated_by = auth()->id();
        $theme->save();
        $this->themeId = $theme->id;
        ActivityLogger::log('Theme Builder', 'UPDATE', 'success', auth()->id(), 'Tema diterapkan ke draft website');
        session()->flash('theme-saved', 'Tema diterapkan ke draft website. Publik belum berubah setelah website memiliki versi publik; publish dari menu Publikasi Website.');
    }

    private function validatedConfig(): array
    {
        return $this->validate([
            'preset_key' => ['required', 'string', 'max:60'],
            'primary_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'secondary_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'accent_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'background_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'surface_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'text_primary_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'text_secondary_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'font_heading' => ['required', 'in:Inter,Poppins,Arial,Georgia'],
            'font_body' => ['required', 'in:Inter,Poppins,Arial,Georgia'],
            'radius_style' => ['required', 'in:square,small,rounded,large,pill'],
            'button_style' => ['required', 'in:solid,outline,soft'],
            'card_style' => ['required', 'in:flat,soft,bordered'],
            'container_width' => ['required', 'in:1100,1200,1280,1400'],
            'navbar_style' => ['required', 'in:solid,gradient,minimal'],
            'color_mode' => ['required', 'in:light,dark,auto'],
        ]);
    }

    private function fillFromConfig(array $config): void
    {
        foreach (array_keys($this->validatedDefaults()) as $field) {
            if (array_key_exists($field, $config) && $config[$field] !== null) $this->{$field} = (string) $config[$field];
        }
    }

    private function validatedDefaults(): array
    {
        return [
            'preset_key' => null, 'primary_color_hex' => null, 'secondary_color_hex' => null, 'accent_color_hex' => null,
            'background_color_hex' => null, 'surface_color_hex' => null, 'text_primary_hex' => null, 'text_secondary_hex' => null,
            'font_heading' => null, 'font_body' => null, 'radius_style' => null, 'button_style' => null, 'card_style' => null,
            'container_width' => null, 'navbar_style' => null, 'color_mode' => null,
        ];
    }

    public function render()
    {
        return view('livewire.admin.theme-settings', ['presets' => ThemePresetService::presets()]);
    }
}
