<script setup>
// Kartu keadaan kosong sesuai desain: ikon dalam kotak biru muda, judul, keterangan, tombol.
// ikon: cari | produk | peringatan
defineProps({
  judul: { type: String, default: 'Produk belum tersedia' },
  pesan: { type: String, default: '' },
  ikon: { type: String, default: 'produk' },
})
</script>

<template>
  <div class="mx-auto flex max-w-md flex-col items-center gap-3 px-4 py-16 text-center">
    <div class="grid h-14 w-14 place-items-center rounded-xl bg-sky-soft text-brand">
      <svg v-if="ikon === 'cari'" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="M20 20l-3.5-3.5" />
      </svg>
      <svg v-else-if="ikon === 'peringatan'" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
      </svg>
      <svg v-else class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 4l-5 3 2 4 3-1v10h8V10l3 1 2-4-5-3a4 4 0 01-8 0z" />
      </svg>
    </div>
    <h2 class="text-xl">{{ judul }}</h2>
    <p v-if="pesan" class="text-sm text-ink">{{ pesan }}</p>
    <div v-if="$slots.default" class="mt-2"><slot /></div>
  </div>
</template>
