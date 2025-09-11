<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import FilterBox from '@/pages/property-search/FilterBox.vue'
import PropertyListViewMapGridList from "@/pages/property-search/PropertyListViewMapGridList.vue";
import UnlistedPropertySheet from "@/components/unlisted-property-sheet/UnlistedPropertySheet.vue";

import { PropertyViewType, PropertyViewTypes } from "@/types/property";
import { ref, onMounted } from "vue";
import { DBApi, DBApiPropertyCount, DBApiPropertyList, paramsDBApiGetProperty } from "@/types/DBApi";
import { getPropertyCount, getPropertyList } from "@/lib/DBApiUtil";
import { MyPropertyList } from "@/types/myList";
import PropertyDetailSheet from "@/components/property-detail-sheet/PropertyDetailSheet.vue";
import { paramUpdateSearchHistory, updateSearchHistory } from "@/lib/propertyUtil";
// import ViewList from "@/pages/property-search/ViewList.vue";

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Property Search',
    href: route('property.search').toString(),
  },
];

const ViewType = ref<PropertyViewType>(PropertyViewTypes.Map);
const selectedMyList = ref<MyPropertyList | null>(null);

const propertyList = ref<DBApiPropertyList[] | undefined | null>([]);
const loadingP = ref<boolean>(false);
const loadingPC = ref<boolean>(false);
const errorP = ref<string | null>('');

const propertyCount = ref<DBApiPropertyCount | undefined | null>(null);

const defaultParams: paramsDBApiGetProperty = {
  status: ['Active'],
  all_wholesale: 1,
  delta_min: 150,
}

const mapCompKey = ref(0)

function refreshMap() {
  mapCompKey.value++
}

const params = ref<paramsDBApiGetProperty>(defaultParams);


onMounted(async () => {
  // await fetchPropertyCount(defaultParams);
  // await fetchPropertyList(defaultParams);
});


// watch(ViewType, () => {
//   refreshMap();
// })


const onApplyFilters = ({ filter, initial = false }: {
  filter: paramsDBApiGetProperty;
  initial?: boolean;
}) => {
  console.log('onApplyFilters', filter, initial);

  if (initial) {
    params.value = filter;
  } else {
    params.value = { ...params.value, ...filter };
  }
  fetchPropertyCount(params.value);
  fetchPropertyList(params.value);
  filterHistorySave(params.value)
};

const onSortBy = (shortBy: string) => {
  params.value = { ...params.value, order_by: shortBy };
  fetchPropertyCount(params.value);
  fetchPropertyList(params.value);
}

const fetchPropertyCount = async (params: paramsDBApiGetProperty) => {
  loadingP.value = true;
  loadingPC.value = true;
  errorP.value = null;
  propertyList.value = [];
  propertyCount.value = null;

  const request: DBApi<DBApiPropertyCount> = await getPropertyCount(params);
  loadingPC.value = false;
  if (request?.error) {
    errorP.value = request.error;
    return;
  }
  propertyCount.value = request?.data;
}
const fetchPropertyList = async (params: paramsDBApiGetProperty) => {
  loadingP.value = true;
  errorP.value = null;
  propertyList.value = [];

  const request: DBApi<DBApiPropertyList[]> = await getPropertyList(params);
  loadingP.value = false;
  if (request?.error) {
    errorP.value = request.error;
    return;
  }
  propertyList.value = request?.data;

}

const filterHistorySave = (params: paramsDBApiGetProperty) => {
  updateSearchHistory(params as paramUpdateSearchHistory)
}

window.addEventListener('error', function (event) {
  if (event.message.includes('Invalid LatLng')) {
    event.preventDefault(); // Suppresses from appearing in the console
  }
});

const filterBoxRef = ref<InstanceType<typeof FilterBox> | null>(null)
const filterBoxKey = ref(0)

const myListReload = () => {
  filterBoxRef.value?.fetchMyList()
}

</script>
<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <UnlistedPropertySheet />
    <PropertyDetailSheet />

    <Head title="Property Search" />
    <div class="flex flex-col gap-1 md:gap-2 rounded-xl px-3 pt-2 overflow-auto">
      <div class="filter-container rounded-md">
        <FilterBox ref="filterBoxRef" :defaultParams="defaultParams" :viewType="ViewType"
          @updateMyList="selectedMyList = $event; refreshMap()" @updateFilters="onApplyFilters" :key="filterBoxKey"
          @updateViewType="ViewType = $event; refreshMap()" />
      </div>
      <div class="view-container overflow-auto rounded-md z-0">
        <PropertyListViewMapGridList :selectedMyListVal="selectedMyList" :loading="loadingP" :loadingCount="loadingPC"
          :error="errorP" @updateSortBy="onSortBy" :propertyCount="propertyCount" :filters-value="params"
          :propertyList="propertyList" :key="mapCompKey" :viewType="ViewType" @update-my-list="myListReload" />

      </div>
    </div>
  </AppLayout>
</template>
