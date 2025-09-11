<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: 'admin',
    },
];

interface Stats {
    from: string;
    to: string;
    recent_property_searches: number;
    recent_property_searches_prev: number;
    recent_property_views: number;
    recent_property_views_prev: number;
    recent_question: number;
    recent_question_prev: number;
    recent_users: number;
    recent_users_prev: number;
    recent_email_campaigns: number;
    recent_email_campaigns_prev: number;
}

const rangeOptions = [
    { label: '1d', value: 1 },
    { label: '1m', value: 30 },
    { label: '3m', value: 90 },
    { label: '6m', value: 180 },
    { label: '1yr', value: 365 },
];
const lastDays = ref<number>(30);

const stats = ref<Stats>({
    from: '',
    to: '',
    recent_property_searches: 0,
    recent_property_searches_prev: 0,
    recent_property_views: 0,
    recent_property_views_prev: 0,
    recent_question: 0,
    recent_question_prev: 0,
    recent_users: 0,
    recent_users_prev: 0,
    recent_email_campaigns: 0,
    recent_email_campaigns_prev: 0,
});

const loading = ref(false);

const getStats = async () => {
    loading.value = true;
    const response = await axios.get(route('admin.get_stats', {
        lastDays: lastDays.value
    }));
    loading.value = false;
    return response.data;
}

onMounted(async () => {
    stats.value = await getStats();
});

watch(lastDays, async () => {
    stats.value = await getStats();
});

function percentChange(current: number, prev: number): string {
    if (prev === 0 && current === 0) return '0%';
    if (prev === 0) return '+100%';
    const change = ((current - prev) / Math.abs(prev)) * 100;
    return `${change > 0 ? '+' : ''}${change.toFixed(2)}%`;
}

function changeClass(current: number, prev: number): string {
    if (prev === 0 && current === 0) return 'text-gray-500';
    if (current > prev) return 'text-green-600 dark:text-green-400';
    if (current < prev) return 'text-red-600 dark:text-red-400';
    return 'text-gray-500';
}

function periodLabel() {
    switch (lastDays.value) {
        case 1: return 'from yesterday';
        case 30: return 'from last month';
        case 90: return 'from last quarter';
        case 180: return 'from last 6 months';
        case 365: return 'from last year';
        default: return 'from previous period';
    }
}
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex justify-between items-center mb-4">
                <div class="text-lg font-semibold">Stats</div>
                <div class="flex md:flex-row flex-col items-center gap-2">
                    <span class="text-sm text-gray-500">{{ stats.from }} – {{ stats.to }}</span>
                    <div class="flex gap-1 ml-2 text-sm">
                        <button
                            v-for="option in rangeOptions"
                            :key="option.value"
                            :class="['px-2 py-1 rounded border', lastDays === option.value ? 'bg-white dark:bg-gray-900 border-primary text-primary dark:text-primary font-bold' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200']"
                            @click="lastDays = option.value"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                    <div class="text-sm font-medium mb-1">Property Views</div>
                    <div class="text-3xl font-bold">{{ stats.recent_property_views }}</div>
                    <div :class="['text-xs mt-1', changeClass(stats.recent_property_views, stats.recent_property_views_prev)]">
                        {{ percentChange(stats.recent_property_views, stats.recent_property_views_prev) }} {{ periodLabel() }}
                    </div>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                    <div class="text-sm font-medium mb-1">User Searches</div>
                    <div class="text-3xl font-bold">{{ stats.recent_property_searches }}</div>
                    <div :class="['text-xs mt-1', changeClass(stats.recent_property_searches, stats.recent_property_searches_prev)]">
                        {{ percentChange(stats.recent_property_searches, stats.recent_property_searches_prev) }} {{ periodLabel() }}
                    </div>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                    <div class="text-sm font-medium mb-1">Questions</div>
                    <div class="text-3xl font-bold">{{ stats.recent_question }}</div>
                    <div :class="['text-xs mt-1', changeClass(stats.recent_question, stats.recent_question_prev)]">
                        {{ percentChange(stats.recent_question, stats.recent_question_prev) }} {{ periodLabel() }}
                    </div>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                    <div class="text-sm font-medium mb-1">Email Campaigns Sent</div>
                    <div class="text-3xl font-bold">{{ stats.recent_email_campaigns }}</div>
                    <div :class="['text-xs mt-1', changeClass(stats.recent_email_campaigns, stats.recent_email_campaigns_prev)]">
                        {{ percentChange(stats.recent_email_campaigns, stats.recent_email_campaigns_prev) }} {{ periodLabel() }}
                    </div>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                    <div class="text-sm font-medium mb-1">User Signups</div>
                    <div class="text-3xl font-bold">{{ stats.recent_users }}</div>
                    <div :class="['text-xs mt-1', changeClass(stats.recent_users, stats.recent_users_prev)]">
                        {{ percentChange(stats.recent_users, stats.recent_users_prev) }} {{ periodLabel() }}
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
