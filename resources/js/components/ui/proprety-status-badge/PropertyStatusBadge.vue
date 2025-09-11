<script setup lang="ts">
import {computed, type HTMLAttributes} from 'vue'
import {Icon} from '@iconify/vue'
import {UserTypes} from "@/types/userTypes";
import {PropertyStatus} from "@/types/property";

const props = defineProps<{
  class?: HTMLAttributes['class'],
  status: string | null
}>()

const delegatedProps = computed(() => {
  const {class: _, ...delegated} = props

  return delegated
})
</script>

<template>
  <span v-if="status == PropertyStatus.Active"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-active"
        :class="props.class">{{
      PropertyStatus.Active
    }}</span>

  <span v-else-if="status == PropertyStatus.ActiveUnderContract"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-active-uc"
        :class="props.class">{{
      PropertyStatus.ActiveUnderContract
    }}</span>

  <span v-else-if="status == PropertyStatus.Canceled"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-canceled"
        :class="props.class">{{
      PropertyStatus.Canceled
    }}</span>

  <span v-else-if="status == PropertyStatus.Closed"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-closed"
        :class="props.class">{{
      PropertyStatus.Closed
    }}</span>

  <span v-else-if="status == PropertyStatus.ComingSoon"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-coming-soon"
        :class="props.class">{{
      PropertyStatus.ComingSoon
    }}</span>

  <span v-else-if="status == PropertyStatus.Pending"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-pending"
        :class="props.class">{{
      PropertyStatus.Pending
    }}</span>

  <span v-else-if="status == PropertyStatus.TempOffMarket"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-tempoff"
        :class="props.class">{{
      PropertyStatus.TempOffMarket
    }}</span>

  <span v-else-if="status == PropertyStatus.Withdrawn"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-withdrawn"
        :class="props.class">{{
      PropertyStatus.Withdrawn
    }}</span>

  <span v-else-if="status == PropertyStatus.Expired"
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-withdrawn"
        :class="props.class">{{
      PropertyStatus.Expired
    }}</span>

  <span v-else
        class="inline-flex w-fit px-2 py-0.5 text-xs font-normal rounded-full shadow-sm prop-withdrawn"
        :class="props.class">Unknown</span>
</template>
