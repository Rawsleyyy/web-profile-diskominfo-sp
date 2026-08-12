<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->string('secondary_color_hex', 7)->default('#0F172A')->after('primary_color_hex');
            $table->string('background_color_hex', 7)->default('#F8FAFC')->after('accent_color_hex');
            $table->string('surface_color_hex', 7)->default('#FFFFFF')->after('background_color_hex');
            $table->string('text_primary_hex', 7)->default('#0F172A')->after('surface_color_hex');
            $table->string('text_secondary_hex', 7)->default('#64748B')->after('text_primary_hex');
            $table->string('preset_key')->default('government-blue')->after('text_secondary_hex');
            $table->string('font_heading')->default('Inter')->after('preset_key');
            $table->string('font_body')->default('Inter')->after('font_heading');
            $table->string('radius_style')->default('rounded')->after('font_body');
            $table->string('button_style')->default('solid')->after('radius_style');
            $table->string('card_style')->default('soft')->after('button_style');
            $table->string('container_width')->default('1280')->after('card_style');
            $table->string('navbar_style')->default('solid')->after('container_width');
            $table->string('color_mode')->default('auto')->after('navbar_style');
            $table->json('draft_config')->nullable()->after('color_mode');
        });
    }

    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropColumn([
                'secondary_color_hex', 'background_color_hex', 'surface_color_hex',
                'text_primary_hex', 'text_secondary_hex', 'preset_key', 'font_heading',
                'font_body', 'radius_style', 'button_style', 'card_style', 'container_width',
                'navbar_style', 'color_mode', 'draft_config',
            ]);
        });
    }
};
