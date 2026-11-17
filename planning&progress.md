# Planning & Progress: Tani Casual

Proyek: `C:\Sistem Informasi\tanicasual`
Stack: Vue 3, Vite, Tailwind, Vue Router, Axios (frontend) + Laravel/Sanctum (backend)
Terakhir diperbarui: 17 November 2026 (dicek langsung dari isi folder `frontend/`)

Legenda: `[x]` selesai, `[ ]` belum dikerjakan, `[~]` sebagian / menunggu keputusan

---

## Ringkasan Progres

| Tahap | Isi | Status |
|---|---|---|
| 0 | Pembacaan desain & penyesuaian rencana | Selesai |
| 1 | Fondasi frontend | Hampir selesai (sisa cek CORS & seeder) |
| 2 | Sprint 1: Beranda & Katalog | Hampir selesai (sisa uji & ukur performa) |
| 3 | Sprint 2: Detail, WhatsApp, Shopee, Kontak | Belum (rute masih placeholder) |
| 4 | Sprint 3: Panel Admin | Belum (rute & guard login ada, halaman placeholder) |
| 5 | Pengujian & perbaikan | Belum |
| 6 | Deployment | Belum |
| 7 | Dokumen & serah terima | Belum |

---

## Tahap 0: Persiapan & Desain (SELESAI)

- [x] Backend sudah tersedia (API, `whatsapp_url`, `ProdukContohSeeder`, `AdminSeeder`, sesi Sanctum 120 menit)
- [x] Desain FigCraft dibaca (~100 frame mobile & desktop, wireframe + mockup per halaman)
- [x] Inventaris layar: Publik (Beranda, Katalog, Detail, Kontak), Admin (Login + error, Ringkasan, Daftar Produk, Tambah/Ubah, Ubah Profil, modal Hapus), kondisi khusus (katalog kosong, belum ada produk, gagal memuat, produk tidak tersedia, validasi harga, detail tanpa Shopee, kontak tautan kosong)
- [x] Konfirmasi: desain tidak memuat chatbot/FAQ, sesuai SRS final
- [x] Palet & gaya ditetapkan
  - Navy `#0B1220`, Primary Blue `#1D4ED8`, Sky `#DBEAFE`, Ice `#EAF2FF`, Mist `#EFF6FF`, Border `#BFD3F5`, Slate `#475569`, Muted `#94A3B8`
  - Font geometris sans-serif (Poppins), tombol pil di navbar, kartu bersudut membulat
  - Tombol WhatsApp hijau, Shopee oranye-merah, Instagram outline
- [x] Keputusan selisih desain vs sistem
  - Galeri Detail Produk: ikuti SRS, **1 foto**, thumbnail dihilangkan (galeri = change request + ubah backend)
  - Ukuran: teks dipecah per koma menjadi chip
  - "Produk Unggulan" Beranda: tampilkan **4 produk terbaru** (backend tidak punya penanda unggulan)
  - Ringkasan admin: ikuti desain (Total Produk + tabel terbaru); data aktif/nonaktif API tidak dipakai
- [x] Daftar kategori tetap dipakai di kode (`lib/kategori.js`): Jaket, Sepatu, Celana, Topi
  - Dropdown form admin nanti harus memakai daftar yang sama
  - Catatan: desain tidak konsisten (filter: Jaket/Sepatu/Celana/Topi; form admin: Kaos/Kemeja/Celana/Aksesoris)
- [x] Logo sudah ada di project (`assets/logo.png`, `logotanicasual.jpg`, `LogoTani.vue`, `favicon.png`)

---

## Tahap 1: Fondasi Frontend

- [x] Buat project (Vue 3, Vite, Tailwind 4, Vue Router, Axios, font Poppins)
- [ ] Atur `FRONTEND_URL` di backend untuk CORS (belum dicek)
- [x] Token warna & font (Poppins) di Tailwind (`style.css`)
- [x] Layout dasar: `AppNavbar`, `AppFooter`, `App.vue`, responsif, teks bahasa Indonesia
- [x] Identitas visual: logo & warna toko
- [x] Komponen dasar: `BaseButton`, `BaseInput`, `InputRupiah`, `ProductCard`, `StatusToggle`, `ConfirmModal`, `PaginationBar`
- [x] State kosong / galat / memuat (`StateEmpty`, `StateError`, `StateLoading`, `useMuat.js`)
- [x] Lapisan API (`lib/api.js`): `VITE_API_URL`, penanganan 401/422/429/5xx/timeout, token store, redirect saat 401
- [x] Router: semua rute publik & admin terdaftar, guard login, judul halaman, halaman 404
- [ ] Jalankan `ProdukContohSeeder` untuk data contoh (belum dicek)

## Tahap 2: Sprint 1, Company Profile & Katalog (UC-01, UC-03, UC-04)

- [x] Beranda (`HomeView.vue`): hero, Tentang Kami (dari `/profil`, ada teks cadangan), 4 produk terbaru, banner ajakan, tombol ke katalog
- [~] Beranda: keunggulan, kategori, dan tombol WhatsApp langsung belum ada (desain hanya memuat tombol ke Kontak); cek lagi ke SRS KF-01/KF-02
- [x] Katalog (KF-03): grid produk, pagination, pesan "Produk belum tersedia"
- [x] Cari berdasarkan nama (KF-04)
- [x] Filter kategori & rentang harga (KF-05), filter tersimpan di URL
- [x] Urut harga & terbaru (KF-06)
- [x] Validasi harga min > maks
- [x] Pesan "Produk tidak ditemukan" + tombol "Hapus Filter"
- [ ] Target tampil < 3 detik (KNF-01), belum diukur

## Tahap 3: Sprint 2, Detail, WhatsApp, Shopee, Kontak (UC-02, UC-05, UC-06, UC-07)

- [ ] Detail produk (KF-07): breadcrumb, foto, nama, kategori, harga, chip ukuran, deskripsi
- [ ] Produk nonaktif/terhapus: "Produk tidak tersedia" + tautan kembali
- [ ] Tombol "Pesan via WhatsApp" (KF-08), pakai `whatsapp_url`, disembunyikan bila kosong
- [ ] Tombol "Beli di Shopee" (KF-09), disembunyikan bila tautan kosong
- [ ] Halaman Kontak & Lokasi (KF-10): alamat, Instagram, WhatsApp, Shopee; tautan kosong tidak ditampilkan

## Tahap 4: Sprint 3, Panel Admin (UC-09 s.d. UC-13, UC-15)

- [~] Login & logout (KF-11): token store + redirect ke login bila belum masuk sudah ada; halaman login, tombol logout, pesan galat belum
- [ ] Logout otomatis saat tidak aktif (KNF-10), pelacak di frontend
- [ ] Ringkasan (KF-12): Total Produk + 5 produk terbaru
- [ ] Daftar produk (KF-13): tabel, pencarian, toggle aktif/nonaktif
- [ ] Form tambah/ubah (KF-14, KF-15): nama, kategori, harga, ukuran, deskripsi, foto, tautan Shopee; validasi "(!) ... wajib diisi"; pratinjau foto; JPG/PNG maks 2 MB
- [ ] Ubah produk dengan foto: POST + `_method=PUT`
- [ ] Konfirmasi sebelum hapus (KF-16)
- [ ] Ubah konten profil (KF-20, Could): dikerjakan terakhir, diturunkan dulu bila waktu mepet

## Tahap 5: Pengujian & Perbaikan (Minggu 13)

- [ ] Kasus uji dari kriteria penerimaan US-01 s.d. US-15; isi kolom "Kasus Uji" di matriks keterlacakan
- [ ] Uji lintas browser (Chrome, Edge, Firefox) & perangkat (HP, laptop), KNF-05
- [ ] Uji performa < 3 detik
- [ ] Uji login, hak akses, pembatasan login
- [ ] Test backend tambahan bila perlu
- [ ] UAT bersama pemilik toko
- [ ] Perbaikan bug hasil UAT

## Tahap 6: Deployment

Backend (alwaysdata):
- [ ] Ganti database SQLite ke MySQL
- [ ] `.env` produksi: `APP_DEBUG=false`, `FRONTEND_URL`, `SANCTUM_EXPIRATION`
- [ ] Jalankan migration, `storage:link`, seed admin
- [ ] Ganti kata sandi bawaan `AdminSeeder`

Frontend (Vercel):
- [ ] Hubungkan ke repo
- [ ] Isi URL API produksi lewat environment variable
- [ ] Pastikan HTTPS

Data & backup:
- [ ] Pemilik memasukkan produk asli & konten profil, lalu hapus produk contoh
- [ ] Backup (KNF-04): cara export MySQL mingguan yang bisa dijalankan pemilik

## Tahap 7: Dokumen & Serah Terima (Minggu 14-15)

- [ ] User manual admin: login, tambah/ubah/hapus produk, ubah profil, backup
- [ ] Dokumen penerapan: arsitektur, cara deploy, daftar akun layanan
- [ ] Pelatihan pemilik
- [ ] BAST
- [ ] Serahkan kredensial hosting ke pemilik (KNF-07)
- [ ] Bersihkan repo: rapikan `tanicasual-app-lama` & file bawaan Laravel tak terpakai
- [ ] Rapikan README & GitHub (KNF-06)

---

## Langkah Berikutnya

1. Cek backend: `FRONTEND_URL` (CORS) dan `ProdukContohSeeder` sudah jalan
2. Sprint 2: Detail Produk (`/produk/:id`) dan Kontak (`/kontak`)
3. Sprint 3: Login admin, lalu Ringkasan, Daftar Produk, Form, Ubah Profil
