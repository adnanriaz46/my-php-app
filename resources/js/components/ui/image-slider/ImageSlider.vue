<script setup lang="ts">
import {ref, computed} from 'vue'
import {useIntervalFn} from '@vueuse/core'
import {Motion} from 'motion-v'
import {Button} from '@/components/ui/button'
import {Icon} from '@iconify/vue'
import {FullScreenImageSlider} from "@/components/ui/image-slider/index";

const props = defineProps<{
  images: string[]
  autoPlay?: boolean
  interval?: number
}>()

const currentIndex = ref(0)
const total = computed(() => props.images.length)

const next = () => {
  currentIndex.value = (currentIndex.value + 1) % total.value
}

const prev = () => {
  currentIndex.value = (currentIndex.value - 1 + total.value) % total.value
}

// Optional: Auto play
if (props.autoPlay) {
  useIntervalFn(next, props.interval || 5000)
}

const selectedImageIndex = ref<number>(0);
const imageSliderOpen = ref<boolean>(false);
const openFullScreenImage = (index: number = 0) => {
  selectedImageIndex.value = index;
  imageSliderOpen.value = true;

}


</script>

<template>
  <FullScreenImageSlider v-if="imageSliderOpen"
                         v-model:open="imageSliderOpen"
                         :images="images" :index="selectedImageIndex"/>
  <div class="relative w-full mx-auto rounded-xl overflow-hidden shadow-lg">
    <div class="relative h-64 bg-black">
      <Motion
          v-for="(img, index) in images"
          :key="index"
          class="absolute inset-0 transition-all duration-500 pointer-events-none"
          :initial="{ opacity: 0 }"
          :animate="{ opacity: currentIndex === index ? 1 : 0 }"
      >
        <img
            v-if="currentIndex === index"
            :src="img"
            @click="openFullScreenImage(index)"
            class="object-cover w-full h-full pointer-events-auto"
            :alt="img"
        />
      </Motion>

    </div>

    <div class="absolute inset-y-0 left-0 flex items-center px-2">
      <Button size="icon" variant="outline" @click="prev">
        <Icon icon="tabler:chevron-left" class="size-6"/>
      </Button>
    </div>
    <div class="absolute inset-y-0 right-0 flex items-center px-2">
      <Button size="icon" variant="outline" @click="next">
        <Icon icon="tabler:chevron-right" class="size-6"/>
      </Button>
    </div>

    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex space-x-1">
      <Button variant="outline">{{ currentIndex + 1 }} of {{ images.length }}</Button>

    </div>
  </div>
</template>
