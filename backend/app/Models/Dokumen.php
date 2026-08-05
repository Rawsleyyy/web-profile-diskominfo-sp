<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumens';

    // Kolom gabungan untuk data PPID & Data Publik
    protected $fillable = [
        'judul',
        'kategori',
        'file_path',
        'format',
        'ukuran_kb',
        'file_size',
        'is_active',
    ];

    /**
     * Accessor untuk mengubah ukuran KB ke MB secara otomatis jika file cukup besar
     */
    public function getUkuranFormattedAttribute(): string
    {
        $kb = $this->ukuran_kb;
        if (!$kb) {
            return $this->file_size ?? '-';
        }

        if ($kb >= 1024) {
            return round($kb / 1024, 1) . ' MB';
        }

        return $kb . ' KB';
    }

    protected $appends = ['ukuran_formatted'];
}
