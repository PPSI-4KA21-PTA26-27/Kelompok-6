<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/** Akun admin tunggal (SRS 3.2). Kredensial dibaca dari .env, bukan ditulis di kode. */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@tanicasual.test');
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            $password = Str::password(14, symbols: false);
            $this->command?->warn("ADMIN_PASSWORD belum diisi di .env. Kata sandi sementara: {$password}");
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => env('ADMIN_NAME', 'Admin Tani Casual'), 'password' => $password]
        );

        $this->command?->info("Akun admin siap: {$email}");
    }
}
