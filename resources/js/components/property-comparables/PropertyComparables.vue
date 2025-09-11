<script setup lang="ts">
import Label from "@/components/ui/label/Label.vue";
import Combobox from "@/components/ui/combobox/Combobox.vue";
import { getLimitedMailRanges, useFilterOptions } from "@/composables/useFilterOptions";
import SelectBox from "@/components/ui/select-box/SelectBox.vue";
import { ref, onMounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useScreen } from '@/composables/useScreen'
import InputNumber from "@/components/ui/input-number/InputNumber.vue";
import { Input } from '@/components/ui/input';
import { getDateBeforeDays } from "@/components/property-comparables";
import { getAvgComps, GetAvgCompsParams, getPropertyList } from "@/lib/DBApiUtil";
import { DBApi, DBApiAverageCompsProperty, DBApiPropertyList, paramsDBApiGetProperty } from "@/types/DBApi";
import { useNumber } from "@/composables/useFormat";
import { useDebounceFn } from "@vueuse/core";
import { PropertyThumb, PropertyThumbLoading } from "@/components/property-thumb";
import { Icon } from '@iconify/vue'
import PropertyDetailCompsSheet from "@/components/property-detail-sheet/PropertyDetailCompsSheet.vue";

const { formatNumber, formatPrice } = useNumber()

const { isMobile } = useScreen()


const props = defineProps<{
  propertyTypes: string[] | null;
  closedOn?: string | null;
  sqftMin: number | string | null;
  sqftMax: number | string | null;
  distanceMax?: number | string | null;
  zip: string | null;
  lat: string | number | null;
  lng: string | number | null;
}>()


const form = useForm(
  {
    propertyTypes: props.propertyTypes ?? [],
    closedOn: props.closedOn ?? '',
    sqftMin: props.sqftMin ?? '',
    sqftMax: props.sqftMax ?? '',
    distanceMax: props.distanceMax ?? 5,
    zip: props.zip,
    lat: props.lat,
    lng: props.lng,
  }
)

const selectedIds = ref<number[]>([]);

const filterOptionValues = useFilterOptions();
const mounted = ref<boolean>(false);
const propertyTypeList = ref<string[]>(Object.values(filterOptionValues.structureType));
const mileRanges = ref<{ key: string, value: string }[]>(getLimitedMailRanges(5))

const openCompsDialog = ref<boolean>(false);

const compsAverageData = ref<DBApi<DBApiAverageCompsProperty>>();
const error = ref<string>();
const loading = ref<boolean>(false);

const compsPropertyData = ref<DBApiPropertyList[]>();
const errorPD = ref<string>();
const loadingPD = ref<boolean>(false);


onMounted(async () => {
  if (!form.closedOn) {
    form.closedOn = getDateBeforeDays(180);
  }
  await requestAvgData();
  await requestPropertyListData()
  mounted.value = true;
});

watch(
  () => [
    form.propertyTypes,
    form.closedOn,
    form.sqftMin,
    form.sqftMax,
    form.distanceMax,
    form.zip,
    form.lat,
    form.lng
  ],
  useDebounceFn(async () => {
    if (mounted.value) {
      loadingPD.value = true;
      await requestAvgData();
      await requestPropertyListData()
    }
  }, 400)
);

const requestAvgData = async () => {
  error.value = '';
  loading.value = true;
  compsAverageData.value = await getAvgComps(setParams());
  loading.value = false;
  if (compsAverageData.value?.error) {
    error.value = compsAverageData.value.error;
  }
}

watch(selectedIds, (newVal) => {
  console.log('Changed:', newVal);
  requestAvgData();
}, { deep: true });

const requestPropertyListData = async () => {
  errorPD.value = '';
  loadingPD.value = true;
  compsPropertyData.value = [];
  selectedIds.value = [];
  const requestData: DBApi<DBApiPropertyList[]> = await getPropertyList(setParamsPD())
  loadingPD.value = false;
  if (requestData?.error) {
    errorPD.value = requestData.error;
  } else {
    compsPropertyData.value = requestData.data;
  }
}

const setParams = () => {
  const params: GetAvgCompsParams = {
    zip: form.zip,
    closed_on: form.closedOn,
    lat: form.lat,
    lng: form.lng,
    distance: form.distanceMax,
    property_type: form.propertyTypes,
    sqft_max: form.sqftMax,
    sqft_min: form.sqftMin,
    ids: selectedIds.value
  }

  return params;
}

const setParamsPD = () => {
  const params: paramsDBApiGetProperty = {
    _limit: 50,
    total_finished_sqft_max: form.sqftMax,
    total_finished_sqft_min: form.sqftMin,
    status: ["Closed"],
    structure_type: form.propertyTypes,
    closed_date_min: form.closedOn,
    zip: form.zip,
    comps_sub_prop_id: String(form.lat + "|" + form.lng),
    distance_max: form.distanceMax
  }
  return params;
}
const scrollContainer = ref(null)
const containerScroll = (direction) => {
  const container = scrollContainer.value
  if (!container) return

  const step = 400 // pixels per scroll step

  container.scrollBy({
    left: direction === 'right' ? step : -step,
    behavior: 'smooth',
  })
}
const selectedPropertyId = ref<number>();
const onDetailDialogOpen = (propertyId: number) => {
  selectedPropertyId.value = propertyId;
  openCompsDialog.value = true;
}

</script>

