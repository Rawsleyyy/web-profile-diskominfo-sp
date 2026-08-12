<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('tagline')->nullable()->after('site_description');
            $table->string('logo_footer_path')->nullable()->after('logo_path');
            $table->string('logo_dark_path')->nullable()->after('logo_footer_path');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('service_hours')->nullable()->after('address');
            $table->text('maps_url')->nullable()->after('service_hours');
            $table->string('seo_title')->nullable()->after('footer_text');
            $table->text('meta_description')->nullable()->after('seo_title');
            $table->string('default_og_path')->nullable()->after('meta_description');
            $table->string('canonical_url')->nullable()->after('default_og_path');
            $table->boolean('robots_index')->default(true)->after('canonical_url');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'tagline', 'logo_footer_path', 'logo_dark_path', 'whatsapp', 'service_hours',
                'maps_url', 'seo_title', 'meta_description', 'default_og_path',
                'canonical_url', 'robots_index',
            ]);
        });
    }
};
