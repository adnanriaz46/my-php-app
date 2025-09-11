<script setup lang="ts">
import {SidebarInset} from '@/components/ui/sidebar';
import {computed} from 'vue';
import UpgradeDialog from "@/components/Upgrade-Dialog.vue";

import ImpersonationBanner from './ui/impersonation-banner/ImpersonationBanner.vue';

interface Props {
  variant?: 'header' | 'sidebar';
  class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);

</script>

<template>
  <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
    <ImpersonationBanner />
    <slot/>
  </SidebarInset>
  <main v-else class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl " :class="className">
    <ImpersonationBanner />
    <slot/>
  </main>
  <UpgradeDialog/>
</template>
