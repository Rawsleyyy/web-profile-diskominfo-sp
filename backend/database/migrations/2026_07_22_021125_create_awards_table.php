<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('awards', function (Blueprint $table) {
        $table->id();
        $table->string('title');         // Judul/Nama Penghargaan
        $table->string('category')->nullable(); // Kategori (Opsional)
        $table->year('year');            // Tahun
        $table->text('description')->nullable(); // Deskripsi
        $table->string('image')->nullable();     // Foto/Gambar
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
