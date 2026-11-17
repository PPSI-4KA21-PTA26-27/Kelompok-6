<script setup>
import { computed } from 'vue'

// Kolom harga bergaya desain: tampil "Rp 200.000", nilai yang disimpan angka murni (string digit).
defineOptions({ inheritAttrs: false })

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Rp Harga' },
  galat: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue', 'terapkan'])

const tampil = computed(() =>
  props.modelValue ? 'Rp ' + Number(props.modelValue).toLocaleString('id-ID') : '',
)

function saatKetik(e) {
  const angka = e.target.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '').slice(0, 10)
  emit('update:modelValue', angka)
  // Pastikan isi kolom mengikuti format walau nilai tidak berubah (mis. huruf dibuang).
  e.target.value = angka ? 'Rp ' + Number(angka).toLocaleString('id-ID') : ''
}
</script>

<template>
  <input
    v-bind="$attrs"
    type="text"
    inputmode="numeric"
    autocomplete="off"
    :value="tampil"
    :placeholder="placeholder"
    class="h-10 w-full rounded-[10px] border bg-white px-3.5 text-sm text-navy placeholder:text-muted focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20"
    :class="galat ? 'border-danger' : 'border-line'"
    :aria-invalid="galat"
    @input="saatKetik"
    @change="emit('terapkan')"
    @keydown.enter.prevent="emit('terapkan')"
  />
</template>
