<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_endpoint_returns_document_defaults_when_setting_is_empty(): void
    {
        $this->getJson('/api/theme')
            ->assertOk()
            ->assertJson([
                'primary_color_hex' => '#1E3A8A',
                'accent_color_hex' => '#DC2626',
            ]);
    }
}
