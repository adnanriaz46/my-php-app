<script setup lang="ts">
// import { Link } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
// import { Icon } from '@iconify/vue';
import GuestTestimonials from './reusables/GuestTestimonials.vue';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import { pageVars, GuestFeature } from './vars/vars';
import GuestAssessment from './reusables/GuestAssessment.vue';
import GuestJoinFooter from './reusables/GuestJoinFooter.vue';
import GuestSmartCards from './reusables/GuestSmartCards.vue';
import GuestDyamicFeature from './reusables/GuestDyamicFeature.vue';

const features: GuestFeature[] = [
    {
        title: 'Define Your Perfect Deal',
        description: 'Set your target counties, strategy (flip, rental, wholesale), property types, and key criteria like ARV, square footage, or year built.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881030e42122.png',
        iframe: null
    },
    {
        title: 'Get Notified First',
        description: 'As soon as a property hits your exact specs, you\'ll get alerted via email or in-app. Be the first to act — before it hits everyone else\'s radar.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/68810337e97ae.png',
        iframe: null
    },
    {
        title: 'Personalized Deal Feed',
        description: 'Your dashboard becomes a curated stream of only the properties that meet your standards. It\'s like having an acquisitions assistant on autopilot.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(101.1400651465798% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/NPCd1d8UKVxUJzvrq8AG?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="Calculator Quick Overview" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Save Multiple Buy Boxes',
        description: 'Invest in multiple markets or use different strategies? No problem. Save and switch between custom Buy Boxes with ease.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881037b6aeef.png',
        iframe: null
    },
    {
        title: 'Works with MLS + Wholesale',
        description: 'Whether it\'s on-market or off, our system runs your Buy Box across both data sources so you don\'t miss an opportunity.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(100% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/cwfKsdRj7ALJm1XQZ9U2?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="AI chat sample" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    }
]

const smartCards = [
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6880fe8c4fd4e.png',
        title: 'Investor-Grade Filters',
        description: 'Apply filters built for real estate investors — not retail buyers. Sort by flip profit, cash flow, price per square foot, and more.',
        alt: 'Deal Intelligence Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6880fea9a71d4.png',
        title: 'Smart Deal Alerts',
        description: 'Set your Buy Box once and get notified the moment a deal hits your criteria. Sleep while your deal engine works for you.',
        alt: 'Speed & Automation Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6880fec3d3b29.png',
        title: 'MLS + Wholesale, One Feed',
        description: 'Browse both MLS and curated wholesale deals in a single interface. No more bouncing between tabs or spreadsheets.',
        alt: 'Built for Real Investors Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6880fedd62858.png',
        title: 'Instant Deal Analysis',
        description: 'Click on any property to reveal comps, ARV, flip margins, rental ROI, and a one-click export sheet.',
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
                    Your <span class="text-primary">Deals.</span> Your Rules.

                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Set your Buy Box once and let Revamp365 deliver properties that match your exact investment
                    criteria.
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
                        Your investment strategy, automated.
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        Never waste time sorting through deals that don’t fit. Tell the system what you buy, and we’ll
                        do the hunting for you.
                    </p>
                </div>
            </div>
        </div>
        <GuestDyamicFeature :features="features" />

        <GuestAssessment />
        <GuestSmartCards 
            main-title="The right deal is already out there."
            main-description="Let us find it for you. Join Revamp365 and never miss an opportunity again."
            :cards="smartCards"
        />
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