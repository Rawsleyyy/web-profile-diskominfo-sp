<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'site_short_name', 'site_description', 'logo_path', 'favicon_path',
        'header_style', 'header_width_mode', 'header_show_site_name', 'header_logo_height',
        'header_topbar_enabled', 'header_topbar_color', 'header_search_enabled',
        'header_dark_toggle_enabled', 'header_glass_enabled', 'header_shadow_enabled',
        'header_custom_color_start', 'header_custom_color_end', 'header_gradient_angle',
        'phone', 'email', 'address', 'instagram_url', 'facebook_url', 'youtube_url',
        'tiktok_url', 'footer_text', 'announcement_enabled', 'announcement_text',
        'announcement_url', 'announcement_color', 'announcement_from', 'announcement_until',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'announcement_enabled' => 'boolean',
            'header_show_site_name' => 'boolean',
            'header_topbar_enabled' => 'boolean',
            'header_search_enabled' => 'boolean',
            'header_dark_toggle_enabled' => 'boolean',
            'header_glass_enabled' => 'boolean',
            'header_shadow_enabled' => 'boolean',
            'header_logo_height' => 'integer',
            'header_gradient_angle' => 'integer',
            'announcement_from' => 'datetime',
            'announcement_until' => 'datetime',
        ];
    }

    public function announcementIsVisible(): bool
    {
        if (! $this->announcement_enabled || blank($this->announcement_text)) {
            return false;
        }

        $now = now();

        if ($this->announcement_from && $this->announcement_from->isAfter($now)) {
            return false;
        }

        if ($this->announcement_until && $this->announcement_until->isBefore($now)) {
            return false;
        }

        return true;
    }
}