<template>
  <div class="w-full">
    <div class="bg-input animate-pulse w-full h-[300px] rounded-lg" v-if="!mounted"></div>

    <div class="w-full text-sm text-red-600 my-3" v-if="errorPD">{{ errorPD }}</div>

    <div class="form-container grid grid-cols-1 gap-3" v-if="mounted">
      <div class="w-full grid grid-cols-1 md:flex">
        <Label class="md:flex-1">
          Property Type:
        </Label>
        <Combobox class="w-full md:flex-2" :optionValues="propertyTypeList" :tag-font-size="!isMobile ? '13px' : null"
          v-model="form.propertyTypes" />
      </div>
      <div class="w-full grid grid-cols-1 md:flex gap-2">
        <Label class="md:flex-1">
          Closed On/After
        </Label>
        <Input type="date" v-model="form.closedOn" class="text-right md:flex-2 md:ml-[10px] custom-date-input" />
      </div>
      <div class="w-full grid grid-cols-1 md:flex">
        <Label class="md:flex-1">
          Sqft
        </Label>
        <div class="w-full grid grid-cols-2 md:flex-2">
          <InputNumber v-model="form.sqftMin" />
          <InputNumber v-model="form.sqftMax" />
        </div>
      </div>
      <div class="w-full grid grid-cols-1 md:flex gap-2">
        <Label class="md:flex-1">
          Max Distance
        </Label>
        <div class="w-full md:flex-2">
          <SelectBox v-model="form.distanceMax" :options="mileRanges" placeholder="Select a mail range"
            aria-label="Mail range" />
        </div>
      </div>
    </div>
    <div class=" w-full text-sm text-red-600 my-3" v-if="error && error != 'The request or response is invalid.'">
      {{ error }}
    </div>
    <div class=" w-full text-sm text-red-600 my-3" v-if="errorPD && errorPD != 'The request or response is invalid.'">
      {{ errorPD }}
    </div>

    <div class="properties-viewer w-full text-sm mt-5 bg-input/30 " v-if="loadingPD">
      <div class="flex flex-row gap-2 overflow-auto">
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
      </div>
    </div>

    <div class="properties-viewer w-full text-sm mt-5 relative bg-input/30 "
      v-if="!loadingPD && compsPropertyData?.length > 0">
      <div class="flex flex-row gap-2 overflow-auto" ref="scrollContainer">
        <div class="min-w-[350px]" v-for="property in compsPropertyData" :key="property.id">
          <PropertyThumb @click="onDetailDialogOpen(property.id)" class=" bg-input/30 border m-2"
            v-model:selectionIds="selectedIds" :propertyListData="property" :show-selection="true"
            :show-distance-from="true" />
        </div>
      </div>
      <div class="hidden md:flex absolute right-0 top-1/2 bg-input/80 rounded-full cursor-pointer select-none"
        @click="containerScroll('right')">
        <Icon icon="tabler:circle-arrow-right" class="size-8" />
      </div>
      <div class="hidden md:flex absolute left-0 top-1/2 bg-input/80 rounded-full cursor-pointer select-none"
        @click="containerScroll('left')">
        <Icon icon="tabler:circle-arrow-left" class="size-8" />
      </div>
    </div>

    <div class="bg-input animate-pulse h-[200px] rounded-lg mt-5" v-if="loading"></div>
    <div class="w-full mt-5 text-center text-sm"
      v-if="!loading && (!compsAverageData?.data || !compsAverageData?.data?.avg_sales)">No comparable average
      data
      found
    </div>

    <div class="average-data-container w-full text-sm mt-5"
      v-if="!loading && compsAverageData?.data && compsAverageData?.data?.avg_sales">
      <div class="font-semibold">{{ compsAverageData?.data?.count ?? 0 }} of
        {{ ((compsPropertyData?.length < compsAverageData?.data?.count) ? compsAverageData?.data?.count :
          compsPropertyData?.length) ?? compsAverageData?.data?.count }} matched properties </div>
          <table class="w-full table-auto border-collapse text-sm">
            <tbody>
              <tr>
                <td class="border-b py-2 font-medium">Avg DOM</td>
                <td class="border-b py-2">{{ formatNumber(compsAverageData?.data?.avg_dom) }}</td>
              </tr>
              <tr>
                <td class="border-b py-2 font-medium">Avg Sqft</td>
                <td class="border-b py-2 ">{{ formatPrice(compsAverageData?.data?.avg_sqft) }} sqft</td>
              </tr>
              <tr>
                <td class="border-b py-2 font-medium">Avg List PPSF</td>
                <td class="border-b py-2 ">{{ formatPrice(compsAverageData?.data?.avg_ppsf) }} per sqft</td>
              </tr>
              <tr>
                <td class="border-b py-2 font-medium">Avg Closed PPSF</td>
                <td class="border-b py-2 ">{{ formatPrice(compsAverageData?.data?.avg_closed_ppsf) }} per sqft</td>
              </tr>
              <tr>
                <td class="border-b py-2 font-medium">Avg Sale Price</td>
                <td class="border-b py-2 ">{{ formatPrice(compsAverageData?.data?.avg_sales) }}</td>
              </tr>
              <tr>
                <td class="border-b py-2 font-medium">Avg Distance</td>
                <td class="border-b py-2 ">{{ formatNumber(compsAverageData?.data?.avg_dist, 2) }} miles</td>
              </tr>
            </tbody>
          </table>
      </div>
    </div>
    <PropertyDetailCompsSheet v-if="openCompsDialog" :property-id="selectedPropertyId" v-model:open="openCompsDialog" />
</template>

<style scoped></style>
