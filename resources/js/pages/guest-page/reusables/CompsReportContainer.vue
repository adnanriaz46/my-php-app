<script setup lang="ts">

import { computed, onMounted, ref, nextTick } from 'vue';
import { getAvgComps, GetAvgCompsParams } from '@/lib/DBApiUtil';
import { DBApi, DBApiAverageCompsProperty, DBApiCalValData, DBApiPropertyList } from '@/types/DBApi';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Icon } from '@iconify/vue';
import { useNumber } from '@/composables/useFormat';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { ZillowPListingMain } from '@/lib/zilowAndlocationUtil';
import Button from '@/components/ui/button/Button.vue';
import CompsReportDownloader from '@/components/CompsReportDownloader.vue';
import PropertyAskAiDialog from '@/components/property-dialogs/PropertyAskAiDialog.vue';
import { getCompsReportMessage } from '@/components/property-dialogs';
import TooltipProvider from '@/components/ui/tooltip/TooltipProvider.vue';
const props = defineProps<{
    zillowData: ZillowPListingMain | null;
    formParams: GetAvgCompsParams;
    properties?: DBApiPropertyList[] | null; // all properties
    selectedIds: number[];
    calculatedValues?: DBApiCalValData | null; // Displayable Calculated Values
}>();
const address = computed(() => {
    return props.zillowData?.address?.streetAddress
        ? `${props.zillowData.address.streetAddress}, ${props.zillowData.address.city}, ${props.zillowData.address.state} ${props.zillowData.address.zipcode}`
        : 'Subject Property';
});
const previousChatPropertyID = ref<number | null>(null);
const filteredProperties = computed(() => {
    return props.properties?.filter(property => props.selectedIds.includes(property.id));
}); // Displayable Properties

const { formatNumber, formatPrice } = useNumber();

const compsAverageData = ref<DBApi<DBApiAverageCompsProperty>>(); // Displayable Average Data
const errorAverage = ref<string>();
const loadingAverage = ref<boolean>(false);
const mapRef = ref<HTMLElement | null>(null);
const downloaderRef = ref<InstanceType<typeof CompsReportDownloader> | null>(null);
let map: L.Map | null = null;
const askAiOpen = ref<boolean>(false);

const setParamsAverage = () => {
    const params: GetAvgCompsParams = {
        lat: props.formParams.lat,
        lng: props.formParams.lng,
        ids: props.selectedIds.map(id => id.toString())
    }

    return params;
}

const requestAvgData = async () => {
    errorAverage.value = '';
    loadingAverage.value = true;
    compsAverageData.value = await getAvgComps(setParamsAverage());

    if (compsAverageData.value?.error) {
        errorAverage.value = compsAverageData.value.error;
    }

    loadingAverage.value = false;
}

