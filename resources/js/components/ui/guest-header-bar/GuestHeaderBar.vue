<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { Link, usePage, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import MobileMenu from './MobileMenu.vue';
import { Icon } from '@iconify/vue';
import { UserTypes } from "@/types/userTypes";
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Separator from '../separator/Separator.vue';
import { GuestNavItem, pageVars } from '@/pages/guest-page/vars/vars';
import GuestUserMenuContent from './GuestUserMenuContent.vue';


const page = usePage();
const auth = computed(() => page.props.auth as any);
const user = computed(() => auth.value?.user as User);

const isLoggedIn = computed(() => !!user.value);

const isCurrentRoute = computed(() => (url: string) => page.url === url);

const activeItemStyles = computed(
    () => (url: string) => (isCurrentRoute.value(url) ? 'text-neutral-900 dark:text-neutral-100' : ''),
);


const emit = defineEmits<{
    (e: 'openSellsOptions'): void;
}>();

const openSellsOptions = () => {
    emit('openSellsOptions');
}

const mainNavItems: GuestNavItem[] = [
    {
        title: 'Features',
        items: [
            {
                title: 'Product Overview',
                description: 'Learn about our platform',
                href: route('guest.product_overview'),
                icon: 'tabler:stack-2'
            },
            {
                title: 'Market Coverage',
                description: 'See our market reach',
                href: route('guest.market_coverage'),
                icon: 'tabler:map-pin'
            },
            {
                title: 'Deal Finder',
                description: 'Find the best deals',
                href: route('guest.deal_finder'),
                icon: 'tabler:shopping-cart'
            },
            {
                title: 'Property AI',
                description: 'AI-powered property insights',
                href: route('guest.property_ai'),
                icon: 'tabler:sparkles'
            },
            {
                title: 'My Buy Box',
                description: 'Manage your buy box',
                href: route('guest.my_buy_box'),
                icon: 'tabler:basket-dollar'
            },
            {
                title: 'Automated Offers',
                description: 'Streamline your offers',
                href: route('guest.automated_offers'),
                icon: 'tabler:bolt'
            },
            {
                title: 'Product Demo',
                description: 'See it in action',
                href: route('guest.ai_onboarding'),
                icon: 'tabler:calendar'
            },
            {
                title: 'Mobile App Install',
                description: 'Get our mobile app',
                icon: 'tabler:device-mobile'
            }
        ]
    },
    {
        title: 'Solutions',
        items: [
            {
                title: 'Fix & Flip',
                description: 'Fix and flip investment strategies',
                href: route('guest.fix_flip'),
                icon: 'tabler:refresh'
            },
            {
                title: 'Buy & Hold',
                description: 'Long-term rental investments',
                href: route('guest.buy_and_hold'),
                icon: 'tabler:shopping-bag-check'
            },
            {
                title: 'Wholesalers',
                description: 'Wholesale real estate solutions',
                href: route('guest.wholesalers'),
                icon: 'tabler:arrows-exchange'
            },
            {
                title: 'Agents / Brokers',
                description: 'Tools for real estate professionals',
                href: route('guest.agents'),
                icon: 'tabler:replace-user'
            },
            {
                title: 'Data Solutions',
                description: 'Comprehensive data analytics',

                icon: 'tabler:chart-bar'
            },
            {
                title: 'Sell A Property',
                description: 'Property selling solutions',
                action: openSellsOptions,
                href: '#',
                icon: 'tabler:tag'
            }
        ]
    },
    {
        title: 'Company',
        items: [
            {
                title: 'About Us',
                description: 'Learn about our mission and story',
                href: '/guest/about',
                icon: 'tabler:users-group'
            },
            {
                title: 'Contact Us',
                description: 'Get in touch with our team',
                href: route('guest.contact_us'),
                icon: 'tabler:phone-call'
            }
        ]
    },
    {
        title: 'Resources',
        items: [
            {
                title: 'Revamp University',
                description: 'Educational resources and training',
                href: route('learn.index'),
                icon: 'tabler:school'
            },
            {
                title: 'Instant Comp',
                description: 'Instant property Comparable Analysis',
                href: route('guest.instant_comp'),
                icon: 'tabler:vs'
            },
            {
                title: 'Revamp Podcast',
                description: 'Listen to our latest episodes',
                href: route('guest.podcast'),
                icon: 'tabler:microphone'
            },
            {
                title: 'AI Assessment',
                description: 'AI-powered property assessments',
                href: route('guest.assessment_page'),
                icon: 'tabler:file-text-spark'
            },
            {
                title: 'Blog / Articles',
                description: 'Latest insights and articles',
                href: '/guest',
                icon: 'tabler:file-text'
            }
        ]
    },
    {
        title: 'Pricing',
        href: route('guest.pricing'),
    }
];

const hoveredMenu = ref<string | null>(null);
const hoverTimeout = ref<number | null>(null);



const handleMouseEnter = (menuTitle: string) => {
    if (hoverTimeout.value) {
        clearTimeout(hoverTimeout.value);
        hoverTimeout.value = null;
    }
    hoveredMenu.value = menuTitle;
};

const handleMouseLeave = () => {
    hoverTimeout.value = setTimeout(() => {
        hoveredMenu.value = null;
    }, 150); // Small delay to prevent menu from disappearing immediately
};

const isMouseEntered = (menuTitle: string) => {
    return hoveredMenu.value === menuTitle;
};

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <header
        class="sticky top-5 z-50 max-w-7xl rounded-lg mx-auto shadow-md backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:supports-[backdrop-filter]:bg-neutral-950/60">
        <div class="mx-auto flex h-16  items-center justify-between px-4">
            <!-- Mobile Menu -->
            <MobileMenu :mainNavItems="mainNavItems" @openSellsOptions="openSellsOptions" />
            <!-- Logo -->
            <Link :href="route('home')" class="flex items-center gap-x-2">
            <img :src="pageVars.logoBlack" alt="Revamp365.ai" class="dark:hidden h-auto w-50 md:max-w-50 md:w-auto" />
            <img :src="pageVars.logoWhite" alt="Revamp365.ai"
                class="hidden dark:block h-auto w-50 md:max-w-50 md:w-auto" />
            </Link>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex lg:items-center lg:space-x-8">
                <!-- Search Icon -->
                <Link :href="route('property.search')" class="h-5 w-5">
                <Search class="h-5 w-5" />
                </Link>

                <!-- Navigation Items -->
                <div class="flex items-center space-x-6">
                    <div v-for="item in mainNavItems" :key="item.title" class="relative cursor-pointer"
                        :class="{ 'bg-input/50 rounded-lg': isMouseEntered(item.title) }"
                        @mouseenter="handleMouseEnter(item.title)" @mouseleave="handleMouseLeave()">
                        <div class="flex items-center">
                            <Link v-if="item.href" :href="item.href"
                                class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 transition-colors hover:text-neutral-900 dark:hover:text-neutral-100"
                                :class="activeItemStyles(item.href)">
                            {{ item.title }}
                            </Link>
                            <button v-else
                                class="cursor-pointer flex items-center gap-1 px-3 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 transition-colors hover:text-neutral-900 dark:hover:text-neutral-100">
                                {{ item.title }}

                                <Icon class="size-5 transition-transform duration-300 ease-in-out"
                                    :class="{ 'rotate-180': isMouseEntered(item.title) }"
                                    :icon="isMouseEntered(item.title) ? 'tabler:chevron-up' : 'tabler:chevron-down'" />
                            </button>
                        </div>

                        <!-- Invisible bridge to prevent gap -->
                        <div v-if="item.items && hoveredMenu === item.title"
                            class="absolute left-0 top-full z-40 h-2 w-full" @mouseenter="handleMouseEnter(item.title)"
                            @mouseleave="handleMouseLeave()"></div>

                        <!-- Dropdown Menu -->
                        <div v-if="item.items && hoveredMenu === item.title"
                            class="absolute left-0 top-full z-50 mt-0 w-80 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-2 shadow-lg"
                            @mouseenter="handleMouseEnter(item.title)" @mouseleave="handleMouseLeave()">
                            <div class="grid gap-1">
                                <div v-for="subItem in item.items" :key="subItem.title"
                                    class="flex items-start gap-3 rounded-md px-3 py-2 text-sm transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                    <button v-if="!subItem.href || subItem.href === '#'"
                                        @click="subItem?.action && subItem.action()"
                                        class="w-full text-left flex items-start gap-3 cursor-pointer">
                                        <div v-if="subItem.icon"
                                            class="mt-0.5 flex h-5 w-5 items-center justify-center">
                                            <Icon :icon="subItem.icon"
                                                class="size-7 text-neutral-500 dark:text-neutral-400" />
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-neutral-900 dark:text-neutral-100">{{
                                                subItem.title }}</div>
                                            <div v-if="subItem.description"
                                                class="text-xs text-neutral-500 dark:text-neutral-400">
                                                {{ subItem.description }}
                                            </div>
                                        </div>
                                    </button>
                                    <Link v-else :href="subItem.href" class="flex items-start gap-3 cursor-pointer">
                                    <div v-if="subItem.icon" class="mt-0.5 flex h-5 w-5 items-center justify-center">
                                        <Icon :icon="subItem.icon"
                                            class="size-7 text-neutral-500 dark:text-neutral-400" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ subItem.title
                                        }}</div>
                                        <div v-if="subItem.description"
                                            class="text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ subItem.description }}
                                        </div>
                                    </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-4">
                <!-- Guest Actions -->
                <template v-if="!isLoggedIn">
                    <div class="flex items-center space-x-1 md:hidden"></div>
                    <Link :href="route('login')"
                        class="hidden px-4 py-2 md:block text-sm font-medium text-neutral-700 dark:text-neutral-300 transition-colors hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-input/50 rounded-lg">
                    Login
                    </Link>
                    <Link :href="route('register')"
                        class="hidden md:block rounded-lg bg-primary px-4 py-2 text-sm font-medium text-dark transition-colors hover:bg-primary/90">
                    Sign Up
                    </Link>
                </template>

                <!-- Logged In User -->
                <template v-else>
                    <div class="relative cursor-pointer" :class="{ 'bg-input/50 rounded-lg': isMouseEntered('user') }"
                        @click="isMouseEntered('user') ? handleMouseLeave() : handleMouseEnter('user')">
                        <div class="flex items-center cursor-pointer hover:bg-input/50 rounded-lg">
                            <button
                                class="flex  cursor-pointer items-center gap-1 px-3 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 transition-colors hover:text-neutral-900 dark:hover:text-neutral-100">
                                <Avatar class="h-6 w-6 overflow-hidden rounded-full">
                                    <AvatarImage v-if="user.profile_picture" :src="user.profile_picture"
                                        :alt="user.name" />
                                    <AvatarFallback
                                        class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white text-xs">
                                        {{ getInitials(user?.name) }}
                                    </AvatarFallback>
                                </Avatar>

                                <Icon class="size-5 transition-transform duration-300 ease-in-out"
                                    :class="{ 'rotate-180': isMouseEntered('user') }"
                                    :icon="isMouseEntered('user') ? 'tabler:chevron-up' : 'tabler:chevron-down'" />
                            </button>
                        </div>

                        <!-- Invisible bridge to prevent gap -->
                        <div v-if="hoveredMenu === 'user'" class="absolute right-0 top-full z-40 h-2 w-full"
                            @mouseenter="handleMouseEnter('user')" @mouseleave="handleMouseLeave()"></div>

                        <!-- Dropdown Menu -->
                        <GuestUserMenuContent :user="user" :hoveredMenu="hoveredMenu"
                            :handleMouseEnter="handleMouseEnter" :handleMouseLeave="handleMouseLeave" />
                    </div>
                </template>
            </div>
        </div>
    </header>

</template>

<style scoped>
/* Ensure dropdown menus appear above other content */
.relative {
    position: relative;
}

/* Smooth transitions for hover effects */
.transition-colors {
    transition: color 0.2s ease-in-out;
}

/* Backdrop blur effect for modern browsers */
@supports (backdrop-filter: blur(8px)) {
    .backdrop-blur {
        backdrop-filter: blur(8px);
    }
}
</style>