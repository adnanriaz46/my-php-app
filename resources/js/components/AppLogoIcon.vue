<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
    logoPath: string; // Use string type
}

const props = defineProps<Props>();

// Get the site URL (Laravel helper exposes this via env or you can hardcode)
const siteUrl = import.meta.env.VITE_APP_URL || window.location.origin;

// Combine full URL with logo path
const logoSrc = `${siteUrl}/${props.logoPath}`;
</script>

<template>
    <img :src="logoSrc" :class="className" v-bind="$attrs" alt="Logo" />
</template>
