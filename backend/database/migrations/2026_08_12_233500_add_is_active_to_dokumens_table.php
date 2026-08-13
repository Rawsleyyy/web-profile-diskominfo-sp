<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('dokumens') || Schema::hasColumn('dokumens', 'is_active')) {
            return;
        }

        Schema::table('dokumens', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('ukuran_kb')->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('dokumens') || ! Schema::hasColumn('dokumens', 'is_active')) {
            return;
        }

        Schema::table('dokumens', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
