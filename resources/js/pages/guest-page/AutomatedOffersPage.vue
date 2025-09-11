<script setup lang="ts">
// import { Link } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';
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
        title: 'Build Your Offer List',
        description: 'Add any deal from MLS or wholesale listings into your "Offer Queue." No more copy/paste. Just select, calculate, and send.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(63.07692307692307% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/rXExkD0CaFUMGxxbWvN2?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="add to list - demo flow" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Smart Pricing Assistance',
        description: 'Use built-in data like ARV, AVM, rehab estimates, and MAO calculators to guide your offer price — or enter it manually if you prefer.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881169754c61.png',
        iframe: null
    },
    {
        title: 'Auto-Generate Signed Contracts',
        description: 'We fill out and digitally sign the Agreement of Sale for you based on your inputs. The system handles the paperwork so you can focus on strategy.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(63.144329896907216% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/SwWnQ2HG94rYsvCYER6J?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="offer - pdf creation demo flow" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Send Direct to Agents',
        description: 'The signed offer gets attached to a pre-written email and automatically sent to the listing agent. It\'s fast, professional, and hands-free.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/688116db1b6d3.png',
        iframe: null
    },
    {
        title: 'Built for Speed & Volume',
        description: 'Make dozens of offers a day without breaking a sweat. With automated contracts, pricing tools, and email templates, volume becomes your new advantage.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(68.28908554572271% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/6qN4kk03w501dhNTmrKH?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="auto offers - bulk send sample" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;"></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Dominate. Your. Market.',
        description: 'Only serious investors know this move—submit faster than the competition and lock it down before they even schedule a showing.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/68811732c4ece.png',
        iframe: null
    }
]

const smartCards = [
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/688118663bbb5.png',
        title: 'Start using Property AI inside Revamp365 today.',
        description: 'Add properties to your list and manage all your pending offers in one place — no spreadsheets required.',
        alt: 'Deal Intelligence Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881187ba59bf.png',
        title: 'Smart Pricing Inputs',
        description: 'Use built-in AVM, ARV, and rehab tools to estimate your offer price in seconds.',
        alt: 'Speed & Automation Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881189346957.png',
        title: 'Instant Contract Generator',
        description: 'Create a fully executed Agreement of Sale, ready to send — no editing or e-sign platforms needed.',
        alt: 'Built for Real Investors Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/688118ab906b7.png',
        title: '1-Click Agent Delivery',
        description: 'Send your signed offer directly to the listing agent with a prewritten email — all done for you.',
        alt: 'Investor Reporting Icon'
    }
]

const typingTexts: string[] = [
    'printing.',
    'uploading.',
    'friction.'
]

const currentTextIndex = ref(0);
const currentText = ref('');
const isTyping = ref(true);
const typingSpeed = 50;
const deletingSpeed = 50;
const pauseTime = 2000;

let typingInterval: number | null = null;

const startTypingAnimation = () => {
    let charIndex = 0;

    const animate = () => {
        const text = typingTexts[currentTextIndex.value];

        if (isTyping.value) {
            if (charIndex < text.length) {
                currentText.value = text.slice(0, charIndex + 1);
                charIndex++;
                typingInterval = setTimeout(animate, typingSpeed);
            } else {
                // Pause before deleting
                setTimeout(() => {
                    isTyping.value = false;
                    charIndex = text.length;
                    animate();
                }, pauseTime);
            }
        } else {
            if (currentText.value.length > 0) {
                currentText.value = currentText.value.slice(0, -1);
                typingInterval = setTimeout(animate, deletingSpeed);
            } else {
                // Move to next text
                currentTextIndex.value = (currentTextIndex.value + 1) % typingTexts.length;
                isTyping.value = true;
                charIndex = 0;
                animate();
            }
        }
    };

    animate();
};

onMounted(() => {
    startTypingAnimation();
});

onUnmounted(() => {
    if (typingInterval) {
        clearTimeout(typingInterval);
    }
});


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
                    Send Offers. <span class="text-primary">Automatically.</span>
                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Generate, sign, and submit real estate offers in seconds — directly to the listing agent.
                    <br /> No <span class="">{{ currentText }}</span>
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
                        From lead to offer in one click.
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        Speed wins in real estate. With Revamp365, you can turn qualified deals into signed offers
                        instantly—no spreadsheets, printers, or manual email chains.
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
        <GuestJoinFooter :show-register-button="false"
            description="NOTE - Automated Offers is not included with the $20/County plan. Auto Offers is a premium add-on service and is limited to a select number of investors in a given territory. Chat with us to apply." />
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