<script setup>
import { computed } from 'vue'

const props = defineProps({
  // primary | outline | terang | wa | shopee | danger | ghost
  varian: { type: String, default: 'primary' },
  // sm (tinggi 40) | md | lg (tombol utama desain, tinggi 56)
  ukuran: { type: String, default: 'md' },
  ke: { type: [String, Object], default: null }, // rute internal
  href: { type: String, default: null }, // tautan luar
  type: { type: String, default: 'button' },
  blok: { type: Boolean, default: false },
  memuat: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
})

const gaya = {
  primary: 'bg-brand text-white hover:bg-brand-dark',
  outline: 'border border-brand bg-white text-navy hover:bg-mist',
  terang: 'bg-white text-brand hover:bg-mist', // di atas latar biru
  wa: 'bg-wa text-white hover:bg-wa-dark',
  shopee: 'bg-shopee text-white hover:bg-shopee-dark',
  danger: 'bg-danger text-white hover:bg-red-700',
  ghost: 'text-ink hover:bg-mist',
}

const ukuranGaya = {
  sm: 'h-10 px-4 text-sm font-medium',
  md: 'px-6 py-3 text-sm font-semibold',
  lg: 'px-8 py-4 text-base font-semibold',
}

const tag = computed(() => (props.ke ? 'RouterLink' : props.href ? 'a' : 'button'))
const atribut = computed(() => {
  if (props.ke) return { to: props.ke }
  if (props.href) return { href: props.href, target: '_blank', rel: 'noopener noreferrer' }
  return { type: props.type, disabled: props.disabled || props.memuat }
})
</script>

<template>
  <component
    :is="tag"
    v-bind="atribut"
    class="inline-flex items-center justify-center gap-2 rounded-[10px] transition-colors disabled:cursor-not-allowed disabled:opacity-60"
    :class="[gaya[varian], ukuranGaya[ukuran], blok ? 'w-full' : '']"
  >
    <svg v-if="memuat" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
      <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
    </svg>
    <slot />
  </component>
</template>
