<script setup lang="ts">
import Button from "@/components/ui/button/Button.vue";

const props = defineProps<{
  onApply: () => void
  onClear: () => void
}>()
</script>

<template>
  <div class="flex flex-row gap-2 mt-2">
    <Button @click="props.onClear" class="flex-1" variant="secondary">Clear</Button>
    <Button @click="props.onApply" class="flex-1" variant="default">Apply</Button>
  </div>
</template>
