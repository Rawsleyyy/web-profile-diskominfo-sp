<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_pages', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('published_at');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_og_path')->nullable()->after('seo_description');
            $table->boolean('robots_index')->default(true)->after('seo_og_path');
        });
    }

    public function down(): void
    {
        Schema::table('custom_pages', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description', 'seo_og_path', 'robots_index']);
        });
    }
};
