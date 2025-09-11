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
// import GuestAdditionalFeatures from './reusables/GuestAdditionalFeatures.vue';
import GuestDyamicFeature from './reusables/GuestDyamicFeature.vue';
import { CountyState } from '@/types';
import { getPropertyCount, getSoldUnderMarketValue } from '@/lib/DBApiUtil';
import { DBApi, DBApiPropertyCount, DBApiSoldUnderMarketValue, paramsDBApiGetProperty } from '@/types/DBApi';
import { Icon } from '@iconify/vue';
import AdditionalFeatureMetricsBar from './reusables/AdditionalFeatureMetricsBar.vue';
import PropertyThumbSlider from './reusables/PropertyThumbSlider.vue';

const props = defineProps<{
    county: CountyState;
}>();


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

const soldUnderMarketValue = ref<DBApiSoldUnderMarketValue[]>([]);
const fetchSoldUnderMarketValue = async () => {
    const response: DBApi<DBApiSoldUnderMarketValue[]> = await getSoldUnderMarketValue({
        range: 30,
        state: props.county.state,
        county: props.county.county,
        limit: 6,
    });
    soldUnderMarketValue.value = response.data || [];
}

onMounted(() => {
    startTypingAnimation();
    fetchSoldUnderMarketValue();
});

onUnmounted(() => {
    if (typingInterval) {
        clearTimeout(typingInterval);
    }
});

// const page = usePage();

