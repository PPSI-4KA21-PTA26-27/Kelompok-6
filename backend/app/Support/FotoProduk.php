<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Menyimpan foto produk (JPG/PNG) ke disk publik.
 * Foto diperkecil (maks. 1200 px) dan dikompres agar hemat kuota disk hosting gratis.
 */
class FotoProduk
{
    private const SISI_MAKS = 1200;

    public static function simpan(UploadedFile $file): string
    {
        $png = strtolower($file->getMimeType() ?? '') === 'image/png';
        $path = 'produk/'.Str::uuid().($png ? '.png' : '.jpg');

        $isi = function_exists('imagecreatetruecolor') ? self::kompres($file, $png) : null;

        if ($isi === null) {
            // GD tidak tersedia atau gambar gagal diproses: simpan apa adanya.
            Storage::disk('public')->putFileAs('produk', $file, basename($path));
        } else {
            Storage::disk('public')->put($path, $isi);
        }

        return $path;
    }

    public static function hapus(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private static function kompres(UploadedFile $file, bool $png): ?string
    {
        $info = @getimagesize($file->getRealPath());
        if (! $info) {
            return null;
        }

        $sumber = $png
            ? @imagecreatefrompng($file->getRealPath())
            : @imagecreatefromjpeg($file->getRealPath());
        if (! $sumber) {
            return null;
        }

        [$w, $h] = $info;
        $skala = min(1, self::SISI_MAKS / max($w, $h));
        $baru = imagecreatetruecolor(max(1, (int) round($w * $skala)), max(1, (int) round($h * $skala)));

        if ($png) {
            imagealphablending($baru, false);
            imagesavealpha($baru, true);
        }

        imagecopyresampled($baru, $sumber, 0, 0, 0, 0, imagesx($baru), imagesy($baru), $w, $h);

        ob_start();
        $png ? imagepng($baru, null, 7) : imagejpeg($baru, null, 82);
        $hasil = ob_get_clean();

        imagedestroy($sumber);
        imagedestroy($baru);

        return $hasil !== false && $hasil !== '' ? $hasil : null;
    }
}
