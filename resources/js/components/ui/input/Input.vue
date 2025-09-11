<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'
import PhoneInput from './PhoneInput.vue';

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  placeholder?: string | ''
  class?: HTMLAttributes['class']
  type?: string
  disabled?: boolean
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

const openNativePicker = (e: FocusEvent) => {
  const input = e.target as HTMLInputElement
  const nativeTypes = ['date', 'time', 'datetime-local']
  if (nativeTypes.includes(input.type) && typeof input.showPicker === 'function') {
    input.showPicker()
  }
}

</script>

<template>
  <input v-if="props.type !== 'tel'" :placeholder="placeholder" v-model="modelValue" :type="props.type" data-slot="input"
    @focus="openNativePicker" :disabled="props.disabled" :class="cn(
      'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
      'focus-visible:border focus-visible:border-amber-600 disabled:bg-gray-200 dark:disabled:bg-gray-700',
      'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive shadow-sm',
      props.class,
    )">
  <PhoneInput v-else v-model="modelValue" :placeholder="placeholder" :disabled="props.disabled" :class="props.class" />
</template>
