<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            if (!Schema::hasColumn('podcasts', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('episode');
            }
            if (!Schema::hasColumn('podcasts', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('url_audio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'thumbnail']);
        });
    }
};
