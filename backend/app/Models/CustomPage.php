<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomPage extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'banner_path', 'is_published', 'published_at',
        'seo_title', 'seo_description', 'seo_og_path', 'robots_index', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'published_at' => 'datetime', 'robots_index' => 'boolean'];
    }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function menuItems(): HasMany { return $this->hasMany(MenuItem::class, 'page_id'); }
}
