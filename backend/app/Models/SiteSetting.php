<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'site_short_name', 'site_description', 'tagline', 'logo_path', 'logo_footer_path', 'logo_dark_path', 'favicon_path',
        'phone', 'whatsapp', 'email', 'address', 'service_hours', 'maps_url',
        'instagram_url', 'facebook_url', 'youtube_url', 'tiktok_url', 'footer_text',
        'seo_title', 'meta_description', 'default_og_path', 'canonical_url', 'robots_index',
        'announcement_enabled', 'announcement_text', 'announcement_url', 'announcement_color', 'announcement_from', 'announcement_until',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean', 'announcement_enabled' => 'boolean',
            'announcement_from' => 'datetime', 'announcement_until' => 'datetime',
        ];
    }

    public function announcementIsVisible(): bool
    {
        if (! $this->announcement_enabled || blank($this->announcement_text)) return false;
        $now = now();
        if ($this->announcement_from && $this->announcement_from->isAfter($now)) return false;
        if ($this->announcement_until && $this->announcement_until->isBefore($now)) return false;
        return true;
    }
}
