<script setup lang="ts">
import {ref, computed} from 'vue'
import {useScroll} from '@vueuse/core'

const props = defineProps<{
  propAddress: string
  propImages: string[]
  openImage: (index: number) => void
}>()

const scrollContainer = ref<HTMLElement | null>(null)

const itemHeight = 300
const overscan = 4
const imagesPerRow = 2

const {y} = useScroll(scrollContainer)

const totalRows = computed(() => Math.ceil(props.propImages.length / imagesPerRow))

const visibleCount = computed(() => {
  if (!scrollContainer.value) return 0
  return Math.ceil(scrollContainer.value.clientHeight / itemHeight) + overscan * 2
})

const startIndex = computed(() => {
  return Math.max(0, Math.floor(y.value / itemHeight) - overscan)
})

const endIndex = computed(() => {
  return Math.min(totalRows.value, startIndex.value + visibleCount.value)
})

const visibleRows = computed(() => {
  const rows = []
  for (let i = startIndex.value; i < endIndex.value; i++) {
    const start = i * imagesPerRow
    const row = props.propImages.slice(start, start + imagesPerRow)
    rows.push(row)
  }
  return rows
})
</script>
<template>
  <div ref="scrollContainer" class="flex-1 h-[calc(100dvh-15px)] relative overflow-auto left-container">
    <div class="w-full" v-if="propImages.length !== 0">
      <img @click="openImage(0)" class="w-full mt-[-2px] rounded-lg"
           :src="propImages[0]" :alt="propAddress"/>
    </div>

    <div class="relative w-full overflow-y-auto mt-1">
      <!-- Phantom spacer: based on number of rows -->
      <div :style="{ height: totalRows * itemHeight + 'px' }"/>

      <!-- Render visible rows -->
      <div
          v-for="(row, rowIndex) in visibleRows"
          :key="'row-' + (startIndex + rowIndex)"
          class="absolute flex gap-2 w-full py-1"
          :style="{
          top: (startIndex + rowIndex) * itemHeight + 'px',
          height: itemHeight + 'px'
        }"
      >
        <div
            v-for="(img, colIndex) in row"
            :key="img + '-' + colIndex"
            class="w-1/2 h-full bg-cover bg-center shadow-md border rounded-lg cursor-pointer"
            :style="{ backgroundImage: `url(${img})` }"
            @click="openImage((startIndex + rowIndex) * 2 + colIndex)"
        />
      </div>
    </div>
  </div>
</template>
<style scoped>
.left-container {
  scrollbar-gutter: stable;
}

</style>
