<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('label');
            $table->string('module_slug')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('homepage_sections')->insert([
            ['section_key' => 'hero', 'label' => 'Hero / Banner Utama', 'module_slug' => null, 'is_enabled' => true, 'sort_order' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['section_key' => 'services', 'label' => 'Layanan Cepat', 'module_slug' => 'layanan', 'is_enabled' => true, 'sort_order' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['section_key' => 'news', 'label' => 'Berita', 'module_slug' => 'berita', 'is_enabled' => true, 'sort_order' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['section_key' => 'structure', 'label' => 'Struktur Organisasi', 'module_slug' => 'struktur', 'is_enabled' => true, 'sort_order' => 40, 'created_at' => $now, 'updated_at' => $now],
            ['section_key' => 'media', 'label' => 'Media / Podcast', 'module_slug' => 'podcast', 'is_enabled' => true, 'sort_order' => 50, 'created_at' => $now, 'updated_at' => $now],
            ['section_key' => 'skm', 'label' => 'Survei Kepuasan Masyarakat', 'module_slug' => 'skm', 'is_enabled' => true, 'sort_order' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['section_key' => 'awards', 'label' => 'Penghargaan / Prestasi', 'module_slug' => 'awards', 'is_enabled' => true, 'sort_order' => 70, 'created_at' => $now, 'updated_at' => $now],
            ['section_key' => 'help', 'label' => 'FAQ / Pusat Bantuan', 'module_slug' => 'faq', 'is_enabled' => true, 'sort_order' => 80, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
