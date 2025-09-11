<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from 'vue';
import { __initMapBuyer } from '@/pages/property-search/subs/mapHelper';
import { Icon } from '@iconify/vue';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

const props = defineProps<{ buyers: any[] }>();

const pageIsReady = ref<boolean>(false);

onMounted(() => {
  pageIsReady.value = true;
  // Initialize map after DOM is ready
  nextTick(() => {
    const mapElement = document.getElementById('map');
    if (mapElement) {
      __initMapBuyer(props.buyers, mapElement);
    }
  });
});

watch(() => props.buyers, (buyers) => {
  if (pageIsReady.value) {
    nextTick(() => {
      const mapElement = document.getElementById('map');
      if (mapElement) {
        __initMapBuyer(buyers, mapElement);
      }
    });
  }
});
</script>

<template>
  <!-- Main container - full screen height minus header -->
  <div class="flex flex-col md:flex-row h-[calc(100vh-80px)] md:h-[calc(100vh-80px)] rounded-lg p-2 md:p-4">
    
    <!-- Map Section - 60% width on desktop, full width on mobile -->
    <div class="w-full md:w-[60%] h-1/2 md:h-full rounded-lg flex overflow-hidden" id="map">
      <Icon icon="tabler:inner-shadow-bottom-right" class="animate-spin size-6 m-auto" />
    </div>
    
    <!-- Right Panel - 40% width on desktop, full width on mobile -->
    <div class="w-full md:w-[40%] h-1/2 md:h-full flex flex-col rounded-lg">
      <!-- Fixed Search/Filter Section -->
      <div class="flex-shrink-0 p-4 dark:bg-neutral-900 rounded-lg">
        <slot name="search-filters" />
      </div>
      
      <!-- Scrollable Buyer Cards List -->
      <div class="flex-1 overflow-y-auto dark:bg-neutral-900 px-4 pb-4 rounded-lg">
        <slot name="buyer-list" />
      </div>
    </div>
  </div>
</template>


