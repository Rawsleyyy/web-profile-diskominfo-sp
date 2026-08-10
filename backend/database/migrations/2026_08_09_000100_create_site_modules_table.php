<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('public_route')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('site_modules')->insert([
            ['name' => 'Berita', 'slug' => 'berita', 'description' => 'Berita dan publikasi instansi.', 'public_route' => '/publikasi', 'is_enabled' => true, 'sort_order' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Artikel', 'slug' => 'articles', 'description' => 'Artikel dan informasi tematik.', 'public_route' => '/artikel', 'is_enabled' => true, 'sort_order' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PPID', 'slug' => 'ppid', 'description' => 'Informasi publik dan dokumen PPID.', 'public_route' => '/ppid', 'is_enabled' => true, 'sort_order' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SKM', 'slug' => 'skm', 'description' => 'Survei Kepuasan Masyarakat.', 'public_route' => '/skm', 'is_enabled' => true, 'sort_order' => 40, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Layanan', 'slug' => 'layanan', 'description' => 'Direktori layanan dan maklumat pelayanan.', 'public_route' => '/maklumat', 'is_enabled' => true, 'sort_order' => 50, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Struktur Organisasi', 'slug' => 'struktur', 'description' => 'Struktur organisasi dan pejabat.', 'public_route' => '/struktur', 'is_enabled' => true, 'sort_order' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Podcast / Media', 'slug' => 'podcast', 'description' => 'Podcast dan media instansi.', 'public_route' => null, 'is_enabled' => true, 'sort_order' => 70, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Penghargaan', 'slug' => 'awards', 'description' => 'Penghargaan dan prestasi instansi.', 'public_route' => null, 'is_enabled' => true, 'sort_order' => 80, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'FAQ / Pusat Bantuan', 'slug' => 'faq', 'description' => 'Bagian FAQ atau pusat bantuan pada halaman publik.', 'public_route' => null, 'is_enabled' => true, 'sort_order' => 90, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Profil Instansi', 'slug' => 'profil', 'description' => 'Visi misi, tupoksi, dan profil instansi.', 'public_route' => '/visi-misi', 'is_enabled' => true, 'sort_order' => 100, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_modules');
    }
};
