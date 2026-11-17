<script setup>
import { useRoute } from 'vue-router'
import StateEmpty from '../components/StateEmpty.vue'
import BaseButton from '../components/BaseButton.vue'

const route = useRoute()
</script>

<template>
  <div class="mx-auto max-w-6xl px-4 sm:px-6">
    <StateEmpty :judul="route.meta.judul" pesan="Halaman ini sedang dikerjakan.">
      <BaseButton ke="/" varian="outline">Kembali ke beranda</BaseButton>
    </StateEmpty>
  </div>
</template>
