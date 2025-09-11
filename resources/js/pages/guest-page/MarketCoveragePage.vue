<script setup lang="ts">

// import { Link } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import { pageVars, GuestFeature } from './vars/vars';
import GuestAssessment from './reusables/GuestAssessment.vue';
import GuestJoinFooter from './reusables/GuestJoinFooter.vue';
import GuestAdditionalFeatures from './reusables/GuestAdditionalFeatures.vue';
import GuestDyamicFeature from './reusables/GuestDyamicFeature.vue';
import { CountyState } from '@/types';
import { Icon } from '@iconify/vue';
// @ts-expect-error - Leaflet types not properly configured
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

export interface GuestCounties {
    [stateCode: string]: CountyState[];
}

const props = defineProps<{
    counties: GuestCounties;
}>();

const mapContainer = ref<HTMLElement | null>(null);
let map: L.Map | null = null;

// State names mapping
const stateNames: { [key: string]: string } = {
    'PA': 'Pennsylvania',
    'NJ': 'New Jersey',
    'DE': 'Delaware',
    'MD': 'Maryland',
    'VA': 'Virginia',
    'WV': 'West Virginia',
    'DC': 'District of Columbia'
};

// State FIPS codes mapping for the map
// const stateFipsCodes: { [key: string]: string } = {
//     'PA': '42',
//     'NJ': '34', 
//     'DE': '10',
//     'MD': '24',
//     'VA': '51',
//     'WV': '54',
//     'DC': '11'
// };

