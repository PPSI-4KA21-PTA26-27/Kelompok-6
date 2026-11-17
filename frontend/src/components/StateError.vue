<script setup>
import StateEmpty from './StateEmpty.vue'
import BaseButton from './BaseButton.vue'

defineProps({
  judul: { type: String, default: 'Gagal memuat halaman' },
  pesan: { type: String, default: 'Terjadi kesalahan saat memuat data. Periksa koneksi Anda lalu coba lagi.' },
  bisaUlang: { type: Boolean, default: true },
})
defineEmits(['ulang'])
</script>

<template>
  <div role="alert">
    <StateEmpty ikon="peringatan" :judul="judul" :pesan="pesan">
      <BaseButton v-if="bisaUlang" ukuran="sm" @click="$emit('ulang')">Muat Ulang</BaseButton>
    </StateEmpty>
  </div>
</template>
