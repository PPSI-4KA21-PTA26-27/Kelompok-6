<script setup>
import { useRoute } from 'vue-router'
import LogoTani from './LogoTani.vue'

const route = useRoute()

const menu = [
  { nama: 'Beranda', ke: '/' },
  { nama: 'Katalog', ke: '/katalog' },
  { nama: 'Kontak', ke: '/kontak' },
]

function aktif(ke) {
  if (ke === '/') return route.path === '/'
  // Detail produk (/produk/:id) termasuk bagian Katalog.
  if (ke === '/katalog') return route.path.startsWith('/katalog') || route.path.startsWith('/produk')
  return route.path.startsWith(ke)
}
</script>

<template>
  <header class="bg-ice shadow-header">
    <nav
      class="wrap flex flex-col gap-4 py-4 md:h-[76px] md:flex-row md:items-center md:justify-between md:py-0"
      aria-label="Navigasi utama"
    >
      <RouterLink to="/" aria-label="Tani Casual, ke beranda" class="self-start"><LogoTani /></RouterLink>

      <ul class="flex items-center gap-2">
        <li v-for="m in menu" :key="m.ke">
          <RouterLink
            :to="m.ke"
            class="block rounded-full px-4 py-2 text-sm transition-colors"
            :class="aktif(m.ke) ? 'bg-brand text-white' : 'border border-line bg-white text-navy hover:bg-mist'"
            :aria-current="aktif(m.ke) ? 'page' : undefined"
          >{{ m.nama }}</RouterLink>
        </li>
      </ul>
    </nav>
  </header>
</template>
