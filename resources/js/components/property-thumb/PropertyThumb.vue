<script setup lang="ts">
import { HTMLAttributes, onMounted, ref } from "vue";
import { DBApiPropertyList } from "@/types/DBApi";
import { Icon } from '@iconify/vue'


import { PropertyStatusBadge } from "@/components/ui/proprety-status-badge";
import { Tooltip, TooltipContent, TooltipTrigger } from "@/components/ui/tooltip";
import { useDateFormat, useNumber, useTextFormat } from "@/composables/useFormat";
import { getMainImage, PropertyStatus } from "../../types/property";
import Separator from "@/components/ui/separator/Separator.vue";
import AddToFavoritePropertyDialog from "@/components/add-to-favorite-property-dialog/AddToFavoritePropertyDialog.vue";
import { cn } from "@/lib/utils";
import { PropertyThumbLocked } from "@/components/property-thumb/index";

const props = defineProps<{
  propertyListData: DBApiPropertyList,
  selectionIds?: number[],
  class?: HTMLAttributes['class']
  showBtnHide?: boolean,
  showBtnFavorite?: boolean,
  showSelection?: boolean,
  showDistanceFrom?: boolean,
  showMapButton?: boolean,
  locked?: boolean,
  isMap?: boolean,
}>()

const emit = defineEmits<{
  (e: 'click'): void;
  (e: 'showInMap'): void;
  (e: 'update:selection-ids', value: number[]): void
  (e: 'hide-property', value: number): void
  (e: 'update-my-list'): void
}>();

const openAddMyList = ref<boolean>(false);
const selectionId = ref<number>(null); // For individual selection

const { formatNumber, formatPrice } = useNumber()
const { formatDate } = useDateFormat()
const { formatToCapitalizeEachWord, } = useTextFormat()


const propertyImage = ref<string>('');
onMounted(async () => {
  propertyImage.value = getMainImage(props.propertyListData.image);
})

const onCheck = () => {
  const isSelected = props.selectionIds.includes(props.propertyListData.id);
  const updatedSelection = isSelected
    ? props.selectionIds.filter(id => id !== props.propertyListData.id)
    : [...props.selectionIds, props.propertyListData.id];

  emit('update:selection-ids', updatedSelection);
};

const onAddToMyList = (id: number) => {
  openAddMyList.value = true;
  selectionId.value = id;
}

const onHide = async (id: number) => {
  emit('hide-property', id);
}

const showInMapChild = () => {
  emit('showInMap');
}

</script>

