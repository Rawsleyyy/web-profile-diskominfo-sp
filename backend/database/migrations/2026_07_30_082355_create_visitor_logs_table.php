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
    Schema::create('visitor_logs', function (Blueprint $table) {
        $table->id();
        $table->string('ip_address', 45);
        $table->date('visit_date');
        $table->timestamps();

        // Mencegah IP yang sama dihitung ganda pada hari yang sama
        $table->unique(['ip_address', 'visit_date']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
