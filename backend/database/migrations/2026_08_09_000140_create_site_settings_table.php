<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Diskominfo SP Kota Surakarta');
            $table->string('site_short_name')->default('Diskominfo SP');
            $table->text('site_description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->text('footer_text')->nullable();
            $table->boolean('announcement_enabled')->default(false);
            $table->string('announcement_text')->nullable();
            $table->string('announcement_url')->nullable();
            $table->string('announcement_color')->default('#DC2626');
            $table->timestamp('announcement_from')->nullable();
            $table->timestamp('announcement_until')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            'site_name' => 'Diskominfo SP Kota Surakarta',
            'site_short_name' => 'Diskominfo SP',
            'site_description' => 'Portal informasi Dinas Komunikasi, Informatika, Statistik dan Persandian Kota Surakarta.',
            'phone' => '(0271) 806060',
            'email' => 'diskominfosp@surakarta.go.id',
            'footer_text' => 'Diskominfo SP Kota Surakarta',
            'announcement_enabled' => false,
            'announcement_color' => '#DC2626',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
