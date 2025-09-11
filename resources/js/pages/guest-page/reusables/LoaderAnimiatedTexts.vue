<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { ref, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps<{
    texts: string[]
}>()

const currentIndex = ref(0);
const isVisible = ref(false);
let intervalId: number | null = null;

const startAnimation = () => {
    stopAnimation(); // Clear any existing interval
    currentIndex.value = 0;
    isVisible.value = true;
    intervalId = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % props.texts.length;
    }, 800);
};

const stopAnimation = () => {
    if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
    }
};

// Watch for changes in texts prop
watch(() => props.texts, (newTexts) => {
    if (newTexts && newTexts.length > 0) {
        startAnimation();
    } else {
        stopAnimation();
        isVisible.value = false;
    }
}, { immediate: true });

onMounted(() => {
    if (props.texts && props.texts.length > 0) {
        startAnimation();
    }
});

onUnmounted(() => {
    stopAnimation();
});
</script>

<template>
    <div class="flex flex-col gap-2">
        <div v-if="isVisible && texts.length > 0" 
             :key="texts.join('')"
             class="flex max-w-fit text-sm gap-2 px-2 py-1 rounded-lg text-muted-foreground animate-fade-in">
            <Icon icon="tabler:loader-2" class="animate-spin size-5 min-w-5" />
            {{ texts[currentIndex] }}
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>