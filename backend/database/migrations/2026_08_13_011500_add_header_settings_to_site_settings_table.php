<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('header_style', 32)->default('theme_gradient');
            $table->string('header_width_mode', 20)->default('adaptive');
            $table->boolean('header_show_site_name')->default(true);
            $table->unsignedSmallInteger('header_logo_height')->default(44);
            $table->boolean('header_topbar_enabled')->default(true);
            $table->string('header_topbar_color', 7)->default('#1C2030');
            $table->boolean('header_search_enabled')->default(true);
            $table->boolean('header_dark_toggle_enabled')->default(true);
            $table->boolean('header_glass_enabled')->default(true);
            $table->boolean('header_shadow_enabled')->default(true);
            $table->string('header_custom_color_start', 7)->default('#0B8A3B');
            $table->string('header_custom_color_end', 7)->default('#46535B');
            $table->unsignedSmallInteger('header_gradient_angle')->default(90);
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'header_style',
                'header_width_mode',
                'header_show_site_name',
                'header_logo_height',
                'header_topbar_enabled',
                'header_topbar_color',
                'header_search_enabled',
                'header_dark_toggle_enabled',
                'header_glass_enabled',
                'header_shadow_enabled',
                'header_custom_color_start',
                'header_custom_color_end',
                'header_gradient_angle',
            ]);
        });
    }
};
