<script setup lang="ts">
import { initMapResuable } from '@/pages/property-search/subs/mapHelper';
import { DBApiPropertyList, paramsDBApiGetProperty } from '@/types/DBApi';
import { onMounted } from 'vue';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';


const props = defineProps<{
    properties: DBApiPropertyList[];
    filterValues?: paramsDBApiGetProperty;
}>();

const emit = defineEmits<{
    (e: 'scrollToProperty', propertyId: string | number): void
}>();


onMounted(() => {
    initMapResuable(props.properties, scrollToProperty, props.filterValues);
});

const scrollToProperty = (propertyId: string | number) => {
    const index = props.properties.findIndex(p => p.id === propertyId)
    if (index === -1) return
    emit('scrollToProperty', index);
}


</script>

<template>
    <div id="map" class="w-full h-full rounded-lg"></div>
</template>

<style scoped></style>
