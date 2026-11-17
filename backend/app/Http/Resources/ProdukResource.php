<?php

namespace App\Http\Resources;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Pengaturan toko dimuat sekali per permintaan, bukan sekali per produk.
        $setting = $request->attributes->get('site_setting');
        if (! $setting) {
            $setting = SiteSetting::current();
            $request->attributes->set('site_setting', $setting);
        }

        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'harga' => $this->harga,
            'ukuran' => $this->ukuran,
            'deskripsi' => $this->deskripsi,
            'foto_url' => $this->foto_url,
            'shopee_url' => $this->shopee_url ?: null,
            'whatsapp_url' => $setting->whatsappUrl("Halo Tani Casual, saya tertarik dengan produk {$this->nama}. Apakah masih tersedia?"),
            'is_active' => $this->is_active,
            'dibuat_pada' => $this->created_at?->toIso8601String(),
        ];
    }
}
