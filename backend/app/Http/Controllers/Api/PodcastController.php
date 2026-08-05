<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Podcast;

class PodcastController extends Controller
{
    // GET /api/podcast — publik, dipakai widget KOMINPOD di homepage
    public function index()
    {
        $podcast = Podcast::where('is_active', true)->latest()->get();
        return response()->json($podcast);
    }
}
