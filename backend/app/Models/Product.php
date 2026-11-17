<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'nama', 'kategori', 'harga', 'ukuran', 'deskripsi', 'foto', 'shopee_url', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Pencarian, filter kategori, rentang harga, dan pengurutan (KF-04 s.d. KF-06). */
    public function scopeFilter(Builder $query, array $f): Builder
    {
        $query
            ->when($f['q'] ?? null, function ($q, $kata) {
                $kata = addcslashes($kata, '%_\\');
                $q->where('nama', 'like', "%{$kata}%");
            })
            ->when($f['kategori'] ?? null, fn ($q, $k) => $q->where('kategori', $k))
            ->when($f['harga_min'] ?? null, fn ($q, $v) => $q->where('harga', '>=', $v))
            ->when($f['harga_maks'] ?? null, fn ($q, $v) => $q->where('harga', '<=', $v));

        return match ($f['urut'] ?? 'terbaru') {
            'harga_asc' => $query->orderBy('harga')->orderByDesc('id'),
            'harga_desc' => $query->orderByDesc('harga')->orderByDesc('id'),
            'nama' => $query->orderBy('nama')->orderByDesc('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }
}
