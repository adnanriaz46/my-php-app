<script setup lang="ts">
import { ref, watch, computed, nextTick } from 'vue';
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import { Label } from '@/components/ui/label';

interface FilterState {
    state: string;
    county: string;
    city: string;
    zip: string;
}

const props = defineProps<{
    modelValue: FilterState;
    states: { key: string; value: string }[];
    counties: { key: string; value: string }[];
    cities: { key: string; value: string }[];
    zips: { key: string; value: string }[];
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: FilterState): void;
}>();

const filters = ref<FilterState>({ ...props.modelValue });

// State options with default empty option
const stateOptions = computed(() => [
    ...props.states
]);

// County options with default empty option
const countyOptions = computed(() => [
    ...props.counties
]);

// City options with default empty option
const cityOptions = computed(() => [
    ...props.cities
]);

// Zip options with default empty option
const zipOptions = computed(() => [
    ...props.zips
]);

// Flag to prevent API calls during filter resets
const isResetting = ref(false);

// Watch for changes in filters and emit updates
watch(filters, (newFilters) => {
    if (!isResetting.value) {
        emit('update:modelValue', { ...newFilters });
    }
}, { deep: true });

// Watch for changes in state to reset dependent fields
watch(() => filters.value.state, (newState) => {
    if (newState) {
        isResetting.value = true;
        // Reset county, city, and zip when state changes
        // filters.value.county = '';
        filters.value.city = '';
        filters.value.zip = '';
        // Emit the updated filters after reset
        nextTick(() => {
            emit('update:modelValue', { ...filters.value });
            isResetting.value = false;
        });
    }
});

// Watch for changes in county to set state and reset dependent fields
watch(() => filters.value.county, (newCounty) => {
    if (newCounty) {
        isResetting.value = true;

        // Find the selected county to extract state
        const selectedCounty = props.counties.find(county => county.key === newCounty);
        if (selectedCounty) {
            // Extract state from county value (e.g., "Delaware, PA" -> "PA")
            const stateMatch = selectedCounty.value.match(/,\s*([A-Z]{2})$/);
            if (stateMatch) {
                filters.value.state = stateMatch[1];
            }
        }

        // Reset city and zip when county changes
        filters.value.city = '';
        filters.value.zip = '';

        // Emit the updated filters after reset
        nextTick(() => {
            emit('update:modelValue', { ...filters.value });
            isResetting.value = false;
        });
    }
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <Label class="text-xs font-medium mb-1 block">State</Label>
            <SelectBox v-model="filters.state" :options="stateOptions" placeholder="Choose an option..."
                :allow-empty="true" class="w-full" />
        </div>

        <div>
            <Label class="text-xs font-medium mb-1 block">County</Label>
            <SelectBox v-model="filters.county" :options="countyOptions" placeholder="Choose an option..."
                :allow-empty="true" class="w-full" />
        </div>

        <div>
            <Label class="text-xs font-medium mb-1 block">City</Label>
            <SelectBox v-model="filters.city" :options="cityOptions" placeholder="Choose an option..."
                :allow-empty="true" class="w-full" />
        </div>

        <div>
            <Label class="text-xs font-medium mb-1 block">Zip Code</Label>
            <SelectBox v-model="filters.zip" :options="zipOptions" placeholder="Choose an option..." :allow-empty="true"
                class="w-full" />
        </div>
    </div>
</template>