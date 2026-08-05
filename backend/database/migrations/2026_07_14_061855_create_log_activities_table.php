<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_activities', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('method'); // GET, POST, PUT, DELETE
            $table->string('ip_address');
            $table->string('status'); // success/failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_activities');
    }
};
