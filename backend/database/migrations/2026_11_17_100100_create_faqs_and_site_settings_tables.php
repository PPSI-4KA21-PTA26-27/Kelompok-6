<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris saja: konten company profile yang dikelola admin (KF-20).
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko')->default('Tani Casual');
            $table->text('profil')->nullable();
            $table->json('keunggulan')->nullable();
            $table->text('lokasi')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('instagram_url', 500)->nullable();
            $table->string('shopee_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
