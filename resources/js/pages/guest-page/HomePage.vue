<script setup lang="ts">

// import { Link } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
// import { Icon } from '@iconify/vue';
import GuestTestimonials from './reusables/GuestTestimonials.vue';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import { pageVars, GuestFeature } from './vars/vars';
import GuestAssessment from './reusables/GuestAssessment.vue';
import GuestJoinFooter from './reusables/GuestJoinFooter.vue';
import GuestAdditionalFeatures from './reusables/GuestAdditionalFeatures.vue';
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

// Dynamic typing animation
const typingTexts: string[] = [
    'Fix & Flip Deals',
    'Cash-Flowing Rentals',
    'Wholesale Deals',
    'Underpriced Listings',
    'Comps (No license required)',
    'Market Data'
];

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
                    Instant <span class="text-primary">Access</span> to
                    <br>
                    <span class=" min-h-[1.2em] inline-block">
                        {{ currentText }}<span class="animate-pulse">|</span>
                    </span>
                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Set your buy box. Instantly find deals that match. Scale your portfolio.
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
                        Built for Speed, Tuned for<br>Strategy
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        Explore investor-grade features designed to eliminate the guesswork and help you take action
                        fast.
                    </p>
                </div>
            </div>
        </div>

        <GuestTestimonials />

        <GuestDyamicFeature :features="features" />

        <GuestAdditionalFeatures />

        <GuestAssessment />

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