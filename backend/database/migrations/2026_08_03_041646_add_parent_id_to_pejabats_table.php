<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pejabats', function (Blueprint $table) {
            // Menunjuk ke pejabat lain di tabel yang sama = "siapa atasannya".
            // null = level teratas (tidak punya atasan).
            // nullOnDelete: kalau atasannya dihapus, bawahan TIDAK ikut terhapus,
            // cuma jadi tidak punya atasan (supaya data tidak hilang tanpa sengaja).
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('pejabats')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pejabats', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