const initMap = async () => {

    if (!mapRef.value) {
        console.warn('Map ref not found');
        return;
    }

    // Check if the map container has dimensions
    const rect = mapRef.value.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) {
        setTimeout(() => initMap(), 200);
        return;
    }

    await nextTick();

    if (map) {
        map.off();
        map.remove();
    }

    // Initialize map with subject property location
    const subjectLat = props.zillowData?.latitude ?? 0;
    const subjectLng = props.zillowData?.longitude ?? 0;

    // Check if we have valid coordinates
    if (!subjectLat || !subjectLng || subjectLat === 0 || subjectLng === 0) {
        console.warn('Invalid coordinates for map initialization:', { subjectLat, subjectLng });
        const defaultLat = 39.9390;
        const defaultLng = -75.2510;
        map = L.map(mapRef.value).setView([defaultLat, defaultLng], 14);
    } else {
        map = L.map(mapRef.value).setView([subjectLat, subjectLng], 14);
    }

    if (!map) {
        console.error('Failed to initialize map');
        return;
    }

    // Add tile layer
    L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZHJld3JldmFtcDM2NSIsImEiOiJjbGFtbmxpdGowOTFiM3BtcXJiejZmM216In0.av2uw-r3r75vcBChtCcinw', {
        maxZoom: 19,
        attribution: '&copy; Revamp365 inc'
    }).addTo(map);


    // Add comparable property markers (blue)
    filteredProperties.value?.forEach((comp, index) => {
        if (comp.latitude && comp.longitude && comp.latitude !== 0 && comp.longitude !== 0) {
            const compIcon = L.divIcon({
                className: 'comp-marker',
                iconSize: [30, 30],
                iconAnchor: [15, 30],
                html: `<div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm border-2 border-white shadow-lg">${index + 1}</div>`
            });

            const compMarker = L.marker([comp.latitude, comp.longitude], {
                title: comp.address,
                icon: compIcon
            }).addTo(map as unknown as L.Map);

            // Add popup for comparable property
            compMarker.bindPopup(`
                <div class="py-2 px-2">
                    <div class="font-semibold text-blue-600 dark:text-blue-400 mb-1">Comparable Property #${index + 1}</div>
                    <div class="text-sm mb-2">${comp.address}</div>
                    <div class="text-xs text-muted-foreground">
                        <div>Sale Price: $${formatNumber(comp.close_price)}</div>
                        <div>${comp.bedrooms_count} bed | ${comp.bathrooms_total_count} bath | ${formatNumber(comp.total_finished_sqft)} sqft</div>
                        <div>${comp.mile_range_from_subject} miles away</div>
                    </div>
                </div>
            `);
        }
    });

    // Add subject property marker (red) if we have valid coordinates
    if (subjectLat && subjectLng && subjectLat !== 0 && subjectLng !== 0) {
        const subjectIcon = L.divIcon({
            className: 'subject-marker',
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            html: '<div class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm border-2 border-white shadow-lg">S</div>'
        });

        const subjectMarker = L.marker([subjectLat, subjectLng], {
            title: "Subject Property",
            icon: subjectIcon
        }).addTo(map as unknown as L.Map);

        // Add popup for subject property
        const subjectAddress = props.zillowData?.address?.streetAddress
            ? `${props.zillowData.address.streetAddress}, ${props.zillowData.address.city}, ${props.zillowData.address.state} ${props.zillowData.address.zipcode}`
            : 'Subject Property';

        subjectMarker.bindPopup(`
            <div class="p-2">
                <div class="font-semibold text-red-600 mb-1">Subject Property</div>
                <div class="text-sm">${subjectAddress}</div>
            </div>
        `);
    }


    // Fit bounds if we have markers
    const markers: [number, number][] = [];
    if (subjectLat && subjectLng && subjectLat !== 0 && subjectLng !== 0) {
        markers.push([subjectLat, subjectLng]);
    }
    filteredProperties.value?.forEach(comp => {
        if (comp.latitude && comp.longitude && comp.latitude !== 0 && comp.longitude !== 0) {
            markers.push([comp.latitude, comp.longitude]);
        }
    });

    if (markers.length > 0 && map) {
        const group = L.featureGroup(markers.map(coords => L.marker(coords)));
        // Type guard to satisfy TS
        const m = map as L.Map;
        m.fitBounds(group.getBounds());
    }
};

onMounted(() => {
    requestAvgData().finally(() => {
        setTimeout(() => {
            initMap();
        }, 500);
    });
});

const averagePricePerSqft = computed<number>(() => {
    const comps = filteredProperties.value ?? []
    if (!comps.length) {
        return Number(compsAverageData.value?.data?.avg_closed_ppsf ?? 0)
    }

    const values: number[] = comps
        .map((comp) =>
            comp.status === 'Closed' ? comp.price_per_sqft_closed : comp.price_per_sqft
        )
        .map((v) => Number(v))
        .filter((v) => Number.isFinite(v) && v > 0)

    if (!values.length) {
        return Number(compsAverageData.value?.data?.avg_closed_ppsf ?? 0)
    }

    const sum = values.reduce((acc, v) => acc + v, 0)
    return sum / values.length
})

