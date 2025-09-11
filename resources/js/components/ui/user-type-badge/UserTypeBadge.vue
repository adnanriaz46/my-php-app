<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue'
import { Icon } from '@iconify/vue'
import { UserTypes } from "@/types/userTypes";
import { cn } from '@/lib/utils';

const props = defineProps<{
  class?: HTMLAttributes['class'],
  userType: number,
  iconOnly?: boolean
}>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})
</script>

<template>
  <span v-if="userType == UserTypes.ADMIN"
    :class="cn('inline-flex w-fit py-0.5 text-xs font-normal rounded-full shadow-sm text-white bg-red-800 ', !props.iconOnly ? 'px-2' : 'px-1', props.class)">
    <Icon icon="tabler:shield-lock" :height="16" /><span v-if="!iconOnly">&nbsp;Admin</span>
  </span>
  <span v-if="userType == UserTypes.PREMIUM"
    :class="cn('inline-flex w-fit py-0.5 text-xs font-normal rounded-full shadow-sm text-white bg-yellow-600 ', !props.iconOnly ? 'px-2' : 'px-1', props.class)">
    <Icon icon="tabler:diamond" :height="16" /><span v-if="!iconOnly">&nbsp;Premium</span>
  </span>
  <span v-if="userType == UserTypes.FREE"
    :class="cn('inline-flex w-fit py-0.5 text-xs font-normal rounded-full shadow-sm text-white bg-gray-700 ', !props.iconOnly ? 'px-2' : 'px-1', props.class)">
    <Icon icon="tabler:lock" :height="16" /><span v-if="!iconOnly">&nbsp;Free</span>
  </span>
</template>
