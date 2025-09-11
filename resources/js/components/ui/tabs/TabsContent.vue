<script setup lang="ts">
import { inject, computed } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = withDefaults(defineProps<{
  value: string
  class?: HTMLAttributes['class']
}>(), {})

const tabs = inject('tabs')

const isSelected = computed(() => tabs?.value?.value === props.value)
</script>

<template>
  <div
    v-show="isSelected"
    :class="
      cn(
        'mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
        props.class,
      )
    "
  >
    <slot />
  </div>
</template> 