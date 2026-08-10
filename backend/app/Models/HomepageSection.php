<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    public const TYPE_BUILTIN = 'builtin';
    public const TYPE_CUSTOM_CONTENT = 'custom_content';
    public const TYPE_PAGE_HIGHLIGHT = 'page_highlight';
    public const TYPE_CTA = 'cta';
    public const TYPE_VIDEO = 'video';
    public const TYPE_SPACER = 'spacer';

    protected $fillable = [
        'section_key',
        'label',
        'section_type',
        'module_slug',
        'source_type',
        'source_id',
        'layout',
        'settings',
        'is_enabled',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'settings' => 'array',
            'is_enabled' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function isBuiltin(): bool
    {
        return $this->section_type === self::TYPE_BUILTIN;
    }
}
