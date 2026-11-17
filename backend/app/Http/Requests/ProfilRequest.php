<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->whatsapp)) {
            $this->merge(['whatsapp' => preg_replace('/[\s\-().]/', '', $this->whatsapp)]);
        }
    }

    public function rules(): array
    {
        return [
            'nama_toko' => ['required', 'string', 'max:255'],
            'profil' => ['nullable', 'string', 'max:5000'],
            'keunggulan' => ['nullable', 'array', 'max:10'],
            'keunggulan.*' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:1000'],
            'whatsapp' => ['nullable', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:500'],
            'shopee_url' => ['nullable', 'url:http,https', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890.',
            'instagram_url.url' => 'Tautan Instagram tidak valid. Awali dengan https://.',
            'shopee_url.url' => 'Tautan Shopee tidak valid. Awali dengan https://.',
        ];
    }

    /** Nomor disimpan dalam format internasional tanpa tanda plus (62812...). */
    public function dataTersimpan(): array
    {
        $data = $this->validated();

        if (! empty($data['whatsapp'])) {
            $data['whatsapp'] = '62'.ltrim(preg_replace('/^(\+62|62|0)/', '', $data['whatsapp']), '0');
        }

        $data['keunggulan'] = array_values(array_filter($data['keunggulan'] ?? []));

        return $data;
    }
}
