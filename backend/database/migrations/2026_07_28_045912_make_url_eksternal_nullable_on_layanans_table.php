<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom 'url_eksternal' pada migration awal bersifat wajib (NOT NULL, tanpa default),
     * padahal form admin "Kelola Layanan" yang sekarang tidak mengisi field ini.
     * Dibuat nullable supaya insert dari Livewire tidak gagal.
     */
    public function up(): void
    {
        Schema::table('layanans', function (Blueprint $table) {
            $table->string('url_eksternal')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('layanans', function (Blueprint $table) {
            $table->string('url_eksternal')->nullable(false)->change();
        });
    }
};
