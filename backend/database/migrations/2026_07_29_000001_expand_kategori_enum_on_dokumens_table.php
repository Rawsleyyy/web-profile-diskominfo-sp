<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel `dokumens` sekarang dipakai 2 fitur sekaligus (Dokumen PPID & Dokumen Publik),
        // jadi kolom kategori perlu memuat kategori dari keduanya, bukan cuma 4 kategori PPID.
        DB::statement("ALTER TABLE dokumens MODIFY kategori ENUM(
            'Informasi Berkala',
            'Informasi Setiap Saat',
            'Informasi Serta Merta',
            'Informasi Dikecualikan',
            'Rilis Data',
            'LKJIP',
            'Statistik'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE dokumens MODIFY kategori ENUM(
            'Informasi Berkala',
            'Informasi Setiap Saat',
            'Informasi Serta Merta',
            'Informasi Dikecualikan'
        ) NOT NULL");
    }
};