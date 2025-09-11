<template>
  <nav v-if="props?.links?.length && props?.links?.length > 1" class="flex gap-1">
    <!-- Prev -->
    <Button
      v-if="currentPage > 1"
      variant="outline"
      @click="$emit('navigate', getPreviousUrl())"
      class="px-2 py-1 rounded-l-md border text-sm"
    >
      <Icon icon="tabler:arrow-narrow-left" class="w-4 h-4" />
    </Button>

    <!-- First -->
    <Button
      v-if="shouldShowFirstPage"
      :variant="1 === currentPage ? 'default' : 'outline'"
      @click="$emit('navigate', links?.[numericPageIndexes[0]]?.url)"
      class="px-2 py-1 rounded-md border text-sm"
    >1</Button>

    <span v-if="shouldShowFirstEllipsis" class="px-2 py-1 text-sm">...</span>

    <!-- Dynamic pages -->
    <Button
      v-for="link in visibleLinks"
      :key="link.label"
      :variant="link.active ? 'default' : 'outline'"
      :disabled="!link.url"
      @click="$emit('navigate', link.url)"
      class="px-2 py-1 rounded-md border text-sm"
    >
      <span v-html="link.label" />
    </Button>

    <span v-if="shouldShowLastEllipsis" class="px-2 py-1 text-sm">...</span>

    <!-- Last -->
    <Button
      v-if="shouldShowLastPage"
      :variant="totalPages === currentPage ? 'default' : 'outline'"
      @click="$emit('navigate', links?.[numericPageIndexes.at(-1)]?.url)"
      class="px-2 py-1 rounded-md border text-sm"
    >
      {{ totalPages }}
    </Button>

    <!-- Next -->
    <Button
      v-if="currentPage < totalPages"
      variant="outline"
      @click="$emit('navigate', getNextUrl())"
      class="px-2 py-1 rounded-r-md border text-sm"
    >
      <Icon icon="tabler:arrow-narrow-right" class="w-4 h-4" />
    </Button>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Button from '@/components/ui/button/Button.vue'
import { Icon } from '@iconify/vue'

const props = defineProps<{
  links?: { url: string | null; label: string; active: boolean }[]
}>()

const numericLinks = computed(() =>
  props.links?.filter(link => /^\d+$/.test(link.label)) ?? []
)

const numericPageIndexes = computed<number[]>(() =>
  props.links?.map((l, i) => (/^\d+$/.test(l.label) ? i : -1)).filter(i => i !== -1) ?? []
)

const currentPage = computed(() => {
  const active = props.links?.find(link => link.active)
  return active && /^\d+$/.test(active.label) ? parseInt(active.label) : 1
})

const totalPages = computed(() => numericLinks.value.length)

const visibleLinks = computed(() => {
  const current = currentPage.value
  const total = totalPages.value

  if (total <= 6) return numericLinks.value

  let start = Math.max(1, current - 2)
  let end = Math.min(total, current + 2)

  if (current <= 3) {
    end = 5
  }
  if (current >= total - 2) {
    start = total - 4
  }

  return numericLinks.value.slice(start - 1, end)
})

const shouldShowFirstPage = computed(() => {
  return visibleLinks.value?.[0]?.label !== '1'
})

const shouldShowLastPage = computed(() => {
  return visibleLinks.value?.at(-1)?.label !== totalPages.value.toString()
})

const shouldShowFirstEllipsis = computed(() => {
  const firstLabel = visibleLinks.value?.[0]?.label
  return firstLabel !== '1' && firstLabel !== '2'
})

const shouldShowLastEllipsis = computed(() => {
  const lastLabel = visibleLinks.value?.at(-1)?.label
  const total = totalPages.value
  return lastLabel !== total.toString() && lastLabel !== (total - 1).toString()
})

const getPreviousUrl = () => {
  const currentIdx = props.links?.findIndex(link => link.active) ?? 0
  return props.links?.[currentIdx - 1]?.url ?? null
}

const getNextUrl = () => {
  const currentIdx = props.links?.findIndex(link => link.active) ?? 0
  return props.links?.[currentIdx + 1]?.url ?? null
}
</script>