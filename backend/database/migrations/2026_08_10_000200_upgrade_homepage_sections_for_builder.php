<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->string('section_type', 40)->default('builtin')->after('label');
            $table->string('source_type', 40)->nullable()->after('module_slug');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            $table->string('layout', 40)->default('default')->after('source_id');
            $table->json('settings')->nullable()->after('layout');

            $table->index('section_type');
            $table->index(['source_type', 'source_id']);
        });

        DB::table('homepage_sections')->update([
            'section_type' => 'builtin',
            'layout' => 'default',
            'settings' => json_encode([], JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropIndex(['section_type']);
            $table->dropIndex(['source_type', 'source_id']);
            $table->dropColumn(['section_type', 'source_type', 'source_id', 'layout', 'settings']);
        });
    }
};
