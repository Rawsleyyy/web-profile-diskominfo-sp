<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Award;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function index()
    {
        $awards = Award::latest()->get()->map(function ($item) {
            // Ambil isi field dari DB terlepas dari nama kolomnya
            $title = $item->nama_penghargaan ?? $item->title ?? $item->nama ?? $item->name ?? '';
            $year  = $item->tahun ?? $item->year ?? '';
            $desc  = $item->deskripsi ?? $item->description ?? '';
            $img   = $item->gambar ?? $item->image ?? null;
            $imgUrl = $img ? asset('storage/' . $img) : null;

            return [
                'id' => $item->id,

                // Field versi Bahasa Indonesia
                'nama_penghargaan' => $title,
                'tahun'            => $year,
                'deskripsi'        => $desc,
                'gambar'           => $imgUrl,

                // Field versi Bahasa Inggris / Umum (Sering dipakai template React/Vue)
                'title'            => $title,
                'name'             => $title,
                'year'             => $year,
                'description'      => $desc,
                'image'            => $imgUrl,
                'image_url'        => $imgUrl,
            ];
        });

        return response()->json($awards);
    }
}
