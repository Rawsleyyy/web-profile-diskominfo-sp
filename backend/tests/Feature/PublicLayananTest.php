<?php

namespace Tests\Feature;

use App\Models\Layanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLayananTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_endpoint_only_returns_active_services_in_order(): void
    {
        Layanan::create(['nama_layanan' => 'Kedua', 'url_eksternal' => 'https://example.com/2', 'urutan' => 2, 'is_active' => true]);
        Layanan::create(['nama_layanan' => 'Nonaktif', 'url_eksternal' => 'https://example.com/off', 'urutan' => 0, 'is_active' => false]);
        Layanan::create(['nama_layanan' => 'Pertama', 'url_eksternal' => 'https://example.com/1', 'urutan' => 1, 'is_active' => true]);

        $this->getJson('/api/layanan')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.nama_layanan', 'Pertama')
            ->assertJsonPath('1.nama_layanan', 'Kedua');
    }
}
