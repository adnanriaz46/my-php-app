<script setup lang="ts">
import {Icon} from '@iconify/vue'
import {useWindowSize} from '@vueuse/core'
import {
  AnimatePresence,
  Motion,
  useMotionValue,
  useMotionValueEvent,
  useTransform
} from 'motion-v'
import {
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle, VisuallyHidden,
} from 'reka-ui'
import {computed, ref, watch, onMounted} from 'vue'
import {useVModel} from '@vueuse/core'

const props = defineProps<{
  modelValue: boolean,
  title?: string,
  description?: string,
}>()

const emit = defineEmits(['update:modelValue'])

const open = useVModel(props, 'modelValue', emit)

const SHEET_MARGIN = 5
const SHEET_RADIUS = 12

const {height, width} = useWindowSize()
const h = computed(() => height.value - SHEET_MARGIN)
const y = useMotionValue(h.value)

const root = ref<HTMLElement | null>(null)

onMounted(() => {
  root.value = document.body.firstElementChild as HTMLElement
  // Sync motion values to body
  useMotionValueEvent(
      useTransform(y, [0, h.value], [(width.value - SHEET_MARGIN) / width.value, 1]),
      'change',
      v => root.value && (root.value.style.scale = `${v}`)
  )
  useMotionValueEvent(
      useTransform(y, [0, h.value], [SHEET_MARGIN - SHEET_RADIUS, 0]),
      'change',
      v => root.value && (root.value.style.translate = `0 ${v}px`)
  )
  useMotionValueEvent(
      useTransform(y, [0, h.value], [SHEET_RADIUS, 0]),
      'change',
      v => root.value && (root.value.style.borderRadius = `${v}px`)
  )
})

const staticTransition = {
  duration: 0.5,
  ease: [0.32, 0.72, 0, 1],
}

</script>
<template>
  <DialogRoot v-model:open="open" class="ring-0 outline-0 ">

    <DialogPortal>
      <AnimatePresence multiple as="div">
        <DialogOverlay as-child>
          <Motion
              class="fixed inset-0 z-10 bg-input/70 backdrop-blur-2xl"
              :initial="{ opacity: 0 }"
              :animate="{ opacity: 1 }"
              :exit="{ opacity: 0 }"
              :transition="staticTransition"
          />
        </DialogOverlay>
        <DialogContent as-child class="top-shadow ring-0 outline-0 max-w-[1400px] mx-auto "
                       aria-describedby="detail full screen dialog">
          <Motion
              class="bg-card dark:bg-stone-900 fixed inset-0 z-20 bottom-0 w-full rounded-t-xl shadow-lg will-change-transform"
              :initial="{ y: h }"
              :animate="{ y: 0 }"
              :exit="{ y: h }"
              :transition="staticTransition"
              :style="{
              y,
              top: `${SHEET_MARGIN}px`,
              paddingBottom: `${height}px`,
            }"
          >
            <div class="outline-hidden mx-auto relative">
              <VisuallyHidden>
                <DialogTitle class="text-3xl font-semibold mb-4">
                  {{ title || 'Modal Sheet' }}
                </DialogTitle>
              </VisuallyHidden>
              <VisuallyHidden>
                <DialogDescription class="text-lg mb-4">
                  {{ description || 'This is a reusable bottom sheet.' }}
                </DialogDescription>
              </VisuallyHidden>
              <div class="text-lg">
                <slot/>
              </div>
              <div class="flex justify-end absolute top-2 right-2">
                <DialogClose
                    class="dark:text-white inline-flex items-center bg-background dark:bg-muted p-2 rounded-full text-sm border border-muted-foreground/30 z-10 cursor-pointer">
                  <Icon icon="lucide:x"/>
                </DialogClose>
              </div>
            </div>
          </Motion>
        </DialogContent>
      </AnimatePresence>
    </DialogPortal>
  </DialogRoot>
</template>
