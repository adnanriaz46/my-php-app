<script setup lang="ts">
import { inject, computed } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = withDefaults(defineProps<{
  value: string
  class?: HTMLAttributes['class']
  disabled?: boolean
}>(), {
  disabled: false,
})

const tabs = inject('tabs')

const isSelected = computed(() => tabs?.value?.value === props.value)

const handleClick = () => {
  if (!props.disabled) {
    tabs?.onValueChange(props.value)
  }
}
</script>

<template>
  <button
    :class="
      cn(
        'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
        {
          'bg-background text-foreground shadow-sm': isSelected,
          'hover:bg-background/80': !isSelected,
        },
        props.class,
      )
    "
    :disabled="props.disabled"
    @click="handleClick"
  >
    <slot />
  </button>
</template> 