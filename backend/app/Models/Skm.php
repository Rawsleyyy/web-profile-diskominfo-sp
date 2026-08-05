<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skm extends Model
{
    use HasFactory;

    // Masukkan semua nama kolom agar diizinkan untuk disimpan (Mass Assignment)
    protected $fillable = [
        'jenis_layanan_id',
        'nama',
        'no_whatsapp',
        'usia',
        'jenis_kelamin',
        'pendidikan',
        'pekerjaan',
        'kecamatan',
        'kelurahan',
        'jawaban_1',
        'jawaban_2',
        'jawaban_3',
        'jawaban_4',
        'jawaban_5',
        'jawaban_6',
        'jawaban_7',
        'jawaban_8',
        'jawaban_9',
        'saran',
    ];

    /**
     * Relasi ke layanan yang dinilai pada survei ini.
     */
    public function jenisLayanan()
    {
        return $this->belongsTo(Layanan::class, 'jenis_layanan_id');
    }
}
