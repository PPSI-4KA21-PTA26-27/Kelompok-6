<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SiteSetting;

class ProfilController extends Controller
{
    /** UC-01 dan UC-02: konten company profile, kontak, dan lokasi. */
    public function show()
    {
        $s = SiteSetting::current();

        return response()->json(['data' => [
            'nama_toko' => $s->nama_toko,
            'profil' => $s->profil,
            'keunggulan' => $s->keunggulan ?? [],
            'lokasi' => $s->lokasi,
            'kategori' => Product::aktif()->distinct()->orderBy('kategori')->pluck('kategori'),
            // Tautan yang kosong dikirim null agar frontend tidak menampilkannya (UC-02 2a).
            'whatsapp' => $s->whatsapp,
            'whatsapp_url' => $s->whatsappUrl('Halo Tani Casual, saya ingin bertanya.'),
            'instagram_url' => $s->instagram_url ?: null,
            'shopee_url' => $s->shopee_url ?: null,
        ]]);
    }
}
