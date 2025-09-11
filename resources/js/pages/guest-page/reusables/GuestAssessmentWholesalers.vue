<script setup lang="ts">
// import { router } from '@inertiajs/vue3';

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
                            Learn how it works and how you can capitalize
                        </h2>
                        <p class="text-base md:text-lg text-white/90 mb-8 leading-relaxed">
                            Learn more about posting your wholesale deals and marketing them through Revamp 365 . Access training videos and see it in action
                        </p>

                        <a :href="route('learn.index')" target="_blank" rel="noopener"
                            class="bg-white cursor-pointer text-black font-semibold py-3 px-6 rounded-lg text-base transition-all duration-200 hover:bg-gray-100 hover:shadow-lg inline-block">
                            See in action
                        </a>
                    </div>

                    <!-- Right Side - Dashboard Illustration -->
                    <div class="flex justify-center lg:justify-end">
                        <img src="https://revamp365-storage.s3.amazonaws.com/assets/assets/687fc5d963da3.svg"
                            alt="Dashboard Illustration" class="max-w-md h-auto">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
