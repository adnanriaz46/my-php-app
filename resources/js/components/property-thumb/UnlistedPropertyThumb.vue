<script setup lang="ts">

import {
  addUnlistedIdToUrl,
  UnlistedSavedListTypes,
} from "@/lib/zilowAndlocationUtil";

import {Icon} from "@iconify/vue";

import {useNumber} from "@/composables/useFormat";

const {formatNumber, formatPrice} = useNumber()

const props = defineProps<{
  address: UnlistedSavedListTypes,
  selectionAddresses?: UnlistedSavedListTypes[];
  selectionShow?: boolean,
}>()

const onCheck = () => {
  const isSelected = props.selectionAddresses.some(a => a.zpid === props.address.zpid && a.list_price === props.address.list_price && a.zEstimateRent === props.address.zEstimateRent);
  const updatedSelection = isSelected
      ? props.selectionAddresses.filter(a => a.zpid !== props.address.zpid || a.list_price !== props.address.list_price || a.zEstimateRent !== props.address.zEstimateRent)
      : [...props.selectionAddresses, props.address];

  emit('update:selection-addresses', updatedSelection);
};

const emit = defineEmits<{
  (e: 'update:selection-addresses', value: number[]): void
}>();


const openUnlistedPropertyDetail = (address: string) => {
  addUnlistedIdToUrl(address);
  return true;
}
</script>

<template>
  <div @click="openUnlistedPropertyDetail(address.address)"
       class="h-[300px] shadow-sm border rounded-lg text-sm cursor-pointer">
    <div class="h-full w-full">
      <div class="img relative h-[150px] p-2 bg-white rounded-t-lg bg-cover bg-center"
           :style="{ backgroundImage: `url(${address?.image})` }">
        <div class="absolute top-2 right-2 flex flex-col items-end gap-2 text-sm">
          <div class="flex-1 p-1 rounded-lg bg-gray-800/40 cursor-pointer select-none" v-if="selectionShow"
               @click.stop="onCheck">
            <Icon
                :class="'action-icon ' + (selectionAddresses?.some(a => a.zpid === props.address.zpid && a.list_price === props.address.list_price && a.zEstimateRent === props.address.zEstimateRent) ? 'text-primary' : 'text-white hover:text-primary')"
                :icon="selectionAddresses?.some(a => a.zpid === props.address.zpid && a.list_price === props.address.list_price && a.zEstimateRent === props.address.zEstimateRent) ? 'tabler:square-rounded-check-filled' : 'tabler:square-rounded'"
            />
          </div>
        </div>
        <div class="absolute bottom-2 right-2 flex flex-col items-end gap-2 text-sm">
          <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold flex gap-1">
            <Icon icon="solar:list-cross-broken" class="size-5"/>
            Unlisted
          </div>
        </div>
      </div>
      <div class="bottom-container">
        <div class="address-container py-2 px-2 truncate overflow-hidden whitespace-nowrap">
          <span>{{ address.address }}</span>
        </div>
        <div class="flex flex-row p5-2 gap-2 px-2">
          <div class=""><span class="font-semibold">{{ address.beds }}</span> bds</div>
          <div class="">|&nbsp; <span class="font-semibold"> {{ address.baths }}</span>
            ba
          </div>
          <div class="">|&nbsp; <span
              class="font-semibold"> {{ formatNumber(address.sqft) }}</span> sqft
          </div>
        </div>
        <div class="grid grid-cols-1 pb-2 gap-2 px-2">
          <div class=""><span
              class="font-semibold"> {{ address.propertyType }}</span></div>
        </div>
        <div class="grid grid-cols-2 justify-around bg-gray-300 dark:bg-gray-700 py-2 mt-2">
          <div class="text-xs text-center"><span
              class="font-semibold mr-1 text-sm">{{ formatPrice(address.zEstimateRent) }}</span>zEst. Rent
          </div>
          <div class="text-xs text-center"><span
              class="font-semibold mr-1 text-sm">{{ formatPrice(address.list_price) }}</span>zEst. Price
          </div>
        </div>
        <div class="p-1 bg-transparent"></div>
      </div>
    </div>
  </div>

</template>

<style scoped>
.action-icon {
  width: 25px;
  height: 25px;
}
</style>
