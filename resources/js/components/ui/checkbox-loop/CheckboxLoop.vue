<script setup lang="ts">
import type {CheckboxRootEmits, CheckboxRootProps} from 'reka-ui'
import {cn} from '@/lib/utils'
import {Check} from 'lucide-vue-next'
import {CheckboxIndicator, CheckboxRoot, useForwardPropsEmits} from 'reka-ui'
import {computed, type HTMLAttributes} from 'vue'

const props = defineProps<{
  modelValue: any
  value?: any
  id?: string
  class?: HTMLAttributes['class']
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', value: any): void
}>()

// Determine if this checkbox is checked
const isChecked = computed(() => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue.includes(props.value)
  }
  return props.modelValue
})

function toggle() {
  if (Array.isArray(props.modelValue)) {
    const newValue = props.modelValue.includes(props.value)
        ? props.modelValue.filter((v: any) => v !== props.value)
        : [...props.modelValue, props.value]
    emits('update:modelValue', newValue)
  } else {
    emits('update:modelValue', !props.modelValue)
  }
}
</script>

<template>
  <CheckboxRoot
      data-slot="checkbox"
      :id="props.id"
      :model-value="isChecked"
      @click="toggle"
      :class="cn(
    'peer border-input data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:border-primary focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive size-4 shrink-0 rounded-[4px] border shadow-xs transition-shadow outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50',
    props.class
  )"
  >
    <CheckboxIndicator
        data-slot="checkbox-indicator"
        class="flex items-center justify-center text-current transition-none"
    >
      <slot>
        <Check class="size-3.5 " />
      </slot>
    </CheckboxIndicator>
  </CheckboxRoot>
</template>
