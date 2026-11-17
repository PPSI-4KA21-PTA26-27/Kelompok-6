<script setup>
import { onMounted } from 'vue'
import api from '../lib/api'
import { useMuat } from '../lib/useMuat'
import heroFoto from '../assets/hero.jpg'
import ProductCard from '../components/ProductCard.vue'
import StateLoading from '../components/StateLoading.vue'
import StateError from '../components/StateError.vue'
import StateEmpty from '../components/StateEmpty.vue'
import BaseButton from '../components/BaseButton.vue'

// Teks cadangan bila profil dari server belum terisi (sama dengan desain).
const PROFIL_CADANGAN =
  'Tani Casual adalah usaha kecil penjualan pakaian dan sepatu bergaya casual di Jakarta Pusat, dikelola langsung oleh pemiliknya. Lihat produk di katalog, lalu pesan via WhatsApp atau beli di Shopee.'

const profil = useMuat(() => api.get('/profil'))
// Backend belum punya penanda "unggulan": tampilkan 4 produk terbaru.
const unggulan = useMuat(() => api.get('/produk', { params: { urut: 'terbaru', per_halaman: 4 } }))

onMounted(() => {
  profil.jalankan()
  unggulan.jalankan()
})
</script>

<template>
  <!-- Hero -->
  <section class="bg-gradient-to-b from-ice to-white">
    <div class="wrap flex flex-col gap-6 py-8 md:flex-row md:items-center md:gap-14 md:py-14">
      <div class="flex flex-1 flex-col items-start gap-4">
        <h1 class="text-[28px] leading-tight sm:text-4xl lg:text-[52px] lg:leading-[64px]">
          Pakaian Casual untuk Keseharian
        </h1>
        <p class="text-base text-ink lg:text-lg">
          Jaket, celana, sepatu, dan topi bergaya casual pilihan dari Jakarta Pusat.
        </p>
        <BaseButton ke="/katalog" ukuran="lg">Lihat Katalog</BaseButton>
      </div>

      <div class="order-first overflow-hidden rounded-[20px] bg-sky-soft shadow-hero md:order-last md:w-[560px] md:shrink-0">
        <img
          :src="heroFoto"
          alt="Koleksi pakaian Tani Casual"
          width="1120"
          height="720"
          class="aspect-[560/360] w-full scale-[1.08] object-cover"
        />
      </div>
    </div>
  </section>

  <!-- Tentang Kami -->
  <section class="bg-mist py-8 md:py-12">
    <div class="wrap space-y-3">
      <h2 class="text-2xl md:text-[32px]">Tentang Kami</h2>
      <p class="text-base leading-relaxed text-ink">{{ profil.data.value?.data?.profil || PROFIL_CADANGAN }}</p>
    </div>
  </section>

  <!-- Produk Unggulan -->
  <section class="py-8 md:py-12">
    <div class="wrap space-y-5">
      <h2 class="text-2xl md:text-[32px]">Produk Unggulan</h2>

      <StateLoading v-if="unggulan.memuat.value" pesan="Memuat produk..." />
      <StateError v-else-if="unggulan.galat.value" :pesan="unggulan.galat.value.pesan" @ulang="unggulan.jalankan()" />
      <StateEmpty
        v-else-if="!unggulan.data.value?.data?.length"
        judul="Produk belum tersedia"
        pesan="Belum ada produk saat ini. Coba lagi nanti."
      />
      <div v-else class="grid grid-cols-2 gap-3 pb-3 sm:gap-6 lg:grid-cols-4">
        <ProductCard v-for="p in unggulan.data.value.data" :key="p.id" :produk="p" />
      </div>

      <div class="flex">
        <BaseButton ke="/katalog" varian="outline" ukuran="lg" class="flex-1 sm:flex-none">Lihat Semua Produk</BaseButton>
      </div>
    </div>
  </section>

  <!-- Ajakan kontak -->
  <section class="bg-brand py-8 md:py-12">
    <div class="wrap flex flex-col items-start gap-3.5 md:items-center md:text-center">
      <h2 class="text-xl text-white md:text-2xl">Ingin tahu lebih lanjut tentang produk kami?</h2>
      <BaseButton ke="/kontak" varian="terang" ukuran="lg">Kenali Tani Casual</BaseButton>
    </div>
  </section>
</template>