const checkIcon = () => {
    return `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 shrink-0 ">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M9 12l2 2l4 -4" />
        <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
    </svg>
    `
}
const features: GuestFeature[] = [
    {
        title: 'Local Market Analyzer',
        description: `
        <div>
            <div class="text-xl font-semibold mb-2">
                Instantly Analyze ${props.county.county} Investment Properties with Real ARV &amp; Rental Data
            </div>
            <div class="mb-5">
                Revamp365.ai automatically runs the numbers on every deal in ${props.county.county} &mdash; no spreadsheets, guesswork, or outside tools needed.
            </div>
            <ul class="space-y-3 mb-5">
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        ${checkIcon()}
                    </span>
                    <span>
                        Instantly see comps, ARV, rehab estimates, rent ranges, and ROI
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        ${checkIcon()}
                    </span>
                    <span>
                        Based on live investor-grade data from MLS data, public records feeds, and more
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        ${checkIcon()}
                    </span>
                    <span>
                        Rental comps pulled from nearby investor listings to show true cash flow potential
                    </span>
                </li>
            </ul>
            <div>
                Whether you&rsquo;re flipping or holding, our AI models adjust to local trends so you know what a good deal actually looks like.
            </div>
        </div>
        `,
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(62.5% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/xmRfU1GcILC1oLvLxo9k?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="instant deal analysis demo block" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Real-Time Deal Scoring',
        description: `
        <div>
            <div class="font-semibold text-lg mb-2">
                Know Which Deals Are Worth It &mdash; Before You Even Click
            </div>
            <div class="mb-5">
                Every property in ${props.county.county} is scored using our proprietary Investor Match Score™ &mdash; a ranking based on:
            </div>
            <ul class="space-y-3 mb-5">
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        ${checkIcon()}
                    </span>
                    <span>
                        Under priced listings
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        ${checkIcon()}
                    </span>
                    <span>
                        Hot market areas
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        ${checkIcon()}
                    </span>
                    <span>
                        Flip profit estimations
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        ${checkIcon()}
                    </span>
                    <span>
                        Investor activity
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        ${checkIcon()}
                    </span>
                    <span>
                        Cash flow calculations
                    </span>
                </li>
            </ul>
            <div>
                This means no more digging through hundreds of properties, spending dozens of hours a week &mdash; just to turn up with no potential deal flow.
            </div>
        </div>
        `,
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(62.5% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/9cVken55muALUVEVDOXc?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="find ai deal demo block" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Investor Activity in ' + props.county.county,
        description: `
        <div>
            <h3 class="text-2xl font-semibold mb-4 text-neutral-900 dark:text-neutral-100">
                ${props.county.county} is a Hotspot for Investors &mdash; Here&rsquo;s the Proof
            </h3>
            <div class="mb-4 text-lg text-neutral-700 dark:text-neutral-200">
                In the past 90 days:
            </div>
            <ul class="space-y-4 ml-2">
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                    </span>
                    <span>
                        <span class="font-semibold">17</span> Under Market Value purchases were made across ${props.county.county}
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                    </span>
                    <span>
                        <span class="font-semibold">33</span> Fix &amp; Flip purchases were made across ${props.county.county}
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                    </span>
                    <span>
                        <span class="font-semibold">9</span> Rental or BRRRR purchases were made across ${props.county.county}
                    </span>
                </li>
            </ul>
        </div>
        `,
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(71.69042769857434% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/3LuFmhYkYouZFsPAXOF4?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="personalized buy box" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    }
]


const WholesaleDeals = ref(0);
const WholesaleDealsLoading = ref(true);
const UnderMarketValueDeals = ref(0);
const UnderMarketValueDealsLoading = ref(true);
const FixAndFlipDeals = ref(0);
const FixAndFlipDealsLoading = ref(true);
const BuyAndHoldDeals = ref(0);
const BuyAndHoldDealsLoading = ref(true);


onMounted(async () => {
    let params: paramsDBApiGetProperty = {
        accuracy_score_value: 8,
        status: ['Active'],
        county: [props.county.county],
        state_or_province_keyword: [props.county.state],
        deal_type: 'Wholesale'
    }
    WholesaleDealsLoading.value = true;
    WholesaleDeals.value = await fetchDeals(params);
    WholesaleDealsLoading.value = false;

    params = {
        delta_min: 50,
        accuracy_score_value: 8,
        status: ['Active'],
        county: [props.county.county],
        state_or_province_keyword: [props.county.state],
    }
    UnderMarketValueDealsLoading.value = true;
    UnderMarketValueDeals.value = await fetchDeals(params);
    UnderMarketValueDealsLoading.value = false;

    params = {
        est_profit_min: 30000,
        accuracy_score_value: 8,
        status: ['Active'],
        county: [props.county.county],
        state_or_province_keyword: [props.county.state]
    }
    FixAndFlipDealsLoading.value = true;
    FixAndFlipDeals.value = await fetchDeals(params);
    FixAndFlipDealsLoading.value = false;

    params = {
        est_cashflow_min: 300,
        accuracy_score_value: 8,
        status: ['Active'],
        county: [props.county.county],
        state_or_province_keyword: [props.county.state],
    }
    BuyAndHoldDealsLoading.value = true;
    BuyAndHoldDeals.value = await fetchDeals(params);
    BuyAndHoldDealsLoading.value = false;
})

const fetchDeals = async (params: paramsDBApiGetProperty) => {

    const getPropertyCountResponse: DBApi<DBApiPropertyCount> = await getPropertyCount(params)
    if (getPropertyCountResponse.data) {
        return getPropertyCountResponse.data?.count || 0;
    }
    return 0;
}


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
                    #1 Investor <span class="text-primary">Deal-Finding</span> Software in {{ county.county }} County,
                    {{ county.state }}
                    <br />
                    <span class="text-white/90 text-lg">Investing, Simplified.</span>
                    <br />
                    <span class=" min-h-[1.2em] inline-block text-3xl md:text-4xl">
                        {{ currentText }}<span class="animate-pulse">|</span>
                    </span>
                </h1>
            </div>

            <!-- Arcade Iframe Container -->
            <div class="w-full mx-auto relative bg-cover bg-center px-4 pb-16"
                style="background-image: url('//revamp365-storage.s3.amazonaws.com/assets/assets/687f9c8f8dbd3.png');">
                <!-- Real Estate Deals Section -->
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12 pt-16">
                        <!-- <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                            Available Deals in {{ county.county }} County, {{ county.state }}
                        </h2>
                        <p class="text-lg text-white/80 max-w-2xl mx-auto">
                            Discover the hottest investment opportunities available right now
                        </p> -->
                    </div>

                    <!-- Deals Grid -->
                    <div class="flex flex-wrap justify-center gap-6">
                        <!-- Wholesale Deals Card -->
                        <div v-if="!WholesaleDealsLoading && WholesaleDeals > 0"
                            class="w-full md:w-80 lg:w-72 bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                            <div class="flex-1" v-if="!WholesaleDealsLoading">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Wholesale Deals</h3>
                                <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-4">{{ WholesaleDeals
                                }}</div>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-base">
                                    {{ WholesaleDeals }} Off-market wholesale deals are available in {{ county.county }}
                                    County,
                                    {{ county.state }} - right now. These are often the hottest deals and sell fast.
                                </p>
                            </div>
                            <div class="flex-1 flex justify-center items-center" v-else>
                                <div class="text-4xl font-bold text-primary mb-4">
                                    <Icon icon="mdi:loading" class="animate-spin size-7" />
                                </div>
                            </div>
                            <div class="mt-6">
                                <button @click="router.visit(route('register'))"
                                    class="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-semibold py-3 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                                    Show Me The Deals
                                </button>
                            </div>
                        </div>

                        <!-- Under Market Value Card -->
                        <div
                            class="w-full md:w-80 lg:w-72 min-h-[320px] bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                            <div class="flex-1" v-if="!UnderMarketValueDealsLoading">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Under Market Value</h3>
                                <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-4">{{
                                    UnderMarketValueDeals }}</div>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-base">
                                    {{ UnderMarketValueDeals }} Under market value properties are available in {{
                                        county.county }} County,
                                    {{ county.state }} - right now. Buy right and claim the equity today.
                                </p>
                            </div>
                            <div class="flex-1 flex justify-center items-center" v-else>
                                <div class="text-4xl font-bold text-primary mb-4">
                                    <Icon icon="mdi:loading" class="animate-spin size-7" />
                                </div>
                            </div>
                            <div class="mt-6">
                                <button @click="router.visit(route('register'))"
                                    class="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-semibold py-3 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                                    Show Me The Deals
                                </button>
                            </div>
                        </div>

                        <!-- Fix & Flip Deals Card -->
                        <div
                            class="w-full md:w-80 lg:w-72 min-h-[320px] bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                            <div class="flex-1" v-if="!FixAndFlipDealsLoading">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Fix & Flip Deals</h3>
                                <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-4">{{ FixAndFlipDeals
                                }}</div>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-base">
                                    {{ FixAndFlipDeals }} Perfect fix & flip opportunities are available in {{
                                        county.county }} County,
                                    {{ county.state }} - right now. Land your next deal today.
                                </p>
                            </div>
                            <div class="flex-1 flex justify-center items-center" v-else>
                                <div class="text-4xl font-bold text-primary mb-4">
                                    <Icon icon="mdi:loading" class="animate-spin size-7" />
                                </div>
                            </div>
                            <div class="mt-6">
                                <button @click="router.visit(route('register'))"
                                    class="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-semibold py-3 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                                    Show Me The Deals
                                </button>
                            </div>
                        </div>

                        <!-- Buy & Hold Deals Card -->
                        <div
                            class="w-full md:w-80 lg:w-72 min-h-[320px]   bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                            <div class="flex-1" v-if="!BuyAndHoldDealsLoading">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Buy & Hold Deals</h3>
                                <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-4">{{ BuyAndHoldDeals
                                }}</div>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-base">
                                    {{ BuyAndHoldDeals }} Rental or BRRRR opportunities are available in {{
                                        county.county }} County,
                                    {{ county.state }} - right now. Cash in on cash flow today.
                                </p>
                            </div>
                            <div class="flex-1 flex justify-center items-center" v-else>
                                <div class="text-4xl font-bold text-primary mb-4">
                                    <Icon icon="mdi:loading" class="animate-spin size-7" />
                                </div>
                            </div>
                            <div class="mt-6">
                                <button @click="router.visit(route('register'))"
                                    class="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-semibold py-3 px-4 rounded-lg transition-colors duration-200 cursor-pointer">
                                    Show Me The Deals
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <GuestDyamicFeature :features="features" />
        <!--Additional Features-->
        <div class="w-full  mt-20 mb-20 px-4 flex flex-col gap-12 bg-gray-800 pb-10">
            <!-- Data Analytics Section -->
            <div class="w-full max-w-7xl mx-auto -mt-15">
                <AdditionalFeatureMetricsBar />
            </div>
            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 items-center justify-center">
                    <!-- Smartphone Mockup -->
                    <div class="relative text-center max-w-7xl mx-auto">
                        <h2 class="text-2xl font-semibold text-white mb-4">
                            These are profitable deals someone else won in Adams County.
                            Don't miss future opportunities...
                        </h2>
                    </div>
                    <!-- Text Boxes -->
                    <PropertyThumbSlider :properties="soldUnderMarketValue" />
                </div>
        </div>
        <GuestAssessment />
        <GuestTestimonials />
        <GuestJoinFooter />

    </GuestAppLayout>
</template>