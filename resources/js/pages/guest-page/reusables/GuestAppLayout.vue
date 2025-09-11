<script setup lang="ts">
import { GuestHeaderBar } from '@/components/ui/guest-header-bar';
import GuestFooter from './GuestFooter.vue';
import VersionChecker from '@/components/VersionChecker.vue';
import SellsOptionsDialog from '@/components/ui/guest-header-bar/SellsOptionsDialog.vue';
import { ref } from 'vue';
import ImpersonationBanner from '@/components/ui/impersonation-banner/ImpersonationBanner.vue';
import UpgradeDialog from '@/components/Upgrade-Dialog.vue';
import { cn } from '@/lib/utils';

const openSellsOptionDialog = ref<boolean>(false);

const props = defineProps<{
    class?: string;
}>();

const openSellsOptions = () => {
    openSellsOptionDialog.value = true;
}

</script>

<template>
    <!-- Impersonation Banner -->
    <ImpersonationBanner />
    <GuestHeaderBar @openSellsOptions="openSellsOptions" />
    <SellsOptionsDialog v-model:open="openSellsOptionDialog" />
    <UpgradeDialog />
    <div
        :class="cn('relative overflow-hidden -mt-18 pt-25 bg-white dark:bg-neutral-950 h-[calc(100dvh+8px)] overflow-y-auto', props.class)">
        <!-- Main Content -->
        <main class="relative min-h-[calc(100dvh)] w-full ">
            <slot />
            <GuestFooter />
        </main>
    </div>
    <VersionChecker />

</template>