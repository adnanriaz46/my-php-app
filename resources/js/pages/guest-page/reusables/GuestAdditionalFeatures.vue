<script setup lang="ts">
import AdditionalFeatureMetricsBar from './AdditionalFeatureMetricsBar.vue';

</script>

<template>
    <!--Additional Features-->
    <div class="w-full  mt-20 mb-20 px-4 flex flex-col gap-12 bg-gray-800 pb-10">
        <!-- Data Analytics Section -->
        <div class="w-full max-w-7xl mx-auto -mt-15">

            <AdditionalFeatureMetricsBar />

            <!-- Main Content Grid -->
            <div class="flex flex-col lg:flex-row items-center justify-center">
                <!-- Smartphone Mockup -->
                <div class="relative">
                    <img src="https://revamp365-storage.s3.amazonaws.com/assets/assets/687fbfebd30ee.png"
                        alt="Smartphone Mockup" class="max-w-xl h-auto">
                </div>
                <!-- Text Boxes -->
                <div class="space-y-6">
                    <!-- Text Box 1 -->
                    <div
                        class="border-2 border-primary/50 rounded-lg p-6 bg-gradient-to-r from-primary/5 to-primary/10 lg:max-w-lg">
                        <h3 class="text-xl font-bold text-white  mb-3">
                            Efficiency + Analytics = Deal Magnet
                        </h3>
                        <p class="text-white/80 leading-relaxed">
                            Imagine you had the capacity to comp EVERY property that comes available for sale,
                            then simply cherry pick the ones you want to flip, rent, or wholesale.
                        </p>
                    </div>

                    <!-- Text Box 2 -->
                    <div
                        class="border-2 border-primary/50 rounded-lg p-6 bg-gradient-to-r from-primary/5 to-primary/10 lg:max-w-lg lg:ml-18">
                        <h3 class="text-xl font-bold text-white mb-3">
                            Big Data, Big Analytics
                        </h3>
                        <p class="text-white/80 leading-relaxed">
                            As soon as a new MLS listing or wholesale deal is available, our data algorithm
                            finds rental comps, flip comps, as is comps, and assigns values.
                        </p>
                    </div>

                    <!-- Text Box 3 -->
                    <div
                        class="border-2 border-primary/50 rounded-lg p-6 bg-gradient-to-r from-primary/5 to-primary/10 lg:max-w-lg">
                        <h3 class="text-xl font-bold text-white mb-3">
                            Best of Both Worlds
                        </h3>
                        <p class="text-white/80 leading-relaxed">
                            Our advanced filtering is not only going to save you hundreds of hours a month, but
                            it's THE best tool to find the needle in the haystack and grow your portfolio.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>