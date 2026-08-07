<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHeroSlideTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_endpoint_only_returns_active_slides_in_order(): void
    {
        HeroSlide::create([
            'title' => 'Slide Kedua',
            'image_path' => 'hero-slides/second.jpg',
            'urutan' => 2,
            'is_active' => true,
        ]);

        HeroSlide::create([
            'title' => 'Slide Nonaktif',
            'image_path' => 'hero-slides/inactive.jpg',
            'urutan' => 0,
            'is_active' => false,
        ]);

        HeroSlide::create([
            'title' => 'Slide Pertama',
            'image_path' => 'hero-slides/first.jpg',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/hero-slides')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.title', 'Slide Pertama')
            ->assertJsonPath('1.title', 'Slide Kedua')
            ->assertJsonMissing(['title' => 'Slide Nonaktif']);
    }
}