<template class="relative">
  <AddToFavoritePropertyDialog v-if="openAddMyList" :selectionIds="[selectionId]" v-model:open="openAddMyList"
    @success="emit('update-my-list')" />
  <div @click="$emit('click')"
    class="rounded-lg shadow-md text-sm font-normal cursor-pointer dark:border dark:border-gray-700 hover:shadow-lg hover:dark:shadow-gray-700 transition-all duration-300 bg-white dark:bg-gray-800/40"
    v-if="!props.locked" :class="cn(props.class)">
    <div class="img relative h-[220px] p-2 bg-white rounded-t-lg bg-cover bg-center"
      :style="{ backgroundImage: `url(${propertyImage})` }">
      <div class="property-status absolute top-2 left-2">
        <PropertyStatusBadge :status="propertyListData.status" />
      </div>
      <div class="action-buttons absolute top-2 right-2 flex gap-2">
        <div v-if="showBtnHide" class="flex-1 p-1 rounded-lg bg-gray-800/40 ">
          <Tooltip>
            <TooltipTrigger as-child>
              <Icon class=" text-white hover:text-primary action-icon" icon="tabler:ban"
                @click.stop="onHide(propertyListData.id)" />
            </TooltipTrigger>
            <TooltipContent side="bottom" align="end">
              Hide from the list
            </TooltipContent>
          </Tooltip>
        </div>
        <div v-if="showBtnFavorite" class="flex-1 p-1 rounded-lg bg-gray-800/40">
          <Tooltip>
            <TooltipTrigger as-child>
              <Icon class=" text-white hover:text-primary action-icon" icon="tabler:heart"
                @click.stop="onAddToMyList(propertyListData.id)" />
            </TooltipTrigger>
            <TooltipContent side="bottom" align="end">
              Add to favorite
            </TooltipContent>
          </Tooltip>
        </div>
        <div class="flex-1 p-1 rounded-lg bg-gray-800/40 cursor-pointer select-none" v-if="showSelection"
          @click.stop="onCheck">
          <Icon
            :class="' action-icon ' + (selectionIds?.includes(propertyListData.id) ? ' text-primary ' : 'text-white hover:text-primary ')"
            :icon="selectionIds?.includes(propertyListData.id) ? 'tabler:square-rounded-check-filled' : 'tabler:square-rounded'" />
        </div>
      </div>
      <div class="absolute bottom-2 left-2 flex flex-col items-end gap-2 text-sm" v-if="showMapButton">
        <div class="flex-1 p-1 rounded-lg bg-gray-800/40">
          <Tooltip>
            <TooltipTrigger as-child>
              <Icon class=" text-white hover:text-primary action-icon" icon="tabler:map-2"
                @click.stop="showInMapChild" />
            </TooltipTrigger>
            <TooltipContent side="bottom" align="end">
              Show in map
            </TooltipContent>
          </Tooltip>
        </div>

      </div>
      <div class="absolute bottom-2 left-2 flex flex-col items-end gap-2 text-sm" v-if="showDistanceFrom">
        <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold">
          {{ propertyListData.mile_range_from_subject }}
          Miles
        </div>
      </div>
      <div class="absolute bottom-2 right-2 flex flex-col items-end gap-2 text-sm">
        <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold"
          v-if="propertyListData.status == PropertyStatus.Closed">{{
            formatDate(propertyListData.close_date)
          }}
        </div>
        <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold"
          v-if="propertyListData.status != PropertyStatus.Closed">{{ propertyListData.real_dom }} DOM
        </div>
        <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold flex">
          <Icon icon="tabler:flame" class="wholesale-icon" v-if="propertyListData.wholesale == 'Wholesale'" />
          {{ propertyListData.wholesale }}
          Listing
          <Icon icon="tabler:flame" class="wholesale-icon" v-if="propertyListData.wholesale == 'Wholesale'" />
        </div>
      </div>
    </div>
    <div class="bottom-container">
      <div class="price-container flex pb-1 p-2 gap-2 px-2">
        <div class="font-semibold text-lg/4 my-auto">{{
          propertyListData.status == PropertyStatus.Closed ? formatPrice(propertyListData.close_price) :
            formatPrice(propertyListData.list_price)
        }}
        </div>
        <div class="text-xs text-muted-foreground align-bottom my-auto truncate overflow-hidden whitespace-nowrap">
          By
          {{ propertyListData.office_info }}
        </div>
      </div>
      <div class="address-container py-1 px-2 truncate overflow-hidden whitespace-nowrap">
        <span v-if="propertyListData.wholesale != 'Wholesale'">{{ propertyListData.address }}</span>
        <span v-else><span class="blur-sm">[Hidden Address]</span>, {{
          propertyListData.address?.split(',')?.pop()?.trim()
          }}</span>
      </div>
      <div class="flex flex-row p5-2 gap-2 px-2 justify-start">
        <div class="flex-shrink flex flex-row gap-2 justify-start">
          <div class="truncate"><span class="font-semibold">{{ propertyListData.bedrooms_count }}</span> bds</div>
          <div class="truncate">|&nbsp; <span class="font-semibold"> {{ propertyListData.bathrooms_total_count }}</span>
            ba
          </div>
          <div class="truncate">|&nbsp; <span class="font-semibold"> {{
            formatNumber(propertyListData.total_finished_sqft)
              }}</span> sqft
          </div>
        </div>
        <div class="truncate"><span class="text-sm">| &nbsp; {{ propertyListData.structure_type }}</span></div>
      </div>
      <div class="grid grid-cols-3 justify-around bg-gray-300 dark:bg-gray-700 py-2 mt-2"
        v-if="propertyListData.status != PropertyStatus.Closed">
        <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
          formatPrice(propertyListData.medianrent) }}</span>Rent
        </div>
        <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
          formatPrice(propertyListData.est_avm) }}</span>AVM
        </div>
        <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
          formatPrice(propertyListData.est_arv) }}</span>ARV
        </div>
      </div>
      <div class="grid grid-cols-3 justify-around text-xs mt-1" v-if="propertyListData.status != PropertyStatus.Closed">
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Est Cash Flow</div>
          <div class="text-center">{{ formatPrice(propertyListData.est_cashflow) }}</div>
        </div>
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Delta PSF</div>
          <div class="text-center">{{ formatPrice(propertyListData.delta_psf) }}</div>
        </div>
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Est Flip Profit</div>
          <div class="text-center">{{ formatPrice(propertyListData.est_profit) }}</div>
        </div>
      </div>
      <Separator class="w-full my-2" v-if="propertyListData.status == PropertyStatus.Closed" />
      <div class="grid grid-cols-3 justify-around text-xs mt-1" v-if="propertyListData.status == PropertyStatus.Closed">
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Listed</div>
          <div class="text-center">{{ formatPrice(propertyListData.price_per_sqft) }}/sqft</div>
        </div>
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Closed</div>
          <div class="text-center">{{ formatPrice(propertyListData.price_per_sqft_closed) }}/sqft</div>
        </div>
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">B/F</div>
          <div class="text-center">{{ formatToCapitalizeEachWord(propertyListData?.buyer_financing) }}</div>
        </div>
      </div>
      <div class="p-1 bg-transparent"></div>
    </div>
  </div>
  <PropertyThumbLocked v-if="props.locked" :class="cn(props.class)" :is-map="props.isMap" @click="$emit('click')" />
</template>

<style scoped>
.action-icon {
  width: 25px;
  height: 25px;
}

.wholesale-icon {
  color: orange;
  width: 18px;
  height: 18px;

}
</style>
