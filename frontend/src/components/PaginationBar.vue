<script setup>
import { computed } from 'vue'

const props = defineProps({
  halaman: { type: Number, required: true },
  terakhir: { type: Number, required: true },
})
const emit = defineEmits(['pindah'])

// Tampilkan maksimal 5 nomor di sekitar halaman aktif.
const nomor = computed(() => {
  const awal = Math.max(1, Math.min(props.halaman - 2, props.terakhir - 4))
  const akhir = Math.min(props.terakhir, awal + 4)
  return Array.from({ length: akhir - awal + 1 }, (_, i) => awal + i)
})

const dasar = 'grid h-10 min-w-10 place-items-center rounded-[10px] border px-3 text-sm transition-colors'
</script>

<template>
  <nav v-if="terakhir > 1" class="flex flex-wrap items-center justify-center gap-2" aria-label="Halaman katalog">
    <button
      type="button"
      :class="[dasar, 'border-line bg-white text-navy hover:bg-mist disabled:cursor-not-allowed disabled:opacity-50']"
      :disabled="halaman <= 1"
      @click="emit('pindah', halaman - 1)"
    >Sebelumnya</button>

    <button
      v-for="n in nomor"
      :key="n"
      type="button"
      :class="[dasar, n === halaman ? 'border-brand bg-brand text-white' : 'border-line bg-white text-navy hover:bg-mist']"
      :aria-current="n === halaman ? 'page' : undefined"
      @click="emit('pindah', n)"
    >{{ n }}</button>

    <button
      type="button"
      :class="[dasar, 'border-line bg-white text-navy hover:bg-mist disabled:cursor-not-allowed disabled:opacity-50']"
      :disabled="halaman >= terakhir"
      @click="emit('pindah', halaman + 1)"
    >Berikutnya</button>
  </nav>
</template>
