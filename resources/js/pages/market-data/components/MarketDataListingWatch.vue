<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { getStatusActivity } from '@/lib/DBApiUtil';
import { DBApiStatusActivity } from '@/types/DBApi';
import { useDebounceFn } from '@vueuse/core';

interface FilterState {
    state: string;
    county: string;
    city: string;
    zip: string;
}

const props = defineProps<{
    filters: FilterState;
}>();

const statusData = ref<DBApiStatusActivity | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

async function loadStatusData() {
    // Don't load if we're in the middle of a filter reset
    if (Object.values(props.filters).every(val => !val)) {
        return;
    }
    
    loading.value = true;
    error.value = null;
    
    try {
        const params = {
            state: props.filters.state || undefined,
            county: props.filters.county || undefined,
            city: props.filters.city || undefined,
            zip: props.filters.zip || undefined,
        };
        
        const response = await getStatusActivity(params);
        
        if (response.error) {
            error.value = response.error;
        } else {
            statusData.value = response.data || null;
        }
    } catch (err: any) {
        error.value = err.message || 'Failed to load status data';
    } finally {
        loading.value = false;
    }
}

function getStatusColor(status: string): string {
    switch (status) {
        case 'Active':
            return 'bg-green-500';
        case 'ActiveUnderContract':
            return 'bg-purple-500';
        case 'Pending':
            return 'bg-purple-500';
        case 'Closed':
            return 'bg-red-500';
        case 'Canceled':
            return 'bg-yellow-500';
        case 'Withdrawn':
            return 'bg-red-500';
        case 'Expired':
            return 'bg-red-500';
        case 'ComingSoon':
            return 'bg-orange-500';
        case 'TempOffMarket':
            return 'bg-purple-500';
        default:
            return 'bg-gray-500';
    }
}

function getStatusLabel(status: string): string {
    switch (status) {
        case 'ActiveUnderContract':
            return 'Active Under Contract';
        case 'TempOffMarket':
            return 'Temp Off Market';
        default:
            return status;
    }
}

function getStatusSuffix(status: string): string {
    switch (status) {
        case 'Pending':
            return ' (Last 7 days)';
        case 'Closed':
            return ' (Last 7 days)';
        default:
            return '';
    }
}

function formatNumber(value: number): string {
    return new Intl.NumberFormat('en-US').format(value);
}

// Create debounced load function
const debouncedLoadStatusData = useDebounceFn(loadStatusData, 300);

// Watch for changes in props and reload data
watch(() => props.filters, () => {
    debouncedLoadStatusData();
}, { deep: true });

onMounted(() => {
    loadStatusData();
});
</script>

<template>
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="i in 10" :key="i" class="animate-pulse">
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded mb-2"></div>
            <div class="h-2 bg-gray-300 dark:bg-gray-600 rounded"></div>
        </div>
    </div>

    <div v-else-if="error" class="text-red-600 dark:text-red-400 text-center py-8">
        {{ error }}
    </div>

    <div v-else-if="statusData" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-4">
            <div v-for="[status, data] in Object.entries(statusData).slice(0, 5)" :key="status" class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-sm font-medium">
                        {{ getStatusLabel(status) }} - {{ formatNumber(data.count) }}{{ getStatusSuffix(status) }}
                    </div>
                    <div class="mt-1">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div 
                                :class="['h-2 rounded-full', getStatusColor(status)]"
                                :style="{ width: `${data.percentage}%` }"
                            ></div>
                        </div>
                    </div>
                </div>
                <div class="ml-4 text-sm text-muted-foreground">
                    {{ data.percentage.toFixed(2) }}%
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4">
            <div v-for="[status, data] in Object.entries(statusData).slice(5)" :key="status" class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-sm font-medium">
                        {{ getStatusLabel(status) }} - {{ formatNumber(data.count) }}{{ getStatusSuffix(status) }}
                    </div>
                    <div class="mt-1">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div 
                                :class="['h-2 rounded-full', getStatusColor(status)]"
                                :style="{ width: `${data.percentage}%` }"
                            ></div>
                        </div>
                    </div>
                </div>
                <div class="ml-4 text-sm text-muted-foreground">
                    {{ data.percentage.toFixed(2) }}%
                </div>
            </div>
        </div>
    </div>
</template> 