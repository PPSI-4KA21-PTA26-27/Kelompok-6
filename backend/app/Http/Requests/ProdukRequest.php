<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses dibatasi oleh middleware auth:sanctum
    }

    public function rules(): array
    {
        $tambah = $this->isMethod('POST') && ! $this->route('produk');

        return [
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:1', 'max:999999999'],
            'ukuran' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'shopee_url' => ['nullable', 'url:http,https', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'foto' => [$tambah ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'foto.mimes' => 'Foto harus berformat JPG atau PNG.',
            'harga.integer' => 'Harga harus berupa angka bulat lebih dari 0.',
            'harga.min' => 'Harga harus berupa angka lebih dari 0.',
        ];
    }
}
