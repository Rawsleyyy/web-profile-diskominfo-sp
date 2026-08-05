<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;

class DokumenController extends Controller
{
    private const KATEGORI_PPID = [
        'Informasi Berkala',
        'Informasi Setiap Saat',
        'Informasi Serta Merta',
        'Informasi Dikecualikan',
    ];

    private const KATEGORI_PUBLIK = [
        'Rilis Data',
        'LKJIP',
        'Statistik',
    ];

    // GET /api/dokumen — publik, dipakai halaman PPID (hanya 4 kategori resmi PPID)
    public function index()
    {
        $dokumen = Dokumen::where('is_active', true)->whereIn('kategori', self::KATEGORI_PPID)->latest()->get();
        return response()->json($dokumen);
    }

    // GET /api/dokumen-publik — publik, dipakai section "Dokumen & Data Publik" di homepage
    public function indexPublik()
    {
        $dokumen = Dokumen::where('is_active', true)->whereIn('kategori', self::KATEGORI_PUBLIK)->latest()->get();
        return response()->json($dokumen);
    }
}
