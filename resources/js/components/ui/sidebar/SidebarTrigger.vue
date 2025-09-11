<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Icon } from '@iconify/vue'
import { useSidebar } from './utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()

const { toggleSidebar, open, isMobile } = useSidebar()

</script>

<template>
  <Button data-sidebar="trigger" data-slot="sidebar-trigger" variant="ghost" size="icon"
    :class="cn('h-7 w-7', props.class)" @click="toggleSidebar">
    <template v-if="isMobile">
      <Icon v-if="open" icon="tabler:menu-2" class="size-6" />
      <Icon v-else icon="tabler:x" class="size-6" />
    </template>
    <template v-else>
      <Icon v-if="open" icon="tabler:circle-chevron-left" class="size-6" />
      <Icon v-else icon="tabler:circle-chevron-right" class="size-6" />
    </template>
    <span class="sr-only">Toggle Sidebar</span>
  </Button>
</template>
