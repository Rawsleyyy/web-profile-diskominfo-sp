<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    protected $fillable = [
        'name', 'path', 'disk', 'mime_type', 'size', 'alt_text', 'category', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    public function getUrlAttribute(): string
    {
        return url(Storage::disk($this->disk)->url($this->path));
    }
}
