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
        $page = CustomPage::query()->where('slug', $slug)->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))->firstOrFail();

        $url = fn (?string $path) => $path ? url(Storage::url($path)) : null;
        return response()->json(['data' => [
            'id' => $page->id, 'title' => $page->title, 'slug' => $page->slug, 'excerpt' => $page->excerpt,
            'content' => $page->content, 'banner_url' => $url($page->banner_path),
            'published_at' => optional($page->published_at)->toIso8601String(), 'updated_at' => optional($page->updated_at)->toIso8601String(),
            'seo' => [
                'title' => $page->seo_title ?: $page->title,
                'description' => $page->seo_description ?: $page->excerpt,
                'og_image_url' => $url($page->seo_og_path ?: $page->banner_path),
                'robots_index' => (bool) $page->robots_index,
            ],
        ]]);
    }
}
