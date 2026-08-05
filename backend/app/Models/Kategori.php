<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = ['nama_kategori', 'is_publish'];

    public function beritas()
    {
        return $this->hasMany(Berita::class);
    }
}
