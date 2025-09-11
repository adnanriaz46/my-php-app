<script setup lang="ts">


const props = defineProps<{
  title: string,
  subTitle?: string | null,
  class?: string | null,
}>();
</script>
  
<template>
  <div :class="props.class" class="font-semibold text-xl px-4 py-2">
    {{ props.title }}
    <div v-if="subTitle" class="font-normal text-base text-muted-foreground">{{ props.subTitle }}</div>
  </div>
</template>

<style scoped>

</style>
