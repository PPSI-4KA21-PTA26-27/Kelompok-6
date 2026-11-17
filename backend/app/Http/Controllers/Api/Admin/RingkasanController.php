<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\Models\Product;

class RingkasanController extends Controller
{
    /** UC-10: total produk dan produk terbaru. */
    public function __invoke()
    {
        return response()->json([
            'total_produk' => Product::count(),
            'produk_aktif' => Product::aktif()->count(),
            'produk_nonaktif' => Product::where('is_active', false)->count(),
            'produk_terbaru' => ProdukResource::collection(
                Product::orderByDesc('created_at')->orderByDesc('id')->limit(5)->get()
            ),
        ]);
    }
}
