<script setup lang="ts">
// import { Link } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
// import { Icon } from '@iconify/vue';
import GuestTestimonials from './reusables/GuestTestimonials.vue';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import { GuestFeature, pageVars } from './vars/vars';
import GuestJoinFooter from './reusables/GuestJoinFooter.vue';
import GuestSmartCards from './reusables/GuestSmartCards.vue';
import GuestDyamicFeature from './reusables/GuestDyamicFeature.vue';
import { onMounted, onUnmounted, ref } from 'vue';
import GuestAssessmentWholesalers from './reusables/GuestAssessmentWholesalers.vue';


const features: GuestFeature[] = [
    {
        title: 'Dispo Deals in Days, Not Weeks',
        description: 'Skip the google drive photo folders and endless emails and texts. Upload your deal once, and we’ll put it in front of serious, verified buyers who are actively investing in your market.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(60.18041237113402% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/nru8nBY6F0FbdnNpy8Gv?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="demo block - wholesale - dispo fast" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Built-In Comps & Underwriting',
        description: 'Run comps, estimate ARV, and validate your numbers instantly—right inside the platform. Back your deal with real data, fast.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(60.18041237113402% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/pTrWnwRZ2WBCj8UYJb1G?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="demo block - wholesale - instant comps" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Email, Text & Track Buyer Interest',
        description: 'Send email blasts, text alerts, or even voicemail drops with a single click. Then watch the analytics roll in—opens, clicks, offers, and more.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(60.18041237113402% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/8vcmwk6ZQTa8elVSG4yN?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="demo block - wholesale - email" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: '(Coming Soon) Targeted Buyer Outreach',
        description: 'Access the entire national buyer database—filter by buy box, market, and budget. Then send offers directly via SMS or voicemail to the perfect investors.',
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881bacbefd86.png',
        iframe: null
    }
]

const smartCards = [
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881c073414a2.png',
        title: 'Verified Buyers, On-Demand',
        description: 'Your deals go straight to serious investors Tap into thousands of verified buyers actively looking for investment properties. No more lowballers or ghosted threads—just real buyers, real fast.',
        alt: 'Deal Intelligence Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881c08a13389.png',
        title: 'Track Buyer Engagement in Real Time',
        description: 'See who’s clicking, opening, and offering Get full visibility into every interaction—who viewed your deal, who opened your email, who clicked to submit an offer. Then follow up with precision.',
        alt: 'Speed & Automation Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881c09f95197.png',
        title: 'Create & Share Stunning Deal Pages',
        description: 'Showcase your properties like a pro Every deal gets a polished, branded listing page you can share with one link. Looks clean, builds credibility, and converts more buyers.',
        alt: 'Built for Real Investors Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881c0b4ddbd1.png',
        title: 'Blast Emails, Texts, & Voicemails—All in One',
        description: 'Automate your outreach and save hours Send professional email campaigns, SMS alerts, and voicemail drops to your list in one click. Fully integrated, fully tracked.',
        alt: 'Investor Reporting Icon'
    }
]


// Dynamic typing animation
const typingTexts: string[] = [
    'Close Faster.',
    'Earn More.',
    'Reach More Buyers.',
    'Leverage Ai Tech.'
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
                    <span class="text-primary">Dispo</span> Smarter <br />&nbsp;
                    {{ currentText }}


                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Upload your wholesale deals, find cash buyers instantly, and automate your entire dispo process.
                    Revamp365 was built for wholesalers who want bigger assignment fees, quicker closings, and minimize
                    tire kickers.
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
                        Your Dispo Process—Streamlined & Scalable
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        Revamp365 replaces clunky CRMs, ghosted group chats, and time-wasting follow-ups.
                        Sell more deals at higher margins with built-in comps, automated buyer outreach, and your own
                        branded listing storefront.
                    </p>
                </div>
            </div>
        </div>
        <GuestDyamicFeature :features="features" />
        <GuestAssessmentWholesalers/>
        <GuestSmartCards main-title="Double Your Dispo Without Doubling Your Work"
            main-description="Join the platform built by real estate investors, for real estate investors.
More visibility. More offers. More profit—on autopilot."
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