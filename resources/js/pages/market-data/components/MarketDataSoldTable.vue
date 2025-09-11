<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { getSoldUnderMarketValue } from '@/lib/DBApiUtil';
import { DBApiSoldUnderMarketValue } from '@/types/DBApi';
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
}>();

const properties = ref<DBApiSoldUnderMarketValue[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

async function loadProperties() {
    // Don't load if we're in the middle of a filter reset
    if (Object.values(props.filters).every(val => !val)) {
        return;
    }
    properties.value = [];
    loading.value = true;
    error.value = null;
    
    try {
        const params = {
            range: props.lastDays,
            state: props.filters.state || undefined,
            county: props.filters.county || undefined,
            city: props.filters.city || undefined,
            zip: props.filters.zip || undefined,
            limit: 50,
        };
        
        const response = await getSoldUnderMarketValue(params);
        
        if (response.error) {
            error.value = response.error;
        } else {
            properties.value = response.data || [];
        }
    } catch (err: any) {
        error.value = err.message || 'Failed to load properties';
    } finally {
        loading.value = false;
    }
}

function formatCurrency(value: number): string {
    if (!value || value === 0) return '$0';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
}

function formatNumber(value: number): string {
    if (!value || value === 0) return '0';
    return new Intl.NumberFormat('en-US').format(value);
}

// Create debounced load function
const debouncedLoadProperties = useDebounceFn(loadProperties, 300);

// Watch for changes in props and reload data
watch([() => props.filters, () => props.lastDays], () => {
    debouncedLoadProperties();
}, { deep: true });

onMounted(() => {
    loadProperties();
});
</script>

<template>
    <div class=" h-full">
        <div v-if="loading" class="p-8 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
            <p class="mt-2 text-sm text-muted-foreground">Loading properties...</p>
        </div>

        <!-- <div v-else-if="error" class="p-8 text-center text-red-600 dark:text-red-400">
            {{ error }}
        </div> -->

        <div v-else-if="properties.length === 0" class="p-8 text-center text-muted-foreground bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
            No properties found for the selected criteria.
        </div>

        <div v-else class="overflow-x-auto bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full">
                <thead class="">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Property
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Address
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            DOM
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Closed Price
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            AVM
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            ARV
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="property in properties" :key="property.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex-shrink-0 h-12 w-16">
                                <img 
                                    v-if="property.image" 
                                    :src="property.image" 
                                    :alt="property.address"
                                    class="h-12 w-16 object-cover rounded"
                                />
                                <div v-else class="h-12 w-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ property.address }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ formatNumber(property.dom) }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(property.close_price) }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(property.est_avm) }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ formatCurrency(property.est_arv) }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template> 