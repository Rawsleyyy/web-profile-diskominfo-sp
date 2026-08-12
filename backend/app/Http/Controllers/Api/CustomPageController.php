<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CustomPageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $page = CustomPage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'excerpt' => $page->excerpt,
                'content' => $page->content,
                'banner_url' => $page->banner_path ? url(Storage::url($page->banner_path)) : null,
                'published_at' => optional($page->published_at)->toIso8601String(),
                'updated_at' => optional($page->updated_at)->toIso8601String(),
            ],
        ]);
    }
}
