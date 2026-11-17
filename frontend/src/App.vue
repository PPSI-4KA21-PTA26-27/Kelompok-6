<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AppNavbar from './components/AppNavbar.vue'
import AppFooter from './components/AppFooter.vue'

const route = useRoute()
// Login admin tampil polos tanpa navbar/footer publik.
const polos = computed(() => route.meta.polos === true)
// Area admin punya layout sendiri (dibuat di Sprint 3).
const admin = computed(() => route.meta.admin === true)
</script>

<template>
  <div class="flex min-h-screen flex-col bg-white">
    <AppNavbar v-if="!polos && !admin" />
    <main class="flex-1">
      <RouterView />
    </main>
    <AppFooter v-if="!polos && !admin" />
  </div>
</template>
