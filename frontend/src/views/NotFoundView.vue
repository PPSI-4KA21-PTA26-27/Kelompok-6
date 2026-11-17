<script setup>
import StateEmpty from '../components/StateEmpty.vue'
import BaseButton from '../components/BaseButton.vue'
</script>

<template>
  <div class="mx-auto max-w-6xl px-4 sm:px-6">
    <StateEmpty judul="Halaman tidak ditemukan" pesan="Alamat yang Anda buka tidak tersedia atau sudah dipindahkan.">
      <BaseButton ke="/">Kembali ke beranda</BaseButton>
    </StateEmpty>
  </div>
</template>
