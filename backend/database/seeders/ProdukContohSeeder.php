<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Produk CONTOH untuk pengembangan dan uji frontend, diambil dari mockup desain
 * (nama, harga, kategori, foto). Bukan data asli toko.
 *
 * Jalankan: php artisan db:seed --class=ProdukContohSeeder
 * Hapus lewat panel admin setelah produk asli dimasukkan (Tahap 6).
 * Foto contoh ada di database/seeders/foto-contoh dan disalin ke disk publik.
 */
class ProdukContohSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan contoh lama (versi sebelumnya memakai akhiran "(contoh)").
        Product::where('nama', 'like', '%(contoh)')->delete();

        // Urutan dibuat terbalik dari tampilan katalog desain, supaya urutan
        // "Terbaru" (id terbesar lebih dulu) sama dengan mockup.
        $contoh = [
            ['Trackpants Kappa Taped', 'Celana', 350000, '28 - 34', 'Trackpants Kappa dengan list taped di sisi.', 'trackpants-kappa'],
            ['Caps Lyle & Scoot Black', 'Topi', 200000, 'All size', 'Topi baseball Lyle & Scott, tersedia beberapa warna.', 'caps-lyle-scoot'],
            ['Adidas Spezial Purple', 'Sepatu', 1300000, '40 - 44', 'Adidas Spezial warna ungu.', 'adidas-spezial-purple'],
            ['Adidas Bermuda Glow Pink', 'Sepatu', 1300000, '38 - 43', 'Adidas Bermuda warna pink.', 'adidas-bermuda-pink'],
            ['Adidas City Series Bali', 'Sepatu', 1700000, '40 - 44', 'Adidas City Series edisi Bali.', 'adidas-bali'],
            ['Adidas City Series Koln 24', 'Sepatu', 1300000, '40 - 44', 'Adidas City Series edisi Koln 24.', 'adidas-koln-24'],
            ['Adidas City Series Stadt Green', 'Sepatu', 800000, '40 - 44', 'Adidas City Series edisi Stadt warna hijau.', 'adidas-stadt-green'],
            ['Tracktop Adidas Firebird Sky Blue', 'Jaket', 700000, 'M, L, XL', 'Tracktop Adidas Firebird warna biru langit.', 'tracktop-adidas-firebird'],
            ['Tracktop Kappa Tapped Black White', 'Jaket', 400000, 'M, L, XL', 'Tracktop Kappa hitam dengan list putih.', 'tracktop-kappa-tapped'],
            ['Overshirt Troublemaker', 'Jaket', 200000, 'M, L, XL', 'Overshirt dengan banyak saku.', 'overshirt-troublemaker'],
        ];

        foreach ($contoh as [$nama, $kategori, $harga, $ukuran, $deskripsi, $berkas]) {
            Product::updateOrCreate(
                ['nama' => $nama],
                [
                    'kategori' => $kategori,
                    'harga' => $harga,
                    'ukuran' => $ukuran,
                    'deskripsi' => $deskripsi,
                    'foto' => $this->salinFoto($berkas),
                    'is_active' => true,
                ]
            );
        }
    }

    /** Salin foto contoh ke disk publik; null bila berkasnya tidak ada. */
    private function salinFoto(string $berkas): ?string
    {
        $sumber = database_path("seeders/foto-contoh/{$berkas}.jpg");
        if (! is_file($sumber)) {
            return null;
        }

        $tujuan = "produk/contoh-{$berkas}.jpg";
        Storage::disk('public')->put($tujuan, file_get_contents($sumber));

        return $tujuan;
    }
}
