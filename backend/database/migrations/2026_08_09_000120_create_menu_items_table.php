<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('type')->default('route'); // route, module, page, external, dropdown
            $table->string('url')->nullable();
            $table->string('module_slug')->nullable();
            $table->foreignId('page_id')->nullable()->constrained('custom_pages')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('target')->default('_self');
            $table->boolean('is_active')->default(true);
            $table->timestamp('visible_from')->nullable();
            $table->timestamp('visible_until')->nullable();
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
            $table->index(['is_active', 'visible_from', 'visible_until']);
        });

        $now = now();
        $homeId = DB::table('menu_items')->insertGetId([
            'label' => 'Home', 'type' => 'route', 'url' => '/', 'sort_order' => 10, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $ppidId = DB::table('menu_items')->insertGetId([
            'label' => 'PPID', 'type' => 'module', 'module_slug' => 'ppid', 'sort_order' => 20, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $ppidChildren = [
            ['Daftar Informasi Publik', '/ppid?tab=daftar'],
            ['Informasi Berkala', '/ppid?tab=berkala'],
            ['Informasi Setiap Saat', '/ppid?tab=setiap-saat'],
            ['Informasi Serta Merta', '/ppid?tab=serta-merta'],
            ['Informasi Dikecualikan', '/ppid?tab=dikecualikan'],
        ];
        foreach ($ppidChildren as $index => [$label, $url]) {
            DB::table('menu_items')->insert([
                'label' => $label, 'type' => 'route', 'url' => $url, 'parent_id' => $ppidId, 'sort_order' => ($index + 1) * 10, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $profilId = DB::table('menu_items')->insertGetId([
            'label' => 'Profil', 'type' => 'module', 'module_slug' => 'profil', 'sort_order' => 30, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);
        foreach ([['Visi & Misi', '/visi-misi'], ['Struktur Organisasi', '/struktur'], ['Tupoksi', '/tupoksi']] as $index => [$label, $url]) {
            DB::table('menu_items')->insert([
                'label' => $label, 'type' => 'route', 'url' => $url, 'parent_id' => $profilId, 'sort_order' => ($index + 1) * 10, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        DB::table('menu_items')->insert([
            ['label' => 'SKM', 'type' => 'module', 'module_slug' => 'skm', 'sort_order' => 40, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Informasi', 'type' => 'module', 'module_slug' => 'articles', 'sort_order' => 50, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $layananId = DB::table('menu_items')->insertGetId([
            'label' => 'Layanan', 'type' => 'module', 'module_slug' => 'layanan', 'sort_order' => 60, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('menu_items')->insert([
            ['label' => 'Maklumat Layanan', 'type' => 'route', 'url' => '/maklumat', 'parent_id' => $layananId, 'sort_order' => 10, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Standar Layanan', 'type' => 'route', 'url' => '/maklumat', 'parent_id' => $layananId, 'sort_order' => 20, 'target' => '_self', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Portal Solo', 'type' => 'external', 'url' => 'https://surakarta.go.id', 'parent_id' => $layananId, 'sort_order' => 30, 'target' => '_blank', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
