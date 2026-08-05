<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    use HasFactory;

    protected $table = 'podcasts';

    protected $fillable = [
        'judul',
        'episode',
        'deskripsi',
        'url_audio',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Cek apakah url_audio berupa link eksternal atau file upload lokal.
     */
    public function isLink(): bool
    {
        return str_starts_with($this->url_audio ?? '', 'http');
    }
}
