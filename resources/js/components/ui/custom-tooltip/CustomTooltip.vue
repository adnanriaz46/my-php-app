<template>
  <div class="relative group">
    <slot/>
    <div
        class="absolute z-50 px-2 py-1 text-xs text-black/90 bg-primary rounded-md shadow opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center "
        :class="cn(positionClasses)"
    >
      {{ String(text ?? '') }}
    </div>
  </div>
</template>

<script setup lang="ts">
import {computed} from "vue";
import {cn} from "@/lib/utils";

const props = defineProps<{
  text: any
  position?: "bottom" | 'left' | 'right' | 'top'
}>()

const positionClasses = computed(() => {
  switch (props.position) {
    case 'bottom':
      return 'bottom-full left-1/2 -translate-x-1/2 mb-1'
    case 'left':
      return 'right-full top-1/2 -translate-y-1/2 mr-1'
    case 'right':
      return 'left-full top-1/2 -translate-y-1/2 ml-1'
    default:
      return 'top-full left-1/2 -translate-x-1/2 mt-1'
  }
})

</script>

<style scoped>
.group:hover .group-hover\:opacity-100 {
  opacity: 1;
}
</style>
