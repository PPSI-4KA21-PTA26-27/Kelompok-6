<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaniCasualApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        SiteSetting::create(['nama_toko' => 'Tani Casual', 'whatsapp' => '6281234567890']);
    }

    private function produk(array $o = []): Product
    {
        return Product::create($o + [
            'nama' => 'Kaos Polos', 'kategori' => 'Kaos', 'harga' => 75000, 'is_active' => true,
        ]);
    }

    private function admin(): User
    {
        return tap(User::factory()->create(), fn ($u) => Sanctum::actingAs($u));
    }

    // ---- Pengunjung ----

    public function test_katalog_hanya_menampilkan_produk_aktif(): void
    {
        $this->produk(['nama' => 'Aktif']);
        $this->produk(['nama' => 'Nonaktif', 'is_active' => false]);

        $this->getJson('/api/produk')->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama', 'Aktif');
    }

    public function test_cari_filter_dan_urut(): void
    {
        $this->produk(['nama' => 'Kaos Putih', 'kategori' => 'Kaos', 'harga' => 90000]);
        $this->produk(['nama' => 'Kaos Hitam', 'kategori' => 'Kaos', 'harga' => 60000]);
        $this->produk(['nama' => 'Sepatu Putih', 'kategori' => 'Sepatu', 'harga' => 250000]);

        $this->getJson('/api/produk?q=putih')->assertJsonCount(2, 'data');
        $this->getJson('/api/produk?kategori=Sepatu')->assertJsonCount(1, 'data');
        $this->getJson('/api/produk?harga_min=70000&harga_maks=100000')
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.nama', 'Kaos Putih');
        $this->getJson('/api/produk?urut=harga_asc')->assertJsonPath('data.0.nama', 'Kaos Hitam');
    }

    public function test_rentang_harga_tidak_valid_ditolak(): void
    {
        $this->getJson('/api/produk?harga_min=100000&harga_maks=50000')
            ->assertStatus(422)->assertJsonValidationErrors('harga_maks');
    }

    public function test_detail_produk_dan_tombol_whatsapp_shopee(): void
    {
        $p = $this->produk(['shopee_url' => 'https://shopee.co.id/tani-casual/1']);

        $res = $this->getJson("/api/produk/{$p->id}")->assertOk();
        $res->assertJsonPath('data.shopee_url', 'https://shopee.co.id/tani-casual/1');
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $res->json('data.whatsapp_url'));
        $this->assertStringContainsString('Kaos%20Polos', $res->json('data.whatsapp_url'));
    }

    public function test_tautan_kosong_dikirim_null(): void
    {
        SiteSetting::current()->update(['whatsapp' => null]);
        $p = $this->produk();

        $this->getJson("/api/produk/{$p->id}")
            ->assertJsonPath('data.shopee_url', null)
            ->assertJsonPath('data.whatsapp_url', null);
    }

    public function test_produk_nonaktif_tidak_tersedia(): void
    {
        $p = $this->produk(['is_active' => false]);

        $this->getJson("/api/produk/{$p->id}")->assertStatus(404)
            ->assertJsonPath('message', 'Produk tidak tersedia.');
    }

    // ---- Admin ----

    public function test_halaman_admin_butuh_login(): void
    {
        $this->getJson('/api/admin/ringkasan')->assertStatus(401);
        $this->postJson('/api/admin/produk', [])->assertStatus(401);
    }

    public function test_login_benar_dan_salah(): void
    {
        $u = User::factory()->create(['email' => 'a@b.test', 'password' => 'rahasia123']);

        $this->postJson('/api/admin/login', ['email' => 'a@b.test', 'password' => 'salah'])
            ->assertStatus(422)->assertJsonPath('errors.email.0', 'Email atau kata sandi salah.');

        $this->postJson('/api/admin/login', ['email' => 'a@b.test', 'password' => 'rahasia123'])
            ->assertOk()->assertJsonStructure(['token', 'admin' => ['id', 'nama', 'email']]);

        $this->assertNotSame('rahasia123', $u->fresh()->password); // ter-hash
    }

    public function test_tambah_produk_dengan_foto(): void
    {
        $this->admin();

        $res = $this->postJson('/api/admin/produk', [
            'nama' => 'Kemeja Flanel', 'kategori' => 'Kemeja', 'harga' => 145000,
            'deskripsi' => 'Ukuran M, L', 'is_active' => true,
            'foto' => UploadedFile::fake()->image('foto.jpg', 1600, 1200),
        ])->assertCreated();

        $path = Product::first()->foto;
        Storage::disk('public')->assertExists($path);
        $this->assertNotNull($res->json('data.foto_url'));
    }

    public function test_tambah_produk_validasi(): void
    {
        $this->admin();

        $this->postJson('/api/admin/produk', ['nama' => '', 'kategori' => '', 'harga' => 0])
            ->assertStatus(422)->assertJsonValidationErrors(['nama', 'kategori', 'harga', 'foto']);

        $this->postJson('/api/admin/produk', [
            'nama' => 'X', 'kategori' => 'Y', 'harga' => 1000,
            'foto' => UploadedFile::fake()->create('dok.pdf', 100, 'application/pdf'),
        ])->assertStatus(422)->assertJsonValidationErrors('foto');

        $this->postJson('/api/admin/produk', [
            'nama' => 'X', 'kategori' => 'Y', 'harga' => 1000,
            'foto' => UploadedFile::fake()->image('besar.jpg')->size(2500),
        ])->assertStatus(422)->assertJsonValidationErrors('foto');

        $this->assertSame(0, Product::count());
    }

    public function test_ubah_status_dan_hapus_produk_menghapus_foto(): void
    {
        $this->admin();
        $this->postJson('/api/admin/produk', [
            'nama' => 'Jaket', 'kategori' => 'Jaket', 'harga' => 200000,
            'foto' => UploadedFile::fake()->image('j.png', 400, 400),
        ])->assertCreated();
        $p = Product::first();

        $this->patchJson("/api/admin/produk/{$p->id}/status", ['is_active' => false])->assertOk();
        $this->getJson('/api/produk')->assertJsonCount(0, 'data');

        $this->putJson("/api/admin/produk/{$p->id}", ['nama' => 'Jaket Baru', 'kategori' => 'Jaket', 'harga' => 210000])
            ->assertOk()->assertJsonPath('data.nama', 'Jaket Baru');

        $foto = $p->fresh()->foto;
        $this->deleteJson("/api/admin/produk/{$p->id}")->assertOk();
        Storage::disk('public')->assertMissing($foto);
        $this->assertSame(0, Product::count());
    }

    public function test_ringkasan_admin(): void
    {
        $this->admin();
        $this->produk(['nama' => 'A']);
        $this->produk(['nama' => 'B', 'is_active' => false]);

        $this->getJson('/api/admin/ringkasan')->assertOk()
            ->assertJsonPath('total_produk', 2)
            ->assertJsonPath('produk_aktif', 1)
            ->assertJsonCount(2, 'produk_terbaru');
    }

    public function test_ukuran_produk_tersimpan_dan_tampil_di_detail(): void
    {
        $this->admin();

        $id = $this->postJson('/api/admin/produk', [
            'nama' => 'Kemeja Flanel', 'kategori' => 'Kemeja', 'harga' => 145000, 'ukuran' => 'M, L, XL',
            'foto' => UploadedFile::fake()->image('k.jpg', 800, 800),
        ])->assertCreated()->json('data.id');

        $this->getJson("/api/produk/{$id}")->assertOk()->assertJsonPath('data.ukuran', 'M, L, XL');
    }

    public function test_ubah_konten_profil_dan_normalisasi_whatsapp(): void
    {
        $this->admin();

        $this->putJson('/api/admin/profil', ['nama_toko' => 'Tani Casual', 'whatsapp' => '0812-3456-7890'])
            ->assertOk()->assertJsonPath('data.whatsapp', '6281234567890');

        $this->putJson('/api/admin/profil', ['nama_toko' => 'Tani Casual', 'whatsapp' => '12345'])
            ->assertStatus(422)->assertJsonValidationErrors('whatsapp');

        $this->putJson('/api/admin/profil', ['nama_toko' => 'Tani Casual', 'shopee_url' => 'bukan-url'])
            ->assertStatus(422)->assertJsonValidationErrors('shopee_url');

        $this->getJson('/api/profil')->assertOk()->assertJsonPath('data.nama_toko', 'Tani Casual');
    }
}
