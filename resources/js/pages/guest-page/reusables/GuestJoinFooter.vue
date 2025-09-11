<script setup lang="ts">
import { router } from '@inertiajs/vue3';

defineProps({
    title: { default: 'Join the elite.' },
    description: { default: 'Access data, deal flow, and decision tools the pros don\'t want you to have.' },
    showRegisterButton: { default: true },
    showChatButton: { default: true },
    registerButtonText: { default: 'Get started for free' },
    chatButtonText: { default: 'Chat with us' },
});

</script>

<template>
    <!--Join-->
    <div class="w-full mt-18 mb-18 px-4 flex flex-col gap-12">
        <div class="w-full max-w-4xl mx-auto text-center">
            <!-- Join the Elite Section -->
            <div class="bg-white dark:bg-neutral-950 py-16">
                <h2 class="text-4xl md:text-5xl font-bold text-neutral-900 dark:text-neutral-100 mb-6">
                    {{ title }}
                </h2>
                <p
                    class="text-lg md:text-xl text-neutral-700 dark:text-neutral-300 mb-10 leading-relaxed max-w-2xl mx-auto">
                    {{ description }}
                </p>

                <!-- Call-to-Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <button v-if="showRegisterButton" @click="router.visit(route('register'))"
                        class="bg-primary hover:bg-primary/90 text-dark font-semibold py-3 px-8 rounded-lg text-base transition-all duration-200 cursor-pointer">
                        {{ registerButtonText }}
                    </button>
                    <button v-if="showChatButton"
                        class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-neutral-900 dark:text-neutral-100 font-semibold py-3 px-8 rounded-lg text-base transition-all duration-200 cursor-pointer">
                        {{ chatButtonText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>