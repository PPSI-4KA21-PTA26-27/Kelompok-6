<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Penyelarasan dengan SRS v1.3:
 * - Produk memiliki atribut ukuran (KF-07, KF-13).
 * - Tabel lama yang tidak dipakai lagi dibersihkan dari database yang sudah ter-migrate sebelumnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('ukuran', 100)->nullable()->after('harga');
        });

        Schema::dropIfExists('faqs');
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('ukuran');
        });
    }
};
