<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100)->default('Umum');
            $table->string('question', 500);
            $table->text('answer');
            $table->text('keywords')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        $now = now();
        DB::table('faqs')->insert([
            [
                'category' => 'Layanan',
                'question' => 'Layanan apa saja yang tersedia di Diskominfo?',
                'answer' => 'Daftar layanan yang tersedia dapat dilihat melalui menu Layanan pada website. MONIKS juga dapat membantu mencarikan layanan aktif berdasarkan nama atau kebutuhan Anda.',
                'keywords' => 'layanan, pelayanan, layanan diskominfo, apa saja layanan',
                'sort_order' => 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category' => 'PPID',
                'question' => 'Bagaimana cara mendapatkan informasi publik?',
                'answer' => 'Silakan buka menu PPID untuk melihat informasi publik yang tersedia. Jika informasi yang Anda cari belum tersedia, ikuti mekanisme permohonan informasi yang tercantum pada layanan PPID.',
                'keywords' => 'ppid, informasi publik, permohonan informasi, minta informasi, dokumen publik',
                'sort_order' => 20,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category' => 'PPID',
                'question' => 'Apakah layanan informasi publik dikenakan biaya?',
                'answer' => 'Informasi mengenai biaya dan ketentuan layanan publik sebaiknya mengikuti ketentuan resmi yang tercantum pada layanan PPID. Jika ada biaya penggandaan atau pengiriman, informasinya harus disampaikan sesuai ketentuan yang berlaku.',
                'keywords' => 'biaya, gratis, tarif, bayar, pembayaran informasi publik',
                'sort_order' => 30,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category' => 'Pengaduan',
                'question' => 'Bagaimana cara melaporkan aduan melalui ULAS?',
                'answer' => 'Untuk menyampaikan aduan, gunakan layanan ULAS yang tersedia pada bagian layanan website. Pilih layanan ULAS untuk melanjutkan ke kanal pengaduan resmi.',
                'keywords' => 'ulas, aduan, pengaduan, lapor, komplain, keluhan',
                'sort_order' => 40,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
