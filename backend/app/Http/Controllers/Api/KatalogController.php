<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\Models\Product;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    /** UC-03 dan UC-04: daftar produk aktif dengan pencarian, filter, dan pengurutan. */
    public function index(Request $request)
    {
        $f = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'harga_min' => ['nullable', 'integer', 'min:0'],
            'harga_maks' => ['nullable', 'integer', 'min:0', 'gte:harga_min'],
            'urut' => ['nullable', 'in:terbaru,harga_asc,harga_desc,nama'],
            'per_halaman' => ['nullable', 'integer', 'min:1', 'max:48'],
        ], [
            'harga_maks.gte' => 'Harga maksimum tidak boleh lebih kecil dari harga minimum.',
        ]);

        $produk = Product::aktif()
            ->filter($f)
            ->paginate($f['per_halaman'] ?? 12)
            ->withQueryString();

        return ProdukResource::collection($produk);
    }

    /** UC-06: detail produk. Produk nonaktif atau terhapus dianggap tidak tersedia. */
    public function show(int $id)
    {
        $produk = Product::aktif()->find($id);

        if (! $produk) {
            return response()->json(['message' => 'Produk tidak tersedia.'], 404);
        }

        return new ProdukResource($produk);
    }

    /** Daftar kategori dari produk aktif untuk pilihan filter. */
    public function kategori()
    {
        return response()->json([
            'data' => Product::aktif()->distinct()->orderBy('kategori')->pluck('kategori'),
        ]);
    }
}
