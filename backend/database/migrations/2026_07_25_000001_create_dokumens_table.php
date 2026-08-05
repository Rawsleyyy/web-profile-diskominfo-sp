<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            // Salah satu dari 5 kategori resmi PPID
            $table->enum('kategori', [
                'Informasi Berkala',
                'Informasi Setiap Saat',
                'Informasi Serta Merta',
                'Informasi Dikecualikan',
            ]);
            $table->string('file_path');
            $table->string('format', 10);   // PDF, XLSX, DOCX, dst — otomatis dari ekstensi file
            $table->unsignedInteger('ukuran_kb'); // otomatis dari ukuran file saat upload
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
