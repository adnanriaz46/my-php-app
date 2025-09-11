<script setup lang="ts">
import { ref, watch } from 'vue'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
  modelValue?: string | number
  placeholder?: string
  disabled?: boolean
  class?: string
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const inputValue = useVModel(props, 'modelValue', emits, {
  passive: true,
})

const formatPhone = (val: string): string => {
  const digits = val.replace(/\D/g, '')

  // If starts with +1 or 1, format as US number
  if (digits.length >= 11 && digits.startsWith('1')) {
    const country = digits.slice(0, 1)
    const area = digits.slice(1, 4)
    const prefix = digits.slice(4, 7)
    const line = digits.slice(7, 11)
    return `+${country} (${area}) ${prefix} ${line}`.trim()
  }

  // Format 10-digit number
  if (digits.length <= 10) {
    const area = digits.slice(0, 3)
    const prefix = digits.slice(3, 6)
    const line = digits.slice(6, 10)
    let result = ''
    if (area) result += `(${area}`
    if (prefix) result += `) ${prefix}`
    if (line) result += ` ${line}`
    return result.trim()
  }

  return val // fallback
}

const onInput = (e: Event) => {
  const target = e.target as HTMLInputElement
  const raw = target.value
  inputValue.value = raw.replace(/\D/g, '') // only store digits
}

const displayValue = ref('')

watch(inputValue, (val) => {
  displayValue.value = formatPhone(String(val ?? ''))
}, { immediate: true })
</script>

<template>
  <input
    type="tel"
    :placeholder="placeholder"
    :value="displayValue"
    @input="onInput"
    :disabled="disabled"
    :class="['file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border focus-visible:border-amber-600 disabled:bg-gray-200 dark:disabled:bg-gray-700 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive shadow-sm', props.class]"
  />
</template>