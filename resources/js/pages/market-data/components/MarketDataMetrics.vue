<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { getSalesActivity } from '@/lib/DBApiUtil';
import { DBApiSaleActivity } from '@/types/DBApi';
import { useDebounceFn } from '@vueuse/core';

interface FilterState {
    state: string;
    county: string;
    city: string;
    zip: string;
}

const props = defineProps<{
    filters: FilterState;
    lastDays: number;
    periodLabel: string;
}>();

const metrics = ref<DBApiSaleActivity | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

async function loadMetrics() {
    // Don't load if we're in the middle of a filter reset
    if (Object.values(props.filters).every(val => !val)) {
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const params = {
            range: props.lastDays,
            state: props.filters.state || undefined,
            county: props.filters.county || undefined,
            city: props.filters.city || undefined,
            zip: props.filters.zip || undefined,
        };

        const response = await getSalesActivity(params);

        if (response.error) {
            error.value = response.error;
        } else {
            metrics.value = response.data || null;
        }
    } catch (err: any) {
        error.value = err.message || 'Failed to load metrics';
    } finally {
        loading.value = false;
    }
}

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

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
}

function formatNumber(value: number, decimals: number = 2): string {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: decimals,
    }).format(value);
}

// Create debounced load function
const debouncedLoadMetrics = useDebounceFn(loadMetrics, 300);

// Watch for changes in props and reload data
watch([() => props.filters, () => props.lastDays], () => {
    debouncedLoadMetrics();
}, { deep: true });

onMounted(() => {
    loadMetrics();
});
</script>

<template>
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="i in 8" :key="i"
            class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px] animate-pulse">
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded mb-2"></div>
            <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded mb-2"></div>
            <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded"></div>
        </div>
    </div>

    <div v-else-if="error" class="text-red-600 dark:text-red-400 text-center py-8">
        {{ error }}
    </div>

    <div v-else-if="metrics" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <!-- Number Sold Under Market Value -->
        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Number Sold Under Market Value</div>
            <div class="text-3xl font-bold">{{ formatNumber(metrics.sold_under_market_value.current) }}</div>
            <div
                :class="['text-xs mt-1', changeClass(metrics.sold_under_market_value.current, metrics.sold_under_market_value.previous)]">
                {{ percentChange(metrics.sold_under_market_value.current, metrics.sold_under_market_value.previous) }}
                {{ periodLabel }} ({{ formatNumber(metrics.sold_under_market_value.previous) }})
            </div>
        </div>

        <!-- Average Sale Price -->
        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Average Sale Price</div>
            <div class="text-3xl font-bold">{{ formatCurrency(metrics.average_sale_price.current) }}</div>
            <div
                :class="['text-xs mt-1', changeClass(metrics.average_sale_price.current, metrics.average_sale_price.previous)]">
                {{ percentChange(metrics.average_sale_price.current, metrics.average_sale_price.previous) }} {{
                    periodLabel }} ({{ formatCurrency(metrics.average_sale_price.previous) }})
            </div>
        </div>

        <!-- Average closed PPSF -->
        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Average closed PPSF</div>
            <div class="text-3xl font-bold">${{ formatNumber(metrics.average_ppsfc.current, 1) }}/sqft</div>
            <div :class="['text-xs mt-1', changeClass(metrics.average_ppsfc.current, metrics.average_ppsfc.previous)]">
                {{ percentChange(metrics.average_ppsfc.current, metrics.average_ppsfc.previous) }} {{ periodLabel }}
                (${{ formatNumber(metrics.average_ppsfc.previous) }}/sqft)
            </div>
        </div>

        <!-- Average DOM -->
        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Average DOM</div>
            <div class="text-3xl font-bold">{{ formatNumber(metrics.average_dom.current) }}</div>
            <div :class="['text-xs mt-1', changeClass(metrics.average_dom.current, metrics.average_dom.previous)]">
                {{ percentChange(metrics.average_dom.current, metrics.average_dom.previous) }} {{ periodLabel }} ({{
                    formatNumber(metrics.average_dom.previous) }})
            </div>
        </div>

        <!-- Number of flipped properties -->
        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Number of flipped properties</div>
            <div class="text-3xl font-bold">{{ formatNumber(metrics.flip_count.current) }}</div>
            <div :class="['text-xs mt-1', changeClass(metrics.flip_count.current, metrics.flip_count.previous)]">
                {{ percentChange(metrics.flip_count.current, metrics.flip_count.previous) }} from last year ({{
                    formatNumber(metrics.flip_count.previous) }})
            </div>
        </div>

        <!-- New Listings -->
        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">New Listings</div>
            <div class="text-3xl font-bold">{{ formatNumber(metrics.new_listings.current) }}</div>
            <div :class="['text-xs mt-1', changeClass(metrics.new_listings.current, metrics.new_listings.previous)]">
                {{ percentChange(metrics.new_listings.current, metrics.new_listings.previous) }} {{ periodLabel }} ({{
                    formatNumber(metrics.new_listings.previous) }})
            </div>
        </div>

        <!-- Pending Sales -->
        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Pending Sales</div>
            <div class="text-3xl font-bold">{{ formatNumber(metrics.pending_count.current) }}</div>
            <div class="text-xs mt-1 text-gray-500">
                Current pending sales
            </div>
        </div>

        <!-- Closed Properties -->
        <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Closed Properties</div>
            <div class="text-3xl font-bold">{{ formatNumber(metrics.closed_count.current) }}</div>
            <div :class="['text-xs mt-1', changeClass(metrics.closed_count.current, metrics.closed_count.previous)]">
                {{ percentChange(metrics.closed_count.current, metrics.closed_count.previous) }} {{ periodLabel }} ({{
                    formatNumber(metrics.closed_count.previous) }})
            </div>
        </div>
    </div>
</template>