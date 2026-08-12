<?php

namespace App\Services;

class ThemePresetService
{
    public static function presets(): array
    {
        return [
            'government-blue' => [
                'label' => 'Pemerintahan Biru',
                'primary_color_hex' => '#1E3A8A', 'secondary_color_hex' => '#0F172A', 'accent_color_hex' => '#DC2626',
                'background_color_hex' => '#F8FAFC', 'surface_color_hex' => '#FFFFFF', 'text_primary_hex' => '#0F172A', 'text_secondary_hex' => '#64748B',
                'font_heading' => 'Inter', 'font_body' => 'Inter', 'radius_style' => 'rounded', 'button_style' => 'solid', 'card_style' => 'soft', 'container_width' => '1280', 'navbar_style' => 'solid', 'color_mode' => 'auto',
            ],
            'solo-red' => [
                'label' => 'Merah Surakarta',
                'primary_color_hex' => '#B91C1C', 'secondary_color_hex' => '#3F0D12', 'accent_color_hex' => '#F59E0B',
                'background_color_hex' => '#FFF7ED', 'surface_color_hex' => '#FFFFFF', 'text_primary_hex' => '#1F2937', 'text_secondary_hex' => '#6B7280',
                'font_heading' => 'Inter', 'font_body' => 'Inter', 'radius_style' => 'rounded', 'button_style' => 'solid', 'card_style' => 'bordered', 'container_width' => '1280', 'navbar_style' => 'gradient', 'color_mode' => 'light',
            ],
            'education' => [
                'label' => 'Pendidikan Modern',
                'primary_color_hex' => '#1D4ED8', 'secondary_color_hex' => '#172554', 'accent_color_hex' => '#F59E0B',
                'background_color_hex' => '#F8FAFC', 'surface_color_hex' => '#FFFFFF', 'text_primary_hex' => '#0F172A', 'text_secondary_hex' => '#475569',
                'font_heading' => 'Poppins', 'font_body' => 'Inter', 'radius_style' => 'large', 'button_style' => 'soft', 'card_style' => 'soft', 'container_width' => '1200', 'navbar_style' => 'solid', 'color_mode' => 'auto',
            ],
            'green-public' => [
                'label' => 'Hijau Layanan Publik',
                'primary_color_hex' => '#047857', 'secondary_color_hex' => '#064E3B', 'accent_color_hex' => '#F59E0B',
                'background_color_hex' => '#F0FDF4', 'surface_color_hex' => '#FFFFFF', 'text_primary_hex' => '#052E16', 'text_secondary_hex' => '#4B5563',
                'font_heading' => 'Inter', 'font_body' => 'Inter', 'radius_style' => 'large', 'button_style' => 'solid', 'card_style' => 'bordered', 'container_width' => '1280', 'navbar_style' => 'solid', 'color_mode' => 'light',
            ],
            'minimal' => [
                'label' => 'Minimal Modern',
                'primary_color_hex' => '#111827', 'secondary_color_hex' => '#030712', 'accent_color_hex' => '#2563EB',
                'background_color_hex' => '#FFFFFF', 'surface_color_hex' => '#F9FAFB', 'text_primary_hex' => '#111827', 'text_secondary_hex' => '#6B7280',
                'font_heading' => 'Inter', 'font_body' => 'Inter', 'radius_style' => 'small', 'button_style' => 'outline', 'card_style' => 'flat', 'container_width' => '1200', 'navbar_style' => 'minimal', 'color_mode' => 'auto',
            ],
        ];
    }

    public static function get(string $key): ?array
    {
        return self::presets()[$key] ?? null;
    }
}
