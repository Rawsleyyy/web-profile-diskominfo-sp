<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pejabats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('foto')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            // true = tampil di homepage (biasanya Kepala Dinas & Sekretaris Dinas)
            // false = hanya tampil di halaman "Struktur Organisasi" lengkap
            $table->boolean('tampil_utama')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pejabats');
    }
};
