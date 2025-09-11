<script setup lang="ts">
// import { Link } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
// import { Icon } from '@iconify/vue';
import GuestTestimonials from './reusables/GuestTestimonials.vue';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import { GuestFeature, pageVars } from './vars/vars';
import GuestAssessment from './reusables/GuestAssessment.vue';
import GuestJoinFooter from './reusables/GuestJoinFooter.vue';
import GuestSmartCards from './reusables/GuestSmartCards.vue';
import GuestDyamicFeature from './reusables/GuestDyamicFeature.vue';

const features: GuestFeature[] = [
    {
        title: 'Discover Properties That Cash Flow',
        description: 'Stop wasting time on Zillow. Set your Buy Box for rental income, location, and condition, and let Revamp365 surface cash-flowing opportunities on and off market.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(60.18041237113402% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/NBaig26bXGd3rDj3b1kV?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="demo block - discover properties" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Analyze Rental ROI in Seconds',
        description: 'Cap rate, cash-on-cash, loan assumptions, expenses, rent projections—done for you, automatically. Whether it’s a turnkey or a BRRRR, see if it pencils out instantly.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(101.1400651465798% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/NPCd1d8UKVxUJzvrq8AG?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="Calculator Quick Overview" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Power Every Step of BRRRR',
        description: 'Buy, Rehab, Rent, Refinance, Repeat—with clear numbers every step of the way. Track your equity, estimate refinance value, and build your portfolio with confidence.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(62.5% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/xmRfU1GcILC1oLvLxo9k?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="instant deal analysis demo block" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Favorites View - Add Your Portfolio & Long-Term Metrics',
        description: 'See your average ROI, rent roll, and performance by zip code or strategy. Get smarter with every deal and optimize your pipeline over time.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(101.1400651465798% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/NPCd1d8UKVxUJzvrq8AG?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="Calculator Quick Overview" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    }
]

const smartCards = [
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881b7c383fca.png',
        title: 'Built-In Rental Filters',
        description: 'Find rentals that match your income goals. Filter by estimated rent, price-to-rent ratio, cash-on-cash return, and more to surface deals that cash flow from day one.',
        alt: 'Deal Intelligence Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881b7e74b9f3.png',
        title: 'Real-Time BRRRR Leads',
        description: 'Never miss your next BRRRR deal. Get alerted the moment a property hits your target market with enough spread to buy, rehab, rent, and refi.',
        alt: 'Speed & Automation Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881b8013d23a.png',
        title: 'MLS + Off-Market Inventory',
        description: 'On-market and off-market rentals, in one place. Skip the chase—browse a unified feed of investor-grade properties from both the MLS and top wholesalers.',
        alt: 'Built for Real Investors Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881b88d05418.png',
        title: 'Cash Flow & ROI Calculated Instantly',
        description: 'Know the numbers before you ever tour the property. Instantly view estimated rent, monthly cash flow, expenses, and key metrics like cap rate and ROI.',
        alt: 'Investor Reporting Icon'
    }
]

</script>

<template>
    <GuestAppLayout>
        <!-- Hero Section -->
        <div class="relative flex flex-col justify-center items-center -mt-25 pt-40 ">
            <!-- Video Background -->
            <div class="absolute inset-0 z-0">
                <video class="w-full h-full object-cover" autoplay muted loop playsinline
                    :poster="pageVars.bgVideoPoster">
                    <source :src="pageVars.bgVideo" type="video/mp4">
                </video>
                <!-- Dark overlay for better text readability -->
                <div class="absolute inset-0 bg-black/40"></div>
            </div>
            <!-- Text Container -->
            <div class="text-center max-w-4xl mx-auto mb-12 z-5 px-4">
                <h1 class="text-4xl md:text-5xl font-semibold text-white mb-6 leading-tight">
                    Find <span class="text-primary">Cash</span> Flowing <br />
                    Rentals - <span class="text-primary">Faster</span>

                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    BRRRR, Buy & Hold, or long-term income—Revamp365 helps you find, analyze, and secure rental
                    properties that perform.
                </p>

                <!-- CTA Button -->
                <button @click="router.visit(route('register'))"
                    class="bg-primary hover:bg-primary/90 text-primary-foreground font-semibold py-4 px-8 rounded-lg text-lg transition-colors duration-200 mb-4 cursor-pointer">
                    Get started for free
                </button>

                <!-- No credit card required -->
                <div class="flex items-center justify-center gap-2 text-primary italic text-sm">
                    <img src="//revamp365-storage.s3.amazonaws.com/assets/assets/687f9ddbeac16.png"
                        alt="No credit card required" class="h-8 w-auto">

                </div>
            </div>
            <!-- Announcement Bar -->
            <div class="bg-gray-800/80 backdrop-blur-sm border border-primary/30 rounded-full px-6 py-3 mb-8 flex items-center gap-4 mx-4 cursor-pointer"
                @click="router.visit(route('guest.ai_onboarding'))">
                <span class="text-white text-sm md:text-base">
                    Announcing AI-Powered Setup & Optimization
                </span>
                <button
                    class="bg-primary hidden md:block    hover:bg-primary/90 text-primary-foreground px-4 py-2 rounded-full text-sm transition-colors duration-200 cursor-pointer">
                    Learn more
                </button>
            </div>

            <!-- Arcade Iframe Container -->
            <div class="w-full mx-auto relative bg-cover bg-center px-4"
                style="background-image: url('//revamp365-storage.s3.amazonaws.com/assets/assets/687f9c8f8dbd3.png');">
                <div class="w-full max-w-5xl mx-auto">
                    <!-- Desktop Iframe -->
                    <div class="hidden md:block">
                        <!--ARCADE EMBED START-->
                        <div
                            style="position: relative; padding-bottom: calc(49.21875% + 41px); height: 0; width: 100%;">
                            <iframe
                                src="https://demo.arcade.software/Ni3rdpAW9qAMynBIoI9C?embed&embed_mobile=tab&embed_desktop=inline&show_copy_link=true"
                                title="Discover Prime Investment Properties with Ease" frameborder="0" loading="lazy"
                                webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe>
                        </div>
                        <!--ARCADE EMBED END-->
                    </div>

                    <!-- Mobile Iframe -->
                    <div class="md:hidden">
                        <!--ARCADE EMBED START-->
                        <div
                            style="position: relative; padding-bottom: calc(134.2281879194631% + 41px); height: 0; width: 100%;">
                            <iframe
                                src="https://demo.arcade.software/yfO6HYyjiKPAUIXCCaW6?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true"
                                title="Arcade Flow (Thu Mar 27 2025)" frameborder="0" loading="lazy"
                                webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe>
                        </div>
                        <!--ARCADE EMBED END-->
                    </div>
                </div>

                <div class="w-full max-w-5xl mx-auto mt-8 text-center mb-20 px-4">
                    <h2 class="text-2xl md:text-4xl font-bold text-white mb-6 leading-tight drop-shadow-sm">
                        The Rental Playbook, Fully Automated
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        From sourcing to analysis to sending offers, Revamp365 gives rental investors the edge.
                        Easily find high-yield properties, run full cash flow projections, and execute the BRRRR method
                        without juggling spreadsheets.
                    </p>
                </div>
            </div>
        </div>
        <GuestDyamicFeature :features="features" />
        <GuestAssessment />
        <GuestSmartCards main-title="Ready to Scale Your Rental Portfolio?"
            main-description="Start finding better buy-and-hold deals, analyzing cash flow faster, and growing your passive income today."
            :cards="smartCards" />
        <GuestTestimonials />
        <GuestJoinFooter />
    </GuestAppLayout>
</template>

<style scoped>
/* Custom animations for typing effect */
@keyframes blink {

    0%,
    50% {
        opacity: 1;
    }

    51%,
    100% {
        opacity: 0;
    }
}

.animate-pulse {
    animation: blink 1s infinite;
}

/* Ensure video covers the entire background */
video {
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Responsive text sizing */
@media (max-width: 768px) {
    h1 {
        font-size: 2.5rem;
        line-height: 1.2;
    }

    .min-h-\[1\.2em\] {
        min-height: 1.2em;
    }
}

/* Ensure proper z-index layering */
.z-5 {
    z-index: 5;
}
</style>