const features: GuestFeature[] = [
    {
        title: 'Instant Deal Analysis',
        description: 'Click any listing to unlock ARV, comps, rehab estimates, rental income, ROI, and more – no spreadsheets needed. Let the AI crunch the numbers so you can move with confidence.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(62.5% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/xmRfU1GcILC1oLvLxo9k?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="instant deal analysis demo block" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Smart Offer Queue',
        description: 'Add any deal to your Offer List in one click – no copy/paste or browser tab chaos. Organize, edit, and prep multiple offers at once using a clean investor-first interface.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(68.28908554572271% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/6qN4kk03w501dhNTmrKH?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="auto offers - bulk send sample" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Pricing Assistance (MAO + AI)',
        description: 'Set your offer price based on calculated flip profit, ARV, or custom targets. Use built-in AI models or adjust manually – you\'re always in control.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(68.28908554572271% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/k4qtBIeJvIj3mG8ddA22?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="(copy) auto offers - bulk send sample" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
    },
    {
        title: 'Personalized Buy Box',
        description: 'Save your strategy and let the system work for you. Revamp365 delivers only the deals that match your exact criteria – by zip code, property type, margins, and more.',
        image: null,
        iframe: '<!--ARCADE EMBED START--><div style="position: relative; padding-bottom: calc(71.69042769857434% + 41px); height: 0; width: 100%;"><iframe src="https://demo.arcade.software/3LuFmhYkYouZFsPAXOF4?embed&embed_mobile=inline&embed_desktop=inline&show_copy_link=true" title="personalized buy box" frameborder="0" loading="lazy" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="clipboard-write" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; color-scheme: light;" ></iframe></div><!--ARCADE EMBED END-->'
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

const initMap = async () => {
    if (!mapContainer.value) return;

    // Remove existing map if any
    if (map) {
        map.off();
        map.remove();
    }

    // Initialize map
    map = L.map(mapContainer.value).setView([39.5, -77], 6);

    // Add tile layer
    const regularLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZHJld3JldmFtcDM2NSIsImEiOiJjbGFtbmxpdGowOTFiM3BtcXJiejZmM216In0.av2uw-r3r75vcBChtCcinw', {
        maxZoom: 14,
        attribution: '&copy; <a href="https://openstreetmap.org/copyright">Revamp365</a>'
    });

    regularLayer.addTo(map);

    // Add markers for each state with coverage
    Object.keys(props.counties).forEach(stateCode => {
        const counties = props.counties[stateCode];
        if (counties && counties.length > 0) {
            // Get approximate center coordinates for each state
            const stateCenters: { [key: string]: [number, number] } = {
                'PA': [40.5908, -77.2098],
                'NJ': [40.0583, -74.4057],
                'DE': [39.3185, -75.5071],
                'MD': [39.0639, -76.8021],
                'VA': [37.4316, -78.6569],
                'WV': [38.5976, -80.4549],
                'DC': [38.9072, -77.0369]
            };

            const center = stateCenters[stateCode];
            if (center) {
                const marker = L.marker(center, {
                    icon: L.divIcon({
                        className: 'state-coverage-marker',
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                        html: `
                            <div class="bg-orange-500 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold text-sm shadow-lg">
                                ${counties.length}
                            </div>
                        `
                    })
                });

                marker.bindPopup(`
                <style>
                    .leaflet-container a {
                        color: inherit !important;
                        text-decoration: none;
                    }
                </style>
                    <div class="text-center p-2">
                        <b class="text-lg">${stateNames[stateCode]}</b><br>
                        <span class="text-sm text-muted-foreground">${counties.length} counties covered</span>
                        <div class="flex flex-col mt-3 max-h-40 overflow-y-auto">
                            ${counties.map(county => `
                                <a href="/coverage/${county.slug}" 
                                   class="text-left text-sm text-blue-600 dark:text-blue-50 hover:text-blue-800 hover:bg-gray-100 dark:hover:bg-gray-800 hover:underline px-2 py-1 rounded transition-colors flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                    </svg>
                                    ${county.county} County
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `);

                marker.addTo(map);
            }
        }
    });
};

onMounted(() => {
    startTypingAnimation();
    initMap();
});

onUnmounted(() => {
    if (typingInterval) {
        clearTimeout(typingInterval);
    }
    if (map) {
        map.off();
        map.remove();
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
                <div class="w-full max-w-5xl mx-auto min-h-[400px]">
                    <div ref="mapContainer" class="w-full h-[400px] rounded-lg shadow-lg"></div>
                </div>

                <div class="w-full max-w-5xl mx-auto mt-8 text-center mb-20 px-4">
                    <h2 class="text-2xl md:text-4xl font-bold text-white mb-6 leading-tight drop-shadow-sm">
                        Public records and transfer data - Nationwide.
                    </h2>
                    <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed drop-shadow-sm">
                        MLS Connections for advanced marketplace filtering is limited coverage. Check to see if your
                        county is covered below.
                    </p>
                </div>
            </div>
        </div>
        <div class="w-full mx-auto text-center  h-[100px] flex items-center justify-center"
            :style="{ backgroundImage: `url(${pageVars.bgFooter})` }">
            <div class="w-full max-w-5xl mx-auto mt-auto text-center px-4 flex items-center justify-center">
                <h2 class="text-2xl md:text-4xl font-bold text-white mb-6 leading-tight drop-shadow-sm">
                    MLS Coverage By County
                </h2>
            </div>
        </div>
        <div class="w-full max-w-7xl mx-auto mt-auto text-center px-4 py-15">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-start ">
                <div class="flex flex-col justify-between" v-for="(counties, state) in props.counties" :key="state">
                    <h2
                        class="text-2xl md:text-4xl font-bold dark:text-white text-black mb-6 leading-tight drop-shadow-sm">
                        {{ stateNames[state as keyof typeof stateNames] }}
                    </h2>
                    <div class="flex flex-col max-h-[200px] overflow-y-auto px-2">
                        <Link :href="route('guest.coverage', county.slug)"
                            class="w-full flex items-center text-left hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg py-2 px-4 cursor-pointer"
                            v-for="county in counties" :key="county.id">
                        <Icon icon="tabler:map-pin" class="size-6 mr-2 text-green-500" /> {{ county.county }} County
                        </Link>

                    </div>
                </div>
            </div>
        </div>
        <div class="w-full mx-auto text-center  h-[100px] flex items-center justify-center"
            :style="{ backgroundImage: `url(${pageVars.bgFooter})` }">
            <div class="w-full max-w-5xl mx-auto mt-auto text-center px-4 flex items-center justify-center">
                <h2 class="text-2xl md:text-4xl font-bold text-white mb-6 leading-tight drop-shadow-sm">
                    #1 Deal Finding Software
                </h2>
            </div>
        </div>

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