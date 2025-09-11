<script setup lang="ts">
import { GuestFeature } from '../vars/vars';

const props = defineProps<{
    features: GuestFeature[]
}>();

</script>

<template>
    <!-- Features -->
    <div class="w-full max-w-7xl mx-auto mt-16 mb-20 px-4 flex flex-col gap-12 bg-white dark:bg-neutral-950">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-center justify-between"
            v-for="(feature, index) in props.features" :key="feature.title">
            <!-- Text Content -->
            <div class="text-left" :class="index % 2 === 0 ? 'lg:order-1' : 'lg:order-2'">
                <h3 class="text-3xl md:text-4xl font-bold text-neutral-900 dark:text-neutral-100 mb-6">
                    {{ feature.title }}
                </h3>
                <p class="text-lg text-neutral-600 dark:text-neutral-300 mb-8 leading-relaxed"
                    v-html="feature.description"></p>
            </div>
            <div class="relative p-4 w-full h-auto" :class="index % 2 === 0 ? 'lg:order-2' : 'lg:order-1'">
                <img v-if="feature.image" :src="feature.image" alt="Smartphone Mockup"
                    class="rounded-4xl max-w-full md:max-w-lg" :class="index % 2 === 0 ? 'ml-auto' : 'mr-auto'">
                <div v-if="feature.iframe" v-html="feature.iframe" class="max-w-full md:max-w-lg"
                    :class="index % 2 === 0 ? 'ml-auto' : 'mr-auto'">
                </div>
            </div>
        </div>
    </div>
</template>