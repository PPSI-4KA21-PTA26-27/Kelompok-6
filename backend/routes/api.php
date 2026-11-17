<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\KontenProfilController;
use App\Http\Controllers\Api\Admin\ProdukController;
use App\Http\Controllers\Api\Admin\RingkasanController;
use App\Http\Controllers\Api\KatalogController;
use App\Http\Controllers\Api\ProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API publik (pengunjung, tanpa login)
|--------------------------------------------------------------------------
*/
Route::middleware('throttle:publik')->group(function () {
    Route::get('/profil', [ProfilController::class, 'show']);          // UC-01, UC-02
    Route::get('/kategori', [KatalogController::class, 'kategori']);
    Route::get('/produk', [KatalogController::class, 'index']);        // UC-03, UC-04
    Route::get('/produk/{id}', [KatalogController::class, 'show'])->whereNumber('id'); // UC-06
});

/*
|--------------------------------------------------------------------------
| API admin (login dengan token Sanctum)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login'); // UC-09

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/ringkasan', RingkasanController::class);          // UC-10

        // UC-11, UC-12, UC-13. Untuk unggah foto saat ubah, kirim POST dengan _method=PUT.
        Route::patch('/produk/{produk}/status', [ProdukController::class, 'status']);
        Route::apiResource('produk', ProdukController::class);

        Route::get('/profil', [KontenProfilController::class, 'show']); // UC-15
        Route::put('/profil', [KontenProfilController::class, 'update']);
    });
});
