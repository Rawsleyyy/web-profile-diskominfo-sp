<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pejabat;

class PejabatController extends Controller
{
    // GET /api/pejabat — publik, dipakai homepage (tampil_utama) & halaman struktur lengkap
    public function index()
    {
        $pejabat = Pejabat::orderBy('urutan')->orderBy('id')->get();
        return response()->json($pejabat);
    }
}
