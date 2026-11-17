<script setup>
import { ref } from 'vue'
import { formatRupiah } from '../lib/format'

defineProps({
  produk: { type: Object, required: true },
})

const fotoGagal = ref(false)
</script>

<template>
  <RouterLink
    :to="{ name: 'produk', params: { id: produk.id } }"
    class="flex flex-col gap-2.5 rounded-[14px] border border-line bg-white p-3.5 shadow-card transition-transform hover:-translate-y-0.5"
  >
    <div class="h-[200px] overflow-hidden rounded-[10px] bg-sky-soft">
      <img
        v-if="produk.foto_url && !fotoGagal"
        :src="produk.foto_url"
        :alt="produk.nama"
        loading="lazy"
        class="h-full w-full object-cover"
        @error="fotoGagal = true"
      />
      <div v-else class="grid h-full w-full place-items-center text-[13px] text-ink">foto produk</div>
    </div>
    <p class="text-[11px] font-medium uppercase text-ink">{{ produk.kategori }}</p>
    <h3 class="line-clamp-2 min-h-[46px] text-[15px] leading-[23px] font-bold text-navy">{{ produk.nama }}</h3>
    <p class="text-sm font-bold text-brand">{{ formatRupiah(produk.harga) }}</p>
    <span class="flex h-[42px] items-center justify-center rounded-[10px] border border-brand bg-white text-[13px] text-navy">
      Lihat Detail
    </span>
  </RouterLink>
</template>
