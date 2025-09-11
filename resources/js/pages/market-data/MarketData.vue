<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    MarketDataFilters,
    MarketDataMetrics,
    MarketDataListingWatch,
    MarketDataSoldTable
} from './components';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Market Data',
        href: 'market-data',
    },
];

interface FilterState {
    state: string;
    county: string;
    city: string;
    zip: string;
}

interface PageProps {
    counties: Array<{ key: string; value: string }>;
    states: Array<{ key: string; value: string }>;
    cities: Array<{ key: string; value: string }>;
    zips: Array<{ key: string; value: string }>;
}

const props = defineProps<PageProps>();

const rangeOptions = [
    { label: '1m', value: 30 },
    { label: '3m', value: 90 },
    { label: '6m', value: 180 },
    { label: '1yr', value: 365 },
];

const lastDays = ref<number>(30);
const filters = ref<FilterState>({
    state: '',
    county: 'Delaware',
    city: '',
    zip: '',
});

// const loading = ref(false);

// Watch for changes in filters or date range
watch([lastDays, filters], () => {
    // This will trigger child components to reload their data
}, { deep: true });

function periodLabel() {
    switch (lastDays.value) {
        case 30: return 'from last month';
        case 90: return 'from last quarter';
        case 180: return 'from last 6 months';
        case 365: return 'from last year';
        default: return 'from previous period';
    }
}

function formatDateRange() {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - lastDays.value);

    return `${startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} - ${endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
}
</script>

<template>

    <Head title="Market Data" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-1 rounded-xl overflow-hidden h-[calc(100dvh-65px)]">
            <!-- Header with Date Range & Filters -->
            <div class="flex justify-between items-center p-4">
                <div class="text-lg font-semibold">Dashboard</div>
                <div class="flex md:flex-row flex-col items-center gap-2">
                    <span class="text-sm text-muted-foreground">{{ formatDateRange() }}</span>
                    <div class="flex gap-1 ml-2 text-sm">
                        <button v-for="option in rangeOptions" :key="option.value"
                            :class="['px-2 py-1 rounded border cursor-pointer', lastDays === option.value ? 'bg-white dark:bg-gray-900 border-primary text-primary dark:text-primary font-bold' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200']"
                            @click="lastDays = option.value">
                            {{ option.label }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex flex-1 flex-col gap-4 rounded-xl px-4 overflow-y-auto">
                <!-- Filters Section -->
                <div class="mb-4">
                    <h3 class="text-sm font-semibold mb-2">Filters</h3>
                    <MarketDataFilters v-model="filters" :states="props.states" :counties="props.counties"
                        :cities="props.cities" :zips="props.zips" />
                </div>

                <!-- Key Metrics Section -->
                <div class="mb-6">
                    <MarketDataMetrics :filters="filters" :last-days="lastDays" :period-label="periodLabel()" />
                </div>

                <!-- Listing Watch Section -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold mb-2">Listing Watch <span class="text-xs text-muted-foreground">(Cumulative)</span></h3>
                    <MarketDataListingWatch :filters="filters" />
                </div>

                <!-- Sold Under Market Value Table -->
                <div class="flex-1">
                    <h3 class="text-sm font-semibold mb-2">List Of Sold Under Market Value</h3>
                    <MarketDataSoldTable :filters="filters" :last-days="lastDays" />
                </div>
                <div class="h-24"></div>
            </div>
        </div>
    </AppLayout>
</template>
