<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\JsonResponse;

class HeroSlideController extends Controller
{
    public function index(): JsonResponse
    {
        $slides = HeroSlide::query()
            ->where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->map(fn (HeroSlide $slide) => [
                'id' => $slide->id,
                'title' => $slide->title,
                'subtitle' => $slide->subtitle,
                'image_path' => $slide->image_path,
                'image_url' => asset('storage/'.$slide->image_path),
                'alt_text' => $slide->alt_text ?: $slide->title,
                'button_label' => $slide->button_label,
                'button_url' => $slide->button_url,
                'urutan' => $slide->urutan,
            ]);

        return response()->json($slides);
    }
}
