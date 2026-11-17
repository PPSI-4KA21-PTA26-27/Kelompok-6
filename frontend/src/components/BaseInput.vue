<script setup>
import { useId } from 'vue'

defineOptions({ inheritAttrs: false })

defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  galat: { type: String, default: '' },
  bantuan: { type: String, default: '' },
  // Bila diisi, tampil sebagai <select> dengan pilihan ini.
  pilihan: { type: Array, default: null },
  placeholderPilihan: { type: String, default: 'Pilih' },
  area: { type: Boolean, default: false }, // <textarea>
})
defineEmits(['update:modelValue'])

const id = useId()
const dasar =
  'w-full rounded-[10px] border bg-white px-3.5 py-2.5 text-sm text-navy placeholder:text-muted focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20'
</script>

<template>
  <div class="space-y-1.5">
    <label v-if="label" :for="id" class="block text-sm font-medium text-navy">{{ label }}</label>

    <select
      v-if="pilihan"
      :id="id"
      v-bind="$attrs"
      :value="modelValue"
      :class="[dasar, galat ? 'border-danger' : 'border-line']"
      :aria-invalid="!!galat"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <option value="">{{ placeholderPilihan }}</option>
      <option v-for="p in pilihan" :key="p" :value="p">{{ p }}</option>
    </select>

    <textarea
      v-else-if="area"
      :id="id"
      v-bind="$attrs"
      :value="modelValue"
      rows="4"
      :class="[dasar, galat ? 'border-danger' : 'border-line']"
      :aria-invalid="!!galat"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <input
      v-else
      :id="id"
      v-bind="$attrs"
      :value="modelValue"
      :class="[dasar, galat ? 'border-danger' : 'border-line']"
      :aria-invalid="!!galat"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <p v-if="galat" class="text-xs font-medium text-danger" role="alert">(!) {{ galat }}</p>
    <p v-else-if="bantuan" class="text-xs text-muted">{{ bantuan }}</p>
  </div>
</template>
