<script setup>
import { watch, ref, nextTick } from 'vue'
import BaseButton from './BaseButton.vue'

const props = defineProps({
  buka: { type: Boolean, default: false },
  judul: { type: String, default: 'Hapus produk?' },
  pesan: { type: String, default: 'Produk yang dihapus tidak dapat dikembalikan.' },
  tombolYa: { type: String, default: 'Ya, hapus' },
  tombolBatal: { type: String, default: 'Batal' },
  memuat: { type: Boolean, default: false },
})
const emit = defineEmits(['konfirmasi', 'batal'])

const tombolBatalRef = ref(null)
watch(
  () => props.buka,
  async (nilai) => {
    if (nilai) {
      await nextTick()
      tombolBatalRef.value?.$el?.focus()
    }
  },
)
</script>

<template>
  <Teleport to="body">
    <div
      v-if="buka"
      class="fixed inset-0 z-50 grid place-items-center bg-navy/60 p-4"
      @click.self="emit('batal')"
      @keydown.esc="emit('batal')"
    >
      <div role="alertdialog" aria-modal="true" aria-labelledby="judul-modal" class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
        <h2 id="judul-modal" class="text-lg">{{ judul }}</h2>
        <p class="mt-2 text-sm text-ink">{{ pesan }}</p>
        <div class="mt-6 flex justify-end gap-3">
          <BaseButton ref="tombolBatalRef" varian="outline" :disabled="memuat" @click="emit('batal')">{{ tombolBatal }}</BaseButton>
          <BaseButton varian="danger" :memuat="memuat" @click="emit('konfirmasi')">{{ tombolYa }}</BaseButton>
        </div>
      </div>
    </div>
  </Teleport>
</template>
