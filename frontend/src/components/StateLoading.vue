<script setup>
defineProps({ pesan: { type: String, default: 'Memuat...' } })
</script>

<template>
  <div class="flex flex-col items-center justify-center gap-3 py-16 text-ink" role="status" aria-live="polite">
    <svg class="h-8 w-8 animate-spin text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-20" />
      <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
    </svg>
    <p class="text-sm">{{ pesan }}</p>
  </div>
</template>
