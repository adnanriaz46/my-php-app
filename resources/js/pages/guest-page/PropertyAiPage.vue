<script setup lang="ts">

// import { Link } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
// import { Icon } from '@iconify/vue';
import GuestTestimonials from './reusables/GuestTestimonials.vue';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import { pageVars, GuestFeature } from './vars/vars';
import GuestJoinFooter from './reusables/GuestJoinFooter.vue';
import GuestAssessmentAI from './reusables/GuestAssessmentAI.vue';
import GuestSmartCards from './reusables/GuestSmartCards.vue';
import GuestDyamicFeature from './reusables/GuestDyamicFeature.vue';

const features: GuestFeature[] = [
    {
        title: 'Instant ARV & Comp Engine',
        description: 'Get AI-generated comps and ARV in seconds based on neighborhood, bed/bath count, square footage, and condition — no MLS license needed.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881030e42122.png',
        iframe: null
    },
    {
        title: 'AI Deal Scoring That Saves Time',
        description: 'Our model automatically scores each property based on your goals: flip profit, cash flow, ROI, rehab cost, and more.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/68810337e97ae.png',
        iframe: null
    },
    {
        title: 'Built-In Rental & Flip Calculators',
        description: 'Whether you\'re flipping or holding, use our calculators to vet deals on the spot. Includes rehab budget, MAO, Cap Rate, Cash-on-Cash, and more.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(101.1400651465798% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/NPCd1d8UKVxUJzvrq8AG?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="Calculator Quick Overview" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Underwrite in One Click',
        description: 'Generate exportable, presentation-ready deal summaries to pitch partners, lenders, or buyers. Let AI do the heavy lifting.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881037b6aeef.png',
        iframe: null
    },
    {
        title: 'AI at your fingertips',
        description: 'Every property page features a built-in AI chat, preloaded with the deal\'s actual data. Ask anything — from "What\'s the max offer I should make?" to "How does this compare to others nearby?" It\'s like having an acquisitions analyst in your browser.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(100% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/cwfKsdRj7ALJm1XQZ9U2?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="AI chat sample" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Learn As You Go',
        description: 'Each data point comes with an explanation. New investors can learn the why behind the numbers while they analyze.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/688103b28df06.png',
        iframe: null
    }
]

const smartCards = [
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881093f7c6e3.png',
        title: 'AI-Powered Valuations',
        description: 'Get accurate ARVs and comps instantly using property-specific data and machine learning.',
        alt: 'Deal Intelligence Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/68810955562c7.png',
        title: 'Automated Deal Scoring',
        description: 'Each property is ranked for you based on flip potential, rental returns, and cash flow.',
        alt: 'Speed & Automation Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881096dc58a5.png',
        title: 'Dynamic Financials',
        description: 'Run full ROI breakdowns including Cap Rate, Cash-on-Cash, rehab budgets, and more — in real time.',
        alt: 'Built for Real Investors Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/688109823150a.png',
        title: 'Ask the AI Anything',
        description: 'Every listing includes a built-in chat assistant trained on that specific deal\'s data — ready to answer any question.',
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
                    Deals You <span class="text-primary">Analysis.</span>
                    Faster <span class="text-primary"> Offers.</span>
                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Revamp’s Property AI runs comps, calculates profit, and ranks deals in seconds — no spreadsheet
                    required.
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

                    <!--ARCADE EMBED START-->
                    <div style="position: relative; padding-bottom: calc(49.21875% + 41px); height: 0; width: 100%;">
                        <iframe
                            src="https://demo.arcade.software/3Szzm7hHjeknW5cmr8r8?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true"
                            title="Ai Detailed Demo" frameborder="0" loading="lazy" webkitallowfullscreen
                            mozallowfullscreen allowfullscreen allow="clipboard-write"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe>
                    </div>
                    <!--ARCADE EMBED END-->

                </div>

                <div class="w-full max-w-5xl mx-auto mt-8 text-center mb-20 px-4">
                    <h2 class="text-2xl md:text-4xl font-bold text-white mb-6 leading-tight drop-shadow-sm">
                        Invest with confidence, not guesswork.
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        Our AI handles the numbers so you can focus on decisions that grow your portfolio.
                    </p>
                </div>
            </div>
        </div>
        <GuestDyamicFeature :features="features" />
        <GuestAssessmentAI />
        <GuestSmartCards 
            main-title="Let the AI do the math and the work. You make the money."
            main-description="Start using Property AI inside Revamp365 today."
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