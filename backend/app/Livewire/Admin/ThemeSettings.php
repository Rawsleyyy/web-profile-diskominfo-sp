<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\ThemeSetting;
use Livewire\Component;

class ThemeSettings extends Component
{
    public string $primary_color_hex = '#1E3A8A';
    public string $accent_color_hex = '#DC2626';
    public ?int $themeId = null;

    public function mount(): void
    {
        $theme = ThemeSetting::query()->first();
        if ($theme) {
            $this->themeId = $theme->id;
            $this->primary_color_hex = strtoupper($theme->primary_color_hex);
            $this->accent_color_hex = strtoupper($theme->accent_color_hex);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'primary_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'accent_color_hex' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        $theme = ThemeSetting::updateOrCreate(
            ['id' => $this->themeId],
            [
                'primary_color_hex' => strtoupper($validated['primary_color_hex']),
                'accent_color_hex' => strtoupper($validated['accent_color_hex']),
                'updated_by' => auth()->id(),
            ]
        );

        $this->themeId = $theme->id;
        ActivityLogger::log('Theme Settings', 'UPDATE', 'success', auth()->id(), $theme->primary_color_hex.' / '.$theme->accent_color_hex);
        session()->flash('theme-saved', 'Warna situs berhasil disimpan. Muat ulang halaman publik untuk melihat perubahan.');
    }

    public function render()
    {
        return view('livewire.admin.theme-settings');
    }
}
