<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import type { User } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Icon } from '@iconify/vue';
import { GuestNavItem } from '@/pages/guest-page/vars/vars';
const props = defineProps<{
    mainNavItems: GuestNavItem[];
}>();


const page = usePage();
const auth = computed(() => page.props.auth as any);
const user = computed(() => auth.value?.user as User);

const isLoggedIn = computed(() => !!user.value);

const isCurrentRoute = computed(() => (url: string) => page.url === url);

const activeItemStyles = computed(
    () => (url: string) => (isCurrentRoute.value(url) ? 'text-neutral-900 dark:text-neutral-100 bg-neutral-50' : ''),
);

const mainNavItems: GuestNavItem[] = props.mainNavItems;

// Track which sections are expanded
const expandedSections = ref<Set<string>>(new Set());

const toggleSection = (sectionTitle: string) => {
    if (expandedSections.value.has(sectionTitle)) {
        expandedSections.value.delete(sectionTitle);
    } else {
        expandedSections.value.add(sectionTitle);
    }
};

const isSectionExpanded = (sectionTitle: string) => {
    return expandedSections.value.has(sectionTitle);
};

const open = ref(false);
</script>

<template>
    <div class="lg:hidden">
        <Sheet v-model:open="open">
            <SheetTrigger as-child>
                <Button variant="ghost" size="icon" class="h-9 w-9">
                    <Menu class="h-5 w-5" />
                </Button>
            </SheetTrigger>
            <SheetContent side="left"
                class="sticky top-0 w-[300px] p-6 bg-white dark:bg-neutral-950 border-r border-neutral-200 dark:border-neutral-800 h-[calc(100dvh)]">
                <!-- <SheetHeader class="flex justify-start text-left">
                    <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                    <Link :href="route('home')" class="flex items-center gap-x-2">
                    <AppLogo />
                    </Link>
                </SheetHeader> -->

                <div class="flex h-full flex-1 flex-col justify-between space-y-4 py-6 ">
                    <!-- Navigation -->
                    <nav class="-mx-3 space-y-1 overflow-x-hidden overflow-y-auto max-h-[calc(100dvh-200px)]">
                        <template v-for="item in mainNavItems" :key="item.title">
                            <!-- Direct link items -->
                            <Link v-if="item.href" :href="item.href"
                                class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-base font-medium bg-transparent "
                                :class="activeItemStyles(item.href)">
                            {{ item.title }}
                            </Link>

                            <!-- Expandable sections -->
                            <div v-else-if="item.items" class="space-y-1">
                                <button @click="toggleSection(item.title)"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-base font-medium text-neutral-900 dark:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                    <span>{{ item.title }}</span>
                                    <Icon class="size-4 transition-transform duration-300 ease-in-out"
                                        :class="{ 'rotate-180': isSectionExpanded(item.title) }"
                                        :icon="'tabler:chevron-down'" />
                                </button>

                                <!-- Expandable content -->
                                <div :class="[
                                    'overflow-hidden transition-all duration-300 ease-in-out',
                                    isSectionExpanded(item.title) ? 'max-h-96 opacity-100' : 'max-h-0 opacity-0'
                                ]">
                                    <div class="space-y-1 pl-3 pb-2">
                                        <div v-for="(subItem, index) in item.items" :key="subItem.title" :style="{
                                            transitionDelay: isSectionExpanded(item.title) ? `${index * 50}ms` : '0ms'
                                        }" class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all duration-300 ease-out transform cursor-pointer"
                                            :class="[
                                                isSectionExpanded(item.title)
                                                    ? 'translate-y-0 opacity-100'
                                                    : '-translate-y-2 opacity-0'
                                            ]">
                                            <button v-if="!subItem.href || subItem.href === '#'"
                                                @click="subItem?.action && subItem.action(); open = false;"
                                                class="w-full text-left flex items-center gap-x-3">
                                                <!-- Icon for each sub-item -->
                                                <Icon v-if="subItem.icon"
                                                    class="size-5 text-neutral-500 dark:text-neutral-400 transition-colors duration-200"
                                                    :icon="subItem.icon" />
                                                <div class="flex-1">
                                                    <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                                        {{ subItem.title }}
                                                    </div>
                                                    <div v-if="subItem.description"
                                                        class="text-xs text-neutral-500 dark:text-neutral-400">
                                                        {{ subItem.description }}
                                                    </div>
                                                </div>
                                            </button>
                                            <Link v-else :href="subItem.href" class="flex items-center gap-x-3" @click="open = false;">
                                            <!-- Icon for each sub-item -->
                                            <Icon v-if="subItem.icon"
                                                class="size-5 text-neutral-500 dark:text-neutral-400 transition-colors duration-200"
                                                :icon="subItem.icon" />
                                            <div class="flex-1">
                                                <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                                    {{ subItem.title }}
                                                </div>
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
                        </template>
                    </nav>

                    <!-- Bottom Actions -->
                    <div class="flex flex-col space-y-4">
                        <!-- Guest Actions -->
                        <template v-if="!isLoggedIn">
                            <div class="flex flex-col space-y-2 text-center">
                                <Link :href="route('login')"
                                    class="rounded-lg px-3 py-2 text-base font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                Login
                                </Link>
                                <Link :href="route('register')"
                                    class="rounded-lg bg-primary px-3 py-2 text-base font-medium text-white hover:bg-primary/90">
                                Sign Up
                                </Link>
                            </div>
                        </template>

                        <!-- Logged In User -->
                        <template v-else>
                            <Link :href="route('dashboard')"
                                class="rounded-lg py-2 text-base font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                            Dashboard
                            </Link>
                            <Link :href="route('profile.edit')"
                                class="rounded-lg py-2 text-base font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                            My Profile
                            </Link>
                            <Link method="post" :href="route('logout')"
                                class="rounded-lg py-2 text-base font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950">
                            Log out
                            </Link>
                        </template>
                    </div>
                </div>
            </SheetContent>
        </Sheet>
    </div>
</template>