const averageARVValue = computed<number>(() => {
    const livingArea = Number(props.zillowData?.livingArea ?? 0)
    return averagePricePerSqft.value * livingArea
})


</script>

<template>
    <div class="w-full flex flex-col border-1 rounded-lg">
        <!-- Header -->
        <div class="flex-shrink-0 px-6 py-4 border-b">
            <h1 class="text-2xl font-bold">Comps Report</h1>
        </div>
        <div class="h-full" v-if="!loadingAverage">
            <!-- Main Content -->
            <div class="flex-1 flex flex-col lg:flex-row ">
                <!-- Left Panel -->
                <div class="w-full h-full lg:w-1/3 p-6">
                    <div class="space-y-6">
                        <div
                            class="flex flex-col gap-2 px-4 py-3 bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 rounded-lg">
                            <div class="text-base text-gray-600 dark:text-gray-400">
                                Estimated ARV
                            </div>
                            <div class="text-xl font-bold">
                                ${{ formatNumber(averageARVValue ?? 0) }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 px-4 py-3 rounded-lg bg-input/60">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Avg Square Feet
                            </div>
                            <div class="text-xl font-bold">
                                {{ formatNumber(compsAverageData?.data?.avg_sqft ?? 0) }} sqft
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                    (vs. {{ formatNumber(zillowData?.livingArea ?? 0) }} sqft)
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 px-4 py-3 rounded-lg bg-input/60">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Avg Closed Price Per Square Foot
                            </div>
                            <div class="text-xl font-bold">
                                ${{ formatNumber(compsAverageData?.data?.avg_closed_ppsf ?? 0) }}<span
                                    class="text-sm text-muted-foreground">/sqft</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Map -->
                <div class="w-full lg:w-2/3 p-6">
                    <div ref="mapRef" class="w-full h-full min-h-[400px] rounded-lg"></div>
                </div>
            </div>

            <div class="flex-shrink-0 mb-2">
                <!-- Average Characteristics Card -->
                <Card class="bg-white dark:bg-gray-800 rounded-none shadow-none">
                    <CardHeader>
                        <CardTitle class="text-lg text-gray-900 dark:text-white">Average Characteristics</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                            <!-- Left Column -->
                            <div class="space-y-0">
                                <div
                                    class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-900 dark:text-white font-medium">Average Bedrooms</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{
                                        compsAverageData?.data?.avg_beds
                                    }}</span>
                                </div>
                                <div
                                    class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-900 dark:text-white font-medium">Average Bathrooms</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{
                                        compsAverageData?.data?.avg_baths
                                    }}</span>
                                </div>
                                <div
                                    class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-900 dark:text-white font-medium">Average Lot Size</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{
                                        formatNumber(compsAverageData?.data?.avg_lot_size ?? 0)
                                    }} sqft</span>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-0 ">
                                <!-- <div
                                        class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-gray-900 dark:text-white font-medium">Average Year
                                            Built</span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ compsAverageData?.data?.avg_year_built
                                            }}</span>
                                    </div> -->
                                <div
                                    class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-900 dark:text-white font-medium">Average Distance</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{
                                        formatNumber(compsAverageData?.data?.avg_dist ?? 0, 2)
                                    }} miles</span>
                                </div>
                                <div
                                    class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-900 dark:text-white font-medium">Average DOM</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{
                                        formatNumber(compsAverageData?.data?.avg_dom ?? 0)
                                    }}</span>
                                </div>
                                <div
                                    class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-900 dark:text-white font-medium">Average Sale</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{
                                        formatPrice(compsAverageData?.data?.avg_sales ?? 0)
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Bottom Table -->
            <div class="flex-shrink-0 p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 max-w-[calc(100dvw)] overflow-x-auto">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Individual Comparable Properties
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr class="border-b border-gray-200 dark:border-gray-600">
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">#</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">ADDRESS
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">SALE PRICE
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">SALE DATE
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">SQFT</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">PRICE/SQ
                                    FT
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">BED</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">BATH</th>
                                <!-- <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">YEAR BUILT
                                    </th> -->
                                <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">DISTANCE
                                </th>
                                <!-- <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">POOL</th> -->
                                <!-- <th class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">GARAGE
                                    </th> -->
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            <tr v-for="(comp, index) in filteredProperties" :key="comp.address"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ comp.address }} <span
                                        v-if="comp.status !== 'Closed'" class="text-xs text-muted-foreground">({{
                                            comp.status }})</span></td>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ (comp.status
                                    === 'Closed') ?
                                    formatPrice(comp.close_price) : '-'
                                }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ (comp.status === 'Closed')
                                    ? comp.close_date : '-' }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{
                                    formatNumber(comp.total_finished_sqft) }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">${{ (comp.status === 'Closed') ?
                                    comp.price_per_sqft_closed : comp.price_per_sqft
                                }} <span v-if="comp.status !== 'Closed'"
                                        class="text-xs text-muted-foreground">(L.P)</span></td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ comp.bedrooms_count }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ comp.bathrooms_total_count }}
                                </td>
                                <!-- <td class="px-4 py-3 text-gray-900 dark:text-white">{{ comp.year_built }}</td> -->
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ comp.mile_range_from_subject
                                }} mi</td>
                                <!-- <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ comp.pool ? 'Y' : '-' }}
                                    </td> -->
                                <!-- <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ comp.garage ? 'Y' : '-' }} 
                                    </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex-shrink-0 px-3">
                <div class="text-sm text-muted-foreground">
                    *(L.P) = List Price
                </div>
            </div>
            <!-- Donwload Report Button -->
            <div
                class="flex flex-col lg:flex-row flex-shrink-0 p-3 border-t border-gray-200 dark:border-gray-700 justify-end items-end gap-2">

                <Button variant="outline" class="w-full lg:w-fit" @click="downloaderRef?.downloadReport()">
                    <Icon icon="tabler:download" class="size-4" />
                    Download Report as PDF
                </Button>
                <CompsReportDownloader ref="downloaderRef" :calculatedValues="calculatedValues ?? null"
                    :compsAverageData="compsAverageData" :filteredProperties="(filteredProperties as unknown as any)"
                    :zillowData="zillowData" :arv="averageARVValue ?? 0" />
                <Button variant="default" class="w-full lg:w-fit" @click="askAiOpen = true">
                    <Icon icon="tabler:robot" class="size-4" />
                    Ask AI About Property
                </Button>
            </div>
        </div>
        <div class="flex-shrink-0 p-6 border-t border-gray-200 dark:border-gray-700" v-else>
            <div class="flex justify-center items-center h-full">
                <Icon icon="tabler:loader-2" class="animate-spin text-2xl text-gray-900 dark:text-white size-8" />
            </div>
        </div>
        <TooltipProvider>
            <PropertyAskAiDialog v-if="askAiOpen && zillowData?.zpid" :average-comps-data="compsAverageData?.data"
                :property-list-data="(filteredProperties as unknown as any)" :calculated-data="calculatedValues"
                :listing-type="'Other'" :property-address="address" :property-id="zillowData?.zpid"
                :ai-initial-message-prompt="getCompsReportMessage(zillowData, compsAverageData?.data ?? null, filteredProperties ?? [], averageARVValue)"
                v-model:previous-id="previousChatPropertyID" v-model:open="askAiOpen" />
        </TooltipProvider>
    </div>

</template>

<style scoped>
/* Custom marker styles */
.subject-marker {
    background: transparent;
    border: none;
}

.comp-marker {
    background: transparent;
    border: none;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .lg\:w-1\/3 {
        width: 100%;
    }

    .lg\:w-2\/3 {
        width: 100%;
    }
}

/* Dark mode specific styles */
.dark .bg-green-50 {
    background-color: rgba(34, 197, 94, 0.1);
}

.dark .border-green-200 {
    border-color: rgba(34, 197, 94, 0.3);
}
</style>