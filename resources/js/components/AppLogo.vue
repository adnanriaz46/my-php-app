<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { pageVars } from '@/pages/guest-page/vars/vars';

const props = withDefaults(defineProps<{
    class?: string;
    size?: 'small' | 'large'; // default is small
}>(), {
    size: 'small',
});

</script>

<template>
    <template v-if="size === 'small'">
        <div :class="props.class" class="flex aspect-square size-8 items-center justify-center rounded-md bg-white">
            <AppLogoIcon class="size-6 fill-current text-white dark:text-black"
                logo-path="site-assets/revamp365-small-logo.png" />
        </div>
        <div class="ml-1 grid flex-1 text-left text-sm">
            <span class="mb-0.5 truncate font-semibold leading-none">Revamp365.ai</span>
        </div>
    </template>
    <template v-else>
        <div :class="props.class" class="flex items-center justify-center rounded-md">
            <img :src="pageVars.logoWhite" class=" hidden dark:block" />
            <img :src="pageVars.logoBlack" class=" block dark:hidden" />
        </div>
    </template>
</template> 
