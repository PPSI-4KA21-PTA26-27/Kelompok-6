<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Batas permintaan dengan penghitung terpisah per jenis (tidak saling memakan jatah).
        RateLimiter::for('publik', fn (Request $r) => Limit::perMinute(120)->by('publik|'.$r->ip()));
        RateLimiter::for('login', fn (Request $r) => Limit::perMinute(5)->by('login|'.$r->ip().'|'.strtolower((string) $r->input('email'))));
    }
}
