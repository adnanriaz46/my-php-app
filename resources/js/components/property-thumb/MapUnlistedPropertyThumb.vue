<script setup lang="ts">
import {onMounted, ref} from "vue";

import {
  addUnlistedIdToUrl,
  getZillowPropertyData,
  ZillowPListingMain
} from "@/lib/zilowAndlocationUtil";

import {Icon} from "@iconify/vue";
import Label from "@/components/ui/label/Label.vue";
import Button from "@/components/ui/button/Button.vue";
import {useNumber} from "@/composables/useFormat";

const {formatNumber, formatPrice} = useNumber()

const props = defineProps<{
  address: string
}>()
const unlistedData = ref<ZillowPListingMain>(null);
const error = ref<string>();
const loading = ref<boolean>(true);
onMounted(async () => {
  await requestData();
})

const requestData = async () => {
  error.value = '';
  unlistedData.value = null;
  loading.value = true;
  const data = await getZillowPropertyData(props.address);
  loading.value = false;
  if (data?.error) {
    error.value = data.error;
    return
  }
  unlistedData.value = data;
}
const openUnlistedPropertyDetail = (address: string) => {
  addUnlistedIdToUrl(address);
  return true;
}
</script>

<template>
  <div class="h-[280px]">
    <div class="h-full w-full flex items-center justify-center" v-if="!loading && !unlistedData">
      <div class="text-sm text-center">
        The information you're looking for is currently unavailable.
        <Label v-if="address">{{ address ?? '' }}</Label>
      </div>
    </div>
    <div class="h-full w-full flex items-center justify-center" v-if="loading">
      <div class="text-sm text-center">
        <Icon icon="tabler:loader-2" class="size-8 animate-spin"/>
      </div>
    </div>

    <div v-if="!loading && unlistedData" class="h-full w-full">
      <div class="img relative h-[150px] p-2 bg-white rounded-t-lg bg-cover bg-center"
           :style="{ backgroundImage: `url(${unlistedData?.imgSrc})` }">
        <div class="absolute bottom-2 left-2 flex flex-col items-end gap-2 text-sm">
          <Button @click="openUnlistedPropertyDetail(address)" variant="default">View Detail</Button>
        </div>
        <div class="absolute bottom-2 right-2 flex flex-col items-end gap-2 text-sm">
          <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold flex">
            <Icon icon="solar:list-cross-broken" class="size-5"/>
            Unlisted
          </div>
        </div>
      </div>
      <div class="bottom-container">
        <div class="address-container py-2 px-2 truncate overflow-hidden whitespace-nowrap">
          <span>{{ address }}</span>
        </div>
        <div class="flex flex-row p5-2 gap-2 px-2">
          <div class=""><span class="font-semibold">{{ unlistedData.bedrooms }}</span> bds</div>
          <div class="">|&nbsp; <span class="font-semibold"> {{ unlistedData.bathrooms }}</span>
            ba
          </div>
          <div class="">|&nbsp; <span
              class="font-semibold"> {{ formatNumber(unlistedData.livingArea) }}</span> sqft
          </div>
        </div>
        <div class="grid grid-cols-1 pb-2 gap-2 px-2">
          <div class=""><span
              class="font-semibold"> {{ unlistedData.propertyTypeDimension }}</span></div>
        </div>
        <div class="grid grid-cols-2 justify-around bg-gray-300 dark:bg-gray-700 py-2 mt-2">
          <div class="text-xs text-center"><span
              class="font-semibold mr-1 text-sm">{{ formatPrice(unlistedData.rentZestimate) }}</span>zEst. Rent
          </div>
          <div class="text-xs text-center"><span
              class="font-semibold mr-1 text-sm">{{ formatPrice(unlistedData.price) }}</span>zEst. Price
          </div>
        </div>
        <div class="p-1 bg-transparent"></div>
      </div>
    </div>
  </div>

</template>

<style scoped>

</style>
