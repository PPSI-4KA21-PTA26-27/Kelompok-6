<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/** Konten awal company profile. Pemilik mengubahnya lewat panel admin (KF-20). */
class ProfilSeeder extends Seeder
{
    public function run(): void
    {
        if (SiteSetting::query()->exists()) {
            return;
        }

        SiteSetting::create([
            'nama_toko' => 'Tani Casual',
            'profil' => 'Tani Casual adalah toko pakaian dan sepatu bergaya casual di Jakarta Pusat. Lihat koleksi kami di katalog, lalu pesan lewat WhatsApp atau Shopee.',
            'keunggulan' => [
                'Pakaian dan sepatu casual pilihan',
                'Bisa tanya langsung lewat WhatsApp',
                'Pembelian COD atau lewat Shopee',
            ],
            'lokasi' => 'Jakarta Pusat (alamat lengkap diisi oleh admin).',
            'whatsapp' => null, // diisi admin lewat panel
        ]);
    }
}
