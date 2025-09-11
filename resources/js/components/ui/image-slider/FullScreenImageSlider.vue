<script setup lang="ts">
import {ref, watch} from 'vue'
import {useVModel, useEventListener} from '@vueuse/core'
import {
  DialogTitle,
  DialogDescription,
  DialogRoot,
  DialogPortal,
  DialogOverlay,
  DialogContent,
  DialogClose, VisuallyHidden,
} from 'reka-ui'
import {Button} from '@/components/ui/button'
import {Icon} from '@iconify/vue';
import Label from "@/components/property-thumb/MapUnlistedPropertyThumb.vue";

const props = defineProps<{
  open: boolean,
  images: string[],
  index?: number
}>()

const emit = defineEmits(['update:open'])

const MIN_ZOOM = 1
const MAX_ZOOM = 3

const isOpen = useVModel(props, 'open', emit)
const currentIndex = ref(props.index ?? 0)

const next = () => {
  resetZoom();
  currentIndex.value = (currentIndex.value + 1) % props.images.length
}

const prev = () => {
  resetZoom();
  currentIndex.value =
      (currentIndex.value - 1 + props.images.length) % props.images.length
}

// Keyboard navigation support
useEventListener('keydown', (e) => {
  if (!isOpen.value) return
  if (e.key === 'ArrowRight') next()
  else if (e.key === 'ArrowLeft') prev()
  else if (e.key === 'Escape') isOpen.value = false
})

// Reset index when opening
watch(isOpen, (val) => {
  if (val) {
    currentIndex.value = 0
    resetZoom()
  }
})

const zoom = ref(1)
const pan = ref({x: 0, y: 0})
const isDragging = ref(false)
const dragStart = ref({x: 0, y: 0})

const resetZoom = () => {
  zoom.value = MIN_ZOOM
  pan.value = {x: 0, y: 0}
}

const onWheel = (e: WheelEvent) => {
  // e.preventDefault()
  zoom.value += e.deltaY < 0 ? 0.1 : -0.1
  zoom.value = Math.max(MIN_ZOOM, Math.min(zoom.value, MAX_ZOOM)) // clamp between 1x and 5x
}

const onMouseDown = (e: MouseEvent) => {
  isDragging.value = true
  dragStart.value = {x: e.clientX - pan.value.x, y: e.clientY - pan.value.y}
}

const onMouseMove = (e: MouseEvent) => {
  if (!isDragging.value) return
  pan.value = {
    x: e.clientX - dragStart.value.x,
    y: e.clientY - dragStart.value.y,
  }
}

const onMouseUp = () => {
  isDragging.value = false
}

const lastTouchDistance = ref<number | null>(null)

const getTouchDistance = (touches: TouchList): number => {
  const [touch1, touch2] = [touches[0], touches[1]]
  const dx = touch2.clientX - touch1.clientX
  const dy = touch2.clientY - touch1.clientY
  return Math.sqrt(dx * dx + dy * dy)
}

const onTouchStart = (e: TouchEvent) => {
  if (e.touches.length === 2) {
    e.preventDefault();
    lastTouchDistance.value = getTouchDistance(e.touches)
  } else if (e.touches.length === 1) {
    isDragging.value = true
    dragStart.value = {
      x: e.touches[0].clientX - pan.value.x,
      y: e.touches[0].clientY - pan.value.y
    }
  }
}

const onTouchMove = (e: TouchEvent) => {
  if (e.touches.length === 2) {
    e.preventDefault();
    const currentDistance = getTouchDistance(e.touches)
    if (lastTouchDistance.value !== null) {
      const delta = currentDistance - lastTouchDistance.value
      zoom.value += delta / 150 // adjust sensitivity
      zoom.value = Math.max(MIN_ZOOM, Math.min(zoom.value, MAX_ZOOM))
    }
    lastTouchDistance.value = currentDistance
  } else if (e.touches.length === 1 && isDragging.value) {
    pan.value = {
      x: e.touches[0].clientX - dragStart.value.x,
      y: e.touches[0].clientY - dragStart.value.y,
    }
  }
}

const onTouchEnd = (e: TouchEvent) => {
  isDragging.value = false
  if (e.touches.length < 2) {
    lastTouchDistance.value = null
  }
}

</script>

<template>
  <DialogRoot v-model:open="isOpen">
    <DialogPortal>
      <DialogOverlay class="fixed inset-0 bg-black/80 backdrop-blur-2xl z-40"/>

      <DialogContent
          class="fixed inset-0 z-50 flex items-center justify-center px-4"
          style="max-width: none"
      >
        <VisuallyHidden>
          <DialogTitle>Image Viewer</DialogTitle>
        </VisuallyHidden>
        <VisuallyHidden>
          <DialogDescription>Fullscreen image slider with navigation</DialogDescription>
        </VisuallyHidden>

        <div class="relative h-[100dvh] w-screen overflow-hidden">

          <div class="w-full h-full cursor-grab "
               @wheel.passive="onWheel"
               @mousedown="onMouseDown"
               @mousemove="onMouseMove"
               @mouseup="onMouseUp"
               @mouseleave="onMouseUp"
               @touchstart="onTouchStart"
               @touchmove="onTouchMove"
               @touchend="onTouchEnd"
               @touchcancel="onTouchEnd"
          >
            <img
                :alt="currentIndex.toString()"
                :src="images[currentIndex]"
                class="select-none pointer-events-none h-full w-full"
                :style="{
                transform: `scale(${zoom}) translate(${pan.x / zoom}px, ${pan.y / zoom}px)`,
                transition: isDragging ? 'none' : 'transform 0.2s ease-out',
                'object-fit': 'contain',
              }"/>
          </div>

          <!-- Zoom controls -->
          <div class="absolute bottom-4 right-4 space-x-2 flex">
            <div class="my-auto px-2 bg-input/50 py-1 rounded-lg">{{ currentIndex + 1 }}/{{ images.length }}</div>
            <Button variant="secondary" @click="zoom = Math.min(zoom + 0.1, MAX_ZOOM)">+</Button>
            <Button variant="secondary" @click="zoom = Math.max(zoom - 0.1, MIN_ZOOM)">−</Button>
            <Button variant="secondary" @click="resetZoom">Reset</Button>
          </div>

          <!-- Close -->
          <DialogClose as-child>
            <Button
                class="absolute top-4 right-4"
                variant="secondary"
                size="icon"
                aria-label="Close"
            >
              <Icon icon="tabler:x" class="size-6"/>
            </Button>
          </DialogClose>

          <!-- Navigation -->
          <Button
              class="absolute left-4 top-1/2 -translate-y-1/2"
              variant="secondary"
              size="icon"
              @click="prev"
              :disabled="images.length === 1"
          >
            <Icon icon="tabler:chevron-left" class="size-6"/>
          </Button>
          <Button
              class="absolute right-4 top-1/2 -translate-y-1/2"
              variant="secondary"
              size="icon"
              @click="next"
              :disabled="images.length === 1"
          >
            <Icon icon="tabler:chevron-right" class="size-6"/>
          </Button>
        </div>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>
