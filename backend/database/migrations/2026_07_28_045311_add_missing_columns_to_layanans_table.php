<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom yang dipakai oleh fitur admin "Kelola Layanan"
     * (app/livewire/admin/Layanan.php) tapi belum ada di migration awal.
     */
    public function up(): void
    {
        Schema::table('layanans', function (Blueprint $table) {
            $table->string('kategori')->default('Umum')->after('nama_layanan');
            $table->text('deskripsi')->nullable()->after('kategori');
            $table->boolean('is_active')->default(true)->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('layanans', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'deskripsi', 'is_active']);
        });
    }
};
