<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
    protected $fillable = [
        'nama', 'jabatan', 'foto', 'urutan', 'tampil_utama', 'parent_id',
    ];

    protected $casts = [
        'tampil_utama' => 'boolean',
    ];

    // Atasan langsung pejabat ini (null = level teratas)
    public function parent()
    {
        return $this->belongsTo(Pejabat::class, 'parent_id');
    }

    // Bawahan langsung pejabat ini
    public function children()
    {
        return $this->hasMany(Pejabat::class, 'parent_id')->orderBy('urutan');
    }
}
