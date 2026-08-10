<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'label', 'type', 'url', 'module_slug', 'page_id', 'parent_id', 'sort_order',
        'target', 'is_active', 'visible_from', 'visible_until',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'visible_from' => 'datetime',
            'visible_until' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CustomPage::class, 'page_id');
    }

    public function scopeVisibleNow(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('visible_from')->orWhere('visible_from', '<=', $now))
            ->where(fn (Builder $q) => $q->whereNull('visible_until')->orWhere('visible_until', '>=', $now));
    }
}
