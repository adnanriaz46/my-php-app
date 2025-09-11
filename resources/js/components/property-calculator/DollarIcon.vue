<script setup lang="ts">
import {Icon} from "@iconify/vue";


const props = defineProps<{
  value: boolean
}>()
</script>

<template>
  <div class="w-fit h-fit my-auto flex">
    <Icon v-if="props.value" icon="tabler:plus" class="size-3 my-auto bottom-0 right-0 text-green-600"/>
    <Icon v-if="!props.value" icon="tabler:minus" class="size-3  my-auto bottom-0 right-0 text-red-600"/>
    <Icon icon="tabler:currency-dollar" :class="'size-4 do-icon ' + (props.value ? 'text-green-600' : 'text-red-600')"/>
  </div>
</template>

<style scoped>
.do-icon{
  margin-left: -5px;
}
</style>
