<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProdukRequest;
use App\Http\Resources\ProdukResource;
use App\Models\Product;
use App\Support\FotoProduk;
use Illuminate\Http\Request;
use Throwable;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
            'urut' => ['nullable', 'in:terbaru,harga_asc,harga_desc,nama'],
        ]);

        $produk = Product::query()
            ->when($f['status'] ?? null, fn ($q, $s) => $q->where('is_active', $s === 'aktif'))
            ->filter($f)
            ->paginate(15)
            ->withQueryString();

        return ProdukResource::collection($produk);
    }

    public function store(ProdukRequest $request)
    {
        $data = $request->safe()->except('foto');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['foto'] = FotoProduk::simpan($request->file('foto'));

        try {
            $produk = Product::create($data);
        } catch (Throwable $e) {
            FotoProduk::hapus($data['foto']);
            report($e);

            return response()->json(['message' => 'Produk gagal disimpan. Silakan coba lagi.'], 500);
        }

        return (new ProdukResource($produk))->response()->setStatusCode(201);
    }

    public function show(Product $produk)
    {
        return new ProdukResource($produk);
    }

    public function update(ProdukRequest $request, Product $produk)
    {
        $data = $request->safe()->except('foto');
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $fotoLama = $produk->foto;
        if ($request->hasFile('foto')) {
            $data['foto'] = FotoProduk::simpan($request->file('foto'));
        }

        try {
            $produk->update($data);
        } catch (Throwable $e) {
            if (isset($data['foto'])) {
                FotoProduk::hapus($data['foto']);
            }
            report($e);

            return response()->json(['message' => 'Perubahan gagal disimpan. Silakan coba lagi.'], 500);
        }

        if (isset($data['foto'])) {
            FotoProduk::hapus($fotoLama);
        }

        return new ProdukResource($produk->refresh());
    }

    /** UC-12 3a: ganti status aktif/nonaktif langsung dari daftar. */
    public function status(Request $request, Product $produk)
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $produk->update(['is_active' => $request->boolean('is_active')]);

        return new ProdukResource($produk);
    }

    /** UC-13: hapus produk beserta fotonya. Konfirmasi dilakukan di frontend. */
    public function destroy(Product $produk)
    {
        $foto = $produk->foto;

        try {
            $produk->delete();
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Produk gagal dihapus. Produk tetap ada.'], 500);
        }

        FotoProduk::hapus($foto);

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}
