<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $fillable = [
        'judul', 'konten', 'thumbnail', 'kategori_id', 'author_id', 'status_publish', 'views'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
