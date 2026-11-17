<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'nama_toko', 'profil', 'keunggulan', 'lokasi',
        'whatsapp', 'instagram_url', 'shopee_url',
    ];

    protected function casts(): array
    {
        return ['keunggulan' => 'array'];
    }

    /** Konten company profile hanya satu baris. */
    public static function current(): self
    {
        return static::query()->first() ?? static::create(['nama_toko' => 'Tani Casual']);
    }

    /** Tautan percakapan WhatsApp toko; null bila nomor belum diisi (UC-05 2a). */
    public function whatsappUrl(?string $pesan = null): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $url = 'https://wa.me/'.$this->whatsapp;

        return $pesan ? $url.'?text='.rawurlencode($pesan) : $url;
    }
}
