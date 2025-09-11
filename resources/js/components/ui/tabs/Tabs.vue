<script setup lang="ts">
import { provide, toRef } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = withDefaults(defineProps<{
  defaultValue?: string
  value?: string
  class?: HTMLAttributes['class']
}>(), {
  defaultValue: '',
})

const emit = defineEmits<{
  'update:value': [value: string]
}>()

const value = toRef(props, 'value')

provide('tabs', {
  value,
  onValueChange: (newValue: string) => {
    emit('update:value', newValue)
  },
})
</script>

<template>
  <div :class="cn('', props.class)">
    <slot />
  </div>
</template> 