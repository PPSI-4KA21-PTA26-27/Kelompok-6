<?php

use Illuminate\Support\Facades\Route;

// Backend hanya menyediakan API (lihat routes/api.php). Antarmuka ada di folder frontend.
Route::get('/', fn () => response()->json([
    'aplikasi' => 'Tani Casual API',
    'status' => 'berjalan',
]));
