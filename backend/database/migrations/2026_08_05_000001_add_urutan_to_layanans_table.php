<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('layanans', 'urutan')) {
            Schema::table('layanans', function (Blueprint $table) {
                $table->unsignedSmallInteger('urutan')->default(0)->after('url_eksternal');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('layanans', 'urutan')) {
            Schema::table('layanans', function (Blueprint $table) {
                $table->dropColumn('urutan');
            });
        }
    }
};
