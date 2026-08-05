<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    // GET /api/articles — publik, hanya artikel yang tanggal terbitnya sudah lewat/hari ini
    public function index()
    {
        $articles = Article::whereDate('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->get();

        return response()->json($articles);
    }

    // GET /api/articles/{id} — publik
    public function show($id)
    {
        $article = Article::whereDate('published_at', '<=', now())
            ->findOrFail($id);

        return response()->json($article);
    }
}