<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api, { bacaGalat } from '../lib/api'
import { KATEGORI } from '../lib/kategori'
import ProductCard from '../components/ProductCard.vue'
import InputRupiah from '../components/InputRupiah.vue'
import PaginationBar from '../components/PaginationBar.vue'
import StateLoading from '../components/StateLoading.vue'
import StateError from '../components/StateError.vue'
import StateEmpty from '../components/StateEmpty.vue'
import BaseButton from '../components/BaseButton.vue'

const route = useRoute()
const router = useRouter()

const URUTAN = [
  { nilai: 'terbaru', nama: 'Terbaru' },
  { nilai: 'harga_asc', nama: 'Harga terendah' },
  { nilai: 'harga_desc', nama: 'Harga tertinggi' },
]
const PER_HALAMAN = 12

// Isi kolom filter. Sumber kebenarannya adalah alamat halaman (query), supaya
// tombol kembali browser dan tautan yang dibagikan membawa filter yang sama.
const form = reactive({ q: '', kategori: '', min: '', maks: '', urut: 'terbaru' })
const halaman = ref(1)

const produk = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const memuat = ref(true)
const galat = ref(null)
const galatHarga = ref('')
let nomorPermintaan = 0

const adaFilter = computed(
  () => !!(form.q || form.kategori || form.min || form.maks || form.urut !== 'terbaru'),
)

function bacaQuery(q) {
  form.q = typeof q.q === 'string' ? q.q : ''
  form.kategori = KATEGORI.includes(q.kategori) ? q.kategori : ''
  form.min = /^\d+$/.test(q.harga_min ?? '') ? q.harga_min : ''
  form.maks = /^\d+$/.test(q.harga_maks ?? '') ? q.harga_maks : ''
  form.urut = URUTAN.some((u) => u.nilai === q.urut) ? q.urut : 'terbaru'
  halaman.value = Math.max(1, parseInt(q.halaman, 10) || 1)
}

// KF-04 s.d. KF-06: validasi harga minimum > maksimum sebelum meminta data.
function cekHarga() {
  if (form.min && form.maks && Number(form.min) > Number(form.maks)) {
    galatHarga.value = 'Harga minimum tidak boleh besar dari harga maksimum.'
    return false
  }
  galatHarga.value = ''
  return true
}

function terapkan(pindahHalaman) {
  if (!cekHarga()) return
  const query = {}
  if (form.q.trim()) query.q = form.q.trim()
  if (form.kategori) query.kategori = form.kategori
  if (form.min) query.harga_min = form.min
  if (form.maks) query.harga_maks = form.maks
  if (form.urut !== 'terbaru') query.urut = form.urut
  const tujuan = pindahHalaman ?? 1
  if (tujuan > 1) query.halaman = String(tujuan)
  router.push({ name: 'katalog', query })
}

function hapusFilter() {
  galatHarga.value = ''
  router.push({ name: 'katalog' })
}

function pindahHalaman(n) {
  terapkan(n)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function muat() {
  const saya = ++nomorPermintaan
  memuat.value = true
  galat.value = null
  try {
    const params = { per_halaman: PER_HALAMAN, page: halaman.value, urut: form.urut }
    if (form.q.trim()) params.q = form.q.trim()
    if (form.kategori) params.kategori = form.kategori
    if (form.min) params.harga_min = form.min
    if (form.maks) params.harga_maks = form.maks
    const { data } = await api.get('/produk', { params })
    if (saya !== nomorPermintaan) return // sudah ada permintaan yang lebih baru
    produk.value = data.data
    meta.value = data.meta
  } catch (e) {
    if (saya !== nomorPermintaan) return
    galat.value = bacaGalat(e)
  } finally {
    if (saya === nomorPermintaan) memuat.value = false
  }
}

watch(
  () => route.query,
  (q) => {
    if (route.name !== 'katalog') return
    bacaQuery(q)
    // Alamat dengan harga terbalik: tampilkan pesan, jangan minta data.
    if (!cekHarga()) {
      memuat.value = false
      return
    }
    muat()
  },
  { immediate: true },
)
</script>

<template>
  <!-- Judul halaman -->
  <section class="bg-mist py-9">
    <div class="wrap space-y-2">
      <h1 class="text-[28px] sm:text-[34px] lg:text-[40px]">Katalog Produk</h1>
      <p class="text-base text-ink">Jaket, celana, sepatu, dan topi pilihan dari Tani Casual.</p>
    </div>
  </section>

  <!-- Cari, filter, dan urutan -->
  <section class="border-y border-line bg-white py-5" aria-label="Cari, filter, dan urutan">
    <form class="wrap" @submit.prevent="terapkan()">
      <div class="flex flex-wrap items-center gap-3">
        <div class="flex w-full gap-3 lg:w-auto lg:flex-1">
          <input
            v-model="form.q"
            type="search"
            maxlength="100"
            placeholder="Cari nama produk..."
            aria-label="Cari nama produk"
            class="h-10 min-w-0 flex-1 rounded-[10px] border border-line bg-white px-3.5 text-sm text-navy placeholder:text-muted focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20"
          />
          <BaseButton type="submit" ukuran="sm">Cari</BaseButton>
        </div>

        <div class="grid w-full grid-cols-2 gap-3 lg:flex lg:w-auto lg:items-center">
          <select
            v-model="form.kategori"
            aria-label="Kategori"
            class="h-10 rounded-[10px] border border-line bg-white px-3 text-sm text-navy focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 lg:w-[170px]"
            @change="terapkan()"
          >
            <option value="">Semua kategori</option>
            <option v-for="k in KATEGORI" :key="k" :value="k">{{ k }}</option>
          </select>

          <select
            v-model="form.urut"
            aria-label="Urutkan"
            class="h-10 rounded-[10px] border border-line bg-white px-3 text-sm text-navy focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 lg:order-last lg:w-[150px]"
            @change="terapkan()"
          >
            <option v-for="u in URUTAN" :key="u.nilai" :value="u.nilai">{{ u.nama }}</option>
          </select>

          <div class="col-span-2 flex items-center gap-2 lg:contents">
            <InputRupiah
              v-model="form.min"
              placeholder="Rp Harga min"
              aria-label="Harga minimum"
              :galat="!!galatHarga"
              class="lg:w-[130px]"
              @terapkan="terapkan()"
            />
            <span class="text-sm text-ink" aria-hidden="true">-</span>
            <InputRupiah
              v-model="form.maks"
              placeholder="Rp Harga maks"
              aria-label="Harga maksimum"
              :galat="!!galatHarga"
              class="lg:w-[130px]"
              @terapkan="terapkan()"
            />
          </div>
        </div>

        <button
          v-if="adaFilter"
          type="button"
          class="text-[13px] text-brand underline"
          @click="hapusFilter"
        >Hapus filter</button>
      </div>

      <p v-if="galatHarga" class="mt-2 text-xs text-danger lg:text-right" role="alert">(!) {{ galatHarga }}</p>
    </form>
  </section>

  <!-- Hasil -->
  <section class="py-8 md:py-10">
    <div class="wrap">
      <StateLoading v-if="memuat" pesan="Memuat produk..." />

      <StateError v-else-if="galat" :pesan="galat.pesan" @ulang="muat" />

      <template v-else-if="!produk.length">
        <StateEmpty
          v-if="adaFilter"
          ikon="cari"
          judul="Produk tidak ditemukan"
          pesan="Coba ubah kata kunci atau hapus filter yang aktif."
        >
          <BaseButton ukuran="sm" @click="hapusFilter">Hapus Filter</BaseButton>
        </StateEmpty>
        <StateEmpty
          v-else
          judul="Produk belum tersedia"
          pesan="Belum ada produk saat ini atau coba kembali lagi nanti."
        >
          <BaseButton ke="/" ukuran="sm">Kembali ke Beranda</BaseButton>
        </StateEmpty>
      </template>

      <div v-else class="space-y-8">
        <div class="grid grid-cols-2 gap-3 sm:gap-6 lg:grid-cols-4">
          <ProductCard v-for="p in produk" :key="p.id" :produk="p" />
        </div>
        <PaginationBar :halaman="meta.current_page" :terakhir="meta.last_page" @pindah="pindahHalaman" />
      </div>
    </div>
  </section>
</template>
