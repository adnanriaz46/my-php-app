<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { pageVars } from '@/pages/guest-page/vars/vars';

</script>

<template>
    <!--Footer-->
    <footer class="w-full bg-gray-800 relative overflow-hidden bg-cover bg-center"
        :style="{ backgroundImage: `url(${pageVars.bgFooter})` }">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0"
                style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.1) 1px, transparent 0); background-size: 20px 20px;">
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 py-12">
            <!-- Top Row: Logo, Navigation, Social Media -->
            <div class="flex flex-col lg:flex-row items-center justify-between mb-12">
                <!-- Logo -->
                <div class="mb-6 lg:mb-0">
                    <img :src="pageVars.logoWhite" alt="Revamp365.ai" class="h-auto w-50 md:max-w-50 md:w-auto" />
                </div>

                <!-- Navigation Links -->
                <nav class="mb-6 lg:mb-0">
                    <ul class="flex flex-wrap justify-center gap-6 lg:gap-8">
                        <li><a href="#"
                                class="text-white hover:text-primary transition-colors text-sm uppercase tracking-wide">About</a>
                        </li>
                        <li><a href="/property/search"
                                class="text-white hover:text-primary transition-colors text-sm uppercase tracking-wide">Search</a>
                        </li>
                        <li><a href="/terms-of-use"
                                class="text-white hover:text-primary transition-colors text-sm uppercase tracking-wide">Terms
                                of Use</a></li>
                        <li><a href="/privacy"
                                class="text-white hover:text-primary transition-colors text-sm uppercase tracking-wide">Privacy
                                Policy</a></li>
                        <li><a href="/contact_us"
                                class="text-white hover:text-primary transition-colors text-sm uppercase tracking-wide">Contact</a>
                        </li>
                    </ul>
                </nav>

                <!-- Social Media Icons -->
                <div class="flex gap-4">
                    <a href="#"
                        class="w-10 h-10 rounded-full border border-white/30 flex items-center justify-center text-white hover:text-primary hover:border-primary transition-colors">
                        <Icon icon="tabler:brand-facebook" class="size-5" />
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full border border-white/30 flex items-center justify-center text-white hover:text-primary hover:border-primary transition-colors">
                        <Icon icon="tabler:brand-instagram" class="size-5" />
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full border border-white/30 flex items-center justify-center text-white hover:text-primary hover:border-primary transition-colors">
                        <Icon icon="tabler:brand-youtube" class="size-5" />
                    </a>
                </div>
            </div>

            <!-- Middle Row: Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <!-- Phone -->
                <div class="flex items-center gap-3 bg-gray-700/50 rounded-lg p-4">
                    <Icon icon="tabler:phone" class="size-6 text-primary flex-shrink-0" />
                    <span class="text-white text-sm">610 595-4192</span>
                </div>

                <!-- Email -->
                <div class="flex items-center gap-3 bg-gray-700/50 rounded-lg p-4">
                    <Icon icon="tabler:mail" class="size-6 text-primary flex-shrink-0" />
                    <span class="text-white text-sm">team@revamp365.ai</span>
                </div>

                <!-- Address -->
                <div class="flex items-center gap-3 bg-gray-700/50 rounded-lg p-4">
                    <Icon icon="tabler:map-pin" class="size-6 text-primary flex-shrink-0" />
                    <span class="text-white text-sm">1309 MacDade Blvd Woodlyn PA 19094</span>
                </div>
            </div>

            <!-- Bottom Row: Copyright -->
            <div class="text-center pt-8 border-t border-gray-700">
                <p class="text-white/80 text-sm">
                    Copyright <span class="text-primary">2025</span> all rights reserved
                </p>
            </div>
        </div>
    </footer>
</template>