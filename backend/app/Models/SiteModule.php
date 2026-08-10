<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteModule extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'public_route', 'is_enabled', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static function enabledMap(): array
    {
        return Cache::remember('site_modules.enabled_map', 300, fn () =>
            self::query()->pluck('is_enabled', 'slug')->map(fn ($value) => (bool) $value)->all()
        );
    }

    public static function isEnabled(string $slug): bool
    {
        return self::enabledMap()[$slug] ?? false;
    }

    public static function flushCache(): void
    {
        Cache::forget('site_modules.enabled_map');
    }
}
