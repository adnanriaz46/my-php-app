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
        title: 'Set a Buy Box for Every Client',
        description: 'Forget copy/pasting MLS links. Set filters based on price, ARV, cash flow, and strategy—and let Revamp365 surface the perfect properties automatically.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(71.69042769857434% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/3LuFmhYkYouZFsPAXOF4?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="personalized buy box" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Analyze Deals On the Spot',
        description: 'Instantly pull comps, run ARV and ROI projections, and see cash flow forecasts—no spreadsheets, no second-guessing.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(62.5% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/9cVken55muALUVEVDOXc?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="find ai deal demo block" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'MLS + Off-Market Inventory',
        description: 'Access a powerful feed of on-market and off-market listings. Wholesale deals, pre-foreclosures, distressed properties—all in one place.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(60.18041237113402% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/qKQrrbS36g5USnQJq0ek?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title=" demo block - discover properties - Wholesale button Focus" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Look Like a Pro',
        description: 'Send polished reports with comps, numbers, and investor-friendly deal summaries—all branded to you. Your clients think you\'re the wizard. We just power the magic.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(63.41463414634146% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/HRw8Qem750PpZbREukH0?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="demo block - pro analysis reports" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    }
]

const smartCards = [
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881c32aa97a8.png',
        title: 'Buy Box Filters for Every Client',
        description: 'Easily manage multiple investor strategies Set a custom buy box for each investor—whether they’re flipping, BRRRRing, or buying turnkey rentals. Get matched listings instantly.',
        alt: 'Deal Intelligence Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881c344e726b.png',
        title: 'Comps & Numbers Built In',
        description: 'Answer “Is this a good deal?” in seconds Pull ARV, rental comps, cash flow, and ROI with one click—no spreadsheets, no guesswork, just investor-ready analysis.',
        alt: 'Speed & Automation Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881c3732e498.png',
        title: 'MLS Meets Off-Market Inventory',
        description: 'Deliver deals they can’t find anywhere else Combine MLS, wholesale, pre-foreclosure, and investor-grade listings in one platform. Beat Zillow. Beat Redfin.',
        alt: 'Built for Real Investors Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6881c38981f3d.png',
        title: 'Branded Deal Reports & Alerts',
        description: 'Look like the expert, every time Share pro-level property reports and automated deal alerts—fully branded to you. Keep your clients loyal and impressed.',
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
                    Find <span class="text-primary">Deals</span> Your Investor Clients <br />
                    Actually <span class="text-primary">Want</span>

                </h1>

                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Set a buy box. Run comps. Source cash-flowing or flippable properties fast—so you always look like
                    the expert in the room.
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
                        Built for Investor-Friendly Agents
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        The Edge You Need to Win More Investor Business. Revamp365 is your deal-sourcing, underwriting,
                        and investor-serving command center. Whether you're helping a BRRRR buyer or a fix & flipper,
                        deliver better deals in less time—and close more repeat business.
                    </p>
                </div>
            </div>
        </div>
        <GuestDyamicFeature :features="features" />
        <GuestAssessment />
        <GuestSmartCards main-title="Become the Investor Agent They Never Leave"
            main-description="You already know how to serve buyers—now show investors you know how to find real opportunities. Faster deals, better relationships, more referrals."
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