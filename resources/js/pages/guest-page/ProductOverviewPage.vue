<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { pageVars, GuestFeature } from './vars/vars';
import GuestTestimonials from './reusables/GuestTestimonials.vue';
import GuestAppLayout from './reusables/GuestAppLayout.vue';

import GuestAssessment from './reusables/GuestAssessment.vue';
import GuestJoinFooter from './reusables/GuestJoinFooter.vue';
import GuestSmartCards from './reusables/GuestSmartCards.vue';
import GuestDyamicFeature from './reusables/GuestDyamicFeature.vue';

const features: GuestFeature[] = [
    {
        title: 'AI Deal Finder',
        description: 'Get matched with profitable opportunities using smart filters, real-time MLS data, and exclusive wholesale deals. Whether you\'re looking for flips or rentals, Revamp365 surfaces the right properties, instantly.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(62.5% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/9cVken55muALUVEVDOXc?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="find ai deal demo block" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Instant Deal Analysis',
        description: 'Click any listing to unlock ARV, comps, rehab estimates, rental income, ROI, and more - no spreadsheets needed. Let the AI crunch the numbers so you can move with confidence.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(62.5% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/xmRfU1GcILC1oLvLxo9k?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="instant deal analysis demo block" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Automated Offers',
        description: 'Streamline your offer process with AI-powered automation. Send personalized offers at scale, track responses, and close deals faster than ever before.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(68.28908554572271% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/6qN4kk03w501dhNTmrKH?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="auto offers - bulk send sample" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Smart Negotiation',
        description: 'Leverage AI-driven insights to negotiate better deals. Get real-time market data, comp analysis, and strategic recommendations to maximize your returns.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(68.28908554572271% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/k4qtBIeJvIj3mG8ddA22?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="(copy) auto offers - bulk send sample" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Personalized Buy Box',
        description: 'Create your perfect investment criteria and let AI find properties that match your strategy. Set filters for location, price range, property type, and more.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(71.69042769857434% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/3LuFmhYkYouZFsPAXOF4?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="personalized buy box" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    }
]

const smartCards = [
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6880f0f121ba0.png',
        title: 'Deal Intelligence',
        description: 'Get instant insights on property value, potential ROI, and comps. Let AI surface the best investment opportunities in your market.',
        alt: 'Deal Intelligence Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6880f1267b13c.png',
        title: 'Speed & Automation',
        description: 'Save hours with automated deal alerts, underwriting, and smart filters. Focus only on properties that match your strategy.',
        alt: 'Speed & Automation Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6880fdf06f3db.png',
        title: 'Built for Real Investors',
        description: 'Whether you\'re flipping, renting, or wholesaling – get expert-tuned tools designed specifically for investors, not agents.',
        alt: 'Built for Real Investors Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6880ff150c258.png',
        title: 'Investor Reporting',
        description: 'Track KPIs like offer-to-close ratio, zip code performance, and lead engagement. Make smarter decisions backed by data.',
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
                    Effortless <span class="text-primary">Deal </span> Flow.

                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Find, analyze, and act on real estate investment opportunities with AI-powered precision.
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

            <!-- Video Container -->
            <div class="w-full mx-auto relative bg-cover bg-center px-4"
                style="background-image: url('//revamp365-storage.s3.amazonaws.com/assets/assets/687f9c8f8dbd3.png');">
                <div class="w-full max-w-4xl mx-auto p-4 backdrop-blur-2xl rounded-lg shadow-md">
                    <div class="relative w-full" style="padding-bottom: 56.25%;">
                        <iframe class="absolute top-0 left-0 w-full h-full rounded-lg"
                            src="https://www.youtube.com/embed/nTG-5FChocc" title="Revamp365 Product Overview"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                    </div>
                </div>

                <div class="w-full max-w-5xl mx-auto mt-8 text-center mb-20 px-4">
                    <h2 class="text-2xl md:text-4xl font-bold text-white mb-6 leading-tight drop-shadow-sm">
                        Elevate your investment <br /> strategy with automation.
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        Explore Revamp365’s smart features built to help you make faster, more profitable decisions.
                    </p>
                </div>
            </div>
        </div>

        <GuestDyamicFeature :features="features" />

        <GuestAssessment />

        <GuestSmartCards 
            main-title="Smarter investing starts here."
            main-description="Start using Revamp 365 and see your deal flow transform."
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