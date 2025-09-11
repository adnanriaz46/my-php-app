<script setup lang="ts">

interface SmartCard {
    image: string;
    title: string;
    description: string;
    alt: string;
}

defineProps<{
    mainTitle: string;
    mainDescription: string;
    cards: SmartCard[];
}>();

</script>

<template>
    <div class="w-full py-20 px-4 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                    {{ mainTitle }}
                </h2>
                <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    {{ mainDescription }}
                </p>
            </div>

            <!-- Feature Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Dynamic Cards -->
                <div v-for="(card, index) in cards" :key="index"
                    class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 mb-6 flex items-center justify-center">
                            <img :src="card.image" :alt="card.alt" class="w-full h-full object-contain" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                            {{ card.title }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ card.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom hover effects for cards */
.bg-white:hover,
.dark .bg-gray-800:hover {
    transform: translateY(-2px);
    transition: transform 0.3s ease;
}

/* Ensure icons maintain aspect ratio */
img {
    max-width: 100%;
    height: auto;
}

/* Dark mode specific hover effects */
.dark .bg-gray-800:hover {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

/* Light mode hover effects */
.bg-white:hover {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}
</style> 