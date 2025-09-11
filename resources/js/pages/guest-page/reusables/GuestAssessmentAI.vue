<script setup lang="ts">
import { router } from '@inertiajs/vue3';

</script>

<template>
    <!--Assessments-->
    <div class="w-full mt-20 mb-20 px-4 flex flex-col gap-12">
        <div class="w-full max-w-7xl mx-auto">
            <!-- Assessment Banner -->
            <div class="bg-gradient-to-r from-amber-800 via-amber-600 to-primary rounded-4xl p-8 md:p-12 shadow-xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <!-- Left Side - Text Content -->
                    <div class="text-white">
                        <h2 class="text-2xl md:text-3xl font-bold mb-4 leading-tight">
                            Meet Your AI Investment Assistant
                        </h2>
                        <p class="text-base md:text-lg text-white/90 mb-8 leading-relaxed">
                            Talk to Revamp365 like you’d talk to a coach.
                            Just tell our voice-powered AI where you buy, what your strategy is, and what your goals are
                            — and she’ll tell you exactly how to use the platform to find better deals, faster.
                        </p>
                        <p class="text-base md:text-lg text-white/90 mb-8 leading-relaxed">
                            No guessing. No digging. Just real guidance in real time.
                        </p>
                        <button @click="router.visit(route('guest.ai_onboarding'))"
                            class="bg-white cursor-pointer text-black font-semibold py-3 px-6 rounded-lg text-base transition-all duration-200 hover:bg-gray-100 hover:shadow-lg">
                            Start Your Free Assessment
                        </button>
                    </div>

                    <!-- Right Side - Dashboard Illustration -->
                    <div class="flex justify-center lg:justify-end">
                        <img src="https://revamp365-storage.s3.amazonaws.com/assets/assets/6881087e10601.png"
                            alt="Dashboard Illustration" class="max-w-sm h-auto rounded-4xl">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>