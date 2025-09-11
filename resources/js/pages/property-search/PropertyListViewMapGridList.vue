<script setup lang="ts">
import { ref, onMounted, computed, watch, nextTick } from 'vue'

import { focusMarker, initMap } from "./subs/mapHelper.js";
import { DBApiPropertyCount, DBApiPropertyList, paramsDBApiGetProperty } from "@/types/DBApi";

import { Icon } from '@iconify/vue'
import { PropertyThumb, PropertyThumbLoading } from "@/components/property-thumb";

import { useGridItems } from "@/composables/useScrnBreakpoints";
import { useVirtualList } from "@vueuse/core";
import { PropertySortBy, PropertyViewType, PropertyViewTypes } from "@/types/property";
import { cn } from "@/lib/utils";
import { useNumber } from "@/composables/useFormat";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem, DropdownMenuSeparator,
  DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";
import { ChevronsUpDown } from "lucide-vue-next";
import Label from "@/components/ui/label/Label.vue";
import AddToFavoritePropertyDialog from "@/components/add-to-favorite-property-dialog/AddToFavoritePropertyDialog.vue";
import Toast from "@/components/ui/toast/Toast.vue";

import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import { upgradeDialog } from '@/stores/DialogStore';

import { useToast } from '@/composables/useToast'
import { MyPropertyList } from "@/types/myList";
import { addPropertyIdToUrl } from "@/lib/zilowAndlocationUtil";
// import Button from "@/components/ui/button/Button.vue";
import ViewList from "@/pages/property-search/ViewList.vue";
import { getUser, isCountySubscribedUser, isFullAccessUser } from "@/composables/useUser";
import { useSidebar } from "@/components/ui/sidebar";
import { useConfirmDialog } from "@/composables/useConfirmDialog";
import axios from "axios";
import ConfirmDialog from "@/components/ui/confirm-dialog/ConfirmDialog.vue";

const { showToast } = useToast()

const { isMobile, toggleSidebar } = useSidebar()

const { formatNumber } = useNumber()



const props = defineProps<{
  filtersValue: paramsDBApiGetProperty
  viewType: PropertyViewType,
  propertyCount: DBApiPropertyCount | null | undefined,
  propertyList: DBApiPropertyList[] | null | undefined,
  loading: boolean,
  loadingCount: boolean,
  error: string | null,
  selectedMyListVal: MyPropertyList | null
}>()

const pageIsReady = ref<boolean>(false);

const emit = defineEmits<{
  (e: 'update-sort-by', sortBy: string): void;
  (e: 'update-my-list'): void;
}>();


const selectedMyList = ref<MyPropertyList | null>(props.selectedMyListVal);
const propertySort = ref<string>('');
const ViewType = ref<PropertyViewType>(props.viewType);

const propertyList = ref<DBApiPropertyList[] | null | undefined>(props.propertyList);
const selectionIds = ref<number[]>([]);

const propertyListContainerKey = ref<number>(0);

onMounted(async () => {
  pageIsReady.value = true;
  initMap(propertyList.value ?? [], scrollToProperty, props.filtersValue);
})

const addToFavoriteOpen = ref<boolean>(false);


const { gridPropertyItems, gridPropertyItemHeight } = useGridItems(ViewType)


// Compute rows for grid layout
const gridRows = computed(() => {
  const cols = gridPropertyItems.value
  // const cols = gridPropertyItems.value
  return Array.from(
    { length: Math.ceil((propertyList.value?.length ?? 0) / cols) },
    (_, rowIndex) => {
      return propertyList.value?.slice(
        rowIndex * cols,
        rowIndex * cols + cols
      )
    }
  )
})
// Virtual list of rows
const { list, containerProps, wrapperProps, scrollTo } = useVirtualList(
  gridRows,
  {
    itemHeight: gridPropertyItemHeight.value,
    overscan: 2,
  }
)

watch(useSidebar().state, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      initMap(propertyList.value ?? [], scrollToProperty, props.filtersValue);
    }, 500)
  }
})

watch(
  () => props.propertyList,
  (newVal) => {
    propertyList.value = newVal
    nextTick();
    if (pageIsReady.value) {

      initMap(propertyList.value ?? [], scrollToProperty, props.filtersValue);
    }
  },
  { immediate: true }
)

watch(
  () => props.selectedMyListVal,
  (newVal) => {
    selectedMyList.value = newVal
    propertySort.value = '';
  },
  { immediate: true }
)


function onSortChange(sortKey: string) {
  if (propertySort.value != sortKey) {
    propertySort.value = sortKey;
  } else {
    propertySort.value = '';
  }
  emit('update-sort-by', propertySort.value);
}

function onSelection(type: string) {
  if (type == 'add-to-list') {
    addToFavoriteOpen.value = true;
  } else if (type == 'select-all') {
    const unlockedItems = propertyList.value?.filter(item => !lockedMap.value[item.id]) ?? [];
    if (unlockedItems.length > 1000) {
      showToast('Selection Too Large', 'A maximum of 1000 items can be added to your list.', 'info');
      return false;
    }
    selectionIds.value = unlockedItems.map(item => item.id);
  } else if (type == 'hide') {
    onHandlingConfirmation('Are you sure you want to hide selected properties?', 'hide', selectionIds.value);
    selectionIds.value = [];
  } else if (type == 'de-select') {
    selectionIds.value = [];
  }
}

// Remove the watcher that was causing infinite loop
// Instead, we'll listen to the success event from the dialog


const openPropertyDetail = (propertyId: number | string, locked: boolean) => {
  if (locked) {
    openUpgrade();
    return false;
  }
  addPropertyIdToUrl(propertyId);
  return true;
}
const highlightedId = ref<number | string | null>(null)
let highlightTimeout: ReturnType<typeof setTimeout> | null = null
const scrollToProperty = (propertyId: string | number) => {
  const index = propertyList.value?.findIndex(p => p.id === propertyId) ?? -1
  if (index === -1) return

  const cols = gridPropertyItems.value
  const rowIndex = Math.floor(index / cols)

  scrollTo(rowIndex)


  highlightedId.value = propertyId

  if (highlightTimeout) {
    clearTimeout(highlightTimeout)
  }

  highlightTimeout = setTimeout(() => {
    highlightedId.value = null
    highlightTimeout = null
  }, 2000)
}

const lockedMap = computed(() => {
  const isFull = isFullAccessUser();

  const locked: Record<number, boolean> = {};

  for (const item of props.propertyList ?? []) {
    locked[item.id] = isFull
      ? false
      : !isCountySubscribedUser(item.county_state);
  }
  return locked;
});


const openUpgrade = () => {
  upgradeDialog.user = getUser() ?? null;
  upgradeDialog.upgradeDialogOpen = true;
  if (isMobile.value) {
    toggleSidebar();
  }
}


const confirmDialog = useConfirmDialog()
const onHandlingConfirmation = async (message: string, type: string, ids: number[]) => {
  const confirmed = await confirmDialog.openConfirm(message, 'Hide')
  if (confirmed) {
    if (type == 'hide') {
      try {
        const response = await axios.post(route('property.search.hide_property'), { property_ids: ids })
        if (response.data?.error) {
          showToast('Hide Property', response.data?.error, 'error')
          return;
        }
        propertyList.value = propertyList.value?.filter((p) => !ids.includes(p.id)) ?? [];
        propertyListContainerKey.value++;
        showToast('Hide Property', response.data?.success ?? 'This property cancellation request has been submitted.', 'success');
      } catch (err) {
        showToast('Hide Property', 'Error: ' + err?.toString(), 'error')
      } finally {

      }
    }
  }
}

const onThumbHideProperty = (id: number) => {
  onHandlingConfirmation('Are you sure you want to hide this property?', 'hide', [id]);
}

</script>

<template>
  <Toast />
  <ConfirmDialog v-if="confirmDialog" v-model:dialog="confirmDialog" />
  <div class="">
    <div class="w-full h-[30px] font-semibold flex" v-if="!loadingCount && propertyCount">
      <div class="flex-1 grid grid-cols-1 my-auto">
        <div v-if="propertyCount.count > 5000">FOUND {{ formatNumber(propertyCount.count) }} PROPERTIES
          <small>DISPLAYING
            5,000
            PROPERTIES</small>
          <div v-if="selectedMyList" class="text-sm text-muted-foreground"> | {{ selectedMyList.name }}</div>
        </div>
        <div v-else class="my-auto">FOUND {{ formatNumber(propertyCount.count) }} PROPERTIES
          <div v-if="selectedMyList" class="text-sm text-muted-foreground inline md:bg-primary-strong"> | {{
            selectedMyList.name
          }}
          </div>
        </div>
      </div>
      <Transition enter-active-class="transition transform duration-300 ease-out" enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100" leave-active-class="transition transform duration-200 ease-in"
        leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div class="max-w-[60px] my-auto pr-1" v-if="selectionIds.length > 0">
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Label class="text-right flex relative  bg-primary rounded-lg text-black">
                <span class="absolute top-0 left-0 py-0.5 px-1 font-medium text-sm">{{
                  selectionIds.length
                }}</span>
                <Icon icon="tabler:list-check" class="opacity-0 flex-1 size-6" />
                <ChevronsUpDown class="ml-auto size-4" />
              </Label>
            </DropdownMenuTrigger>

            <DropdownMenuContent class="w-[200px] rounded-lg text-sm" side="bottom" align="end" :side-offset="4">
              <DropdownMenuGroup>
                <DropdownMenuItem :as-child="true" @click="onSelection('select-all')">
                  <Label>
                    <Icon icon="tabler:select-all" class="size-5" />
                    Select all
                  </Label>
                </DropdownMenuItem>
                <DropdownMenuItem :as-child="true" @click="onSelection('de-select')">
                  <Label>
                    <Icon icon="tabler:deselect" class="size-5" />
                    De-Select
                  </Label>
                </DropdownMenuItem>
              </DropdownMenuGroup>
              <DropdownMenuSeparator />
              <DropdownMenuGroup>
                <DropdownMenuItem :as-child="true" @click="onSelection('add-to-list')">
                  <Label>
                    <Icon icon="tabler:heart-plus" class="size-5" />
                    Add to my list
                  </Label>
                </DropdownMenuItem>
                <DropdownMenuItem :as-child="true" @click="onSelection('hide')">
                  <Label>
                    <Icon icon="tabler:ban" class="size-5" />
                    Hide from list
                  </Label>
                </DropdownMenuItem>
              </DropdownMenuGroup>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </Transition>
      <div class="max-w-[80px] md:max-w-[150px] my-auto">
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Label class="text-right flex"
              :class="propertySort ? 'bg-primary text-black rounded-sm py-1 px-1 shadow-sm' : ''">
              <span class="ml-auto flex-1" v-if="!propertySort">Sort By</span>
              <span v-else class="flex-1 truncate overflow-hidden whitespace-nowrap">{{
                propertySort
              }}</span>
              <ChevronsUpDown class="ml-auto size-4" />
            </Label>
          </DropdownMenuTrigger>
          <DropdownMenuContent class="w-[250px] rounded-lg text-sm" side="bottom" align="end" :side-offset="4">
            <DropdownMenuGroup>
              <DropdownMenuItem v-for="(label, key) in PropertySortBy" :key="key" :as-child="true"
                @click="onSortChange(label)">
                <Label class="flex" :class="label == propertySort ? 'bg-primary text-black' : ''">
                  <div class="flex-1"> {{ label }}</div>
                  <Icon icon="tabler:square-rounded-check" class="size-4 text-black" v-if="propertySort == label" />
                </Label>
              </DropdownMenuItem>
            </DropdownMenuGroup>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </div>
  </div>

  <div v-if="error" class="text-xs text-red-600">{{ error }}</div>
  <div
    class="flex flex-col md:flex-row gap-2 md:gap-4 h-[calc(100dvh-160px)] md:h-[calc(100dvh-150px)] bg-input/30 rounded-lg max-w-[1800px] mx-auto">
    <div v-if="viewType == PropertyViewTypes.Map" class="flex-1 overflow-hidden h-1/2 md:h-full rounded-lg flex"
      id="map">
      <Icon icon="tabler:inner-shadow-bottom-right" class="animate-spin size-6 m-auto" />
    </div>
    <div v-if="ViewType === PropertyViewTypes.Map || ViewType === PropertyViewTypes.Grid"
      :class="cn('w-full md:h-full overflow-hidden', viewType == PropertyViewTypes.Map ? ` h-1/2  md:max-w-[350px] lg:max-w-[400px] xl:max-w-[800px] 2xl:max-w-[800px] ` : ` h-full `)"
      id="property-container">
      <div class="grid gap-4 w-full" :class="` grid-cols-${gridPropertyItems}`" v-if="loading">
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
        <PropertyThumbLoading />
      </div>
      <div
        :class="cn(viewType == PropertyViewTypes.Map ? `h-full` : `h-[calc(100dvh-30px-140px)]`, 'md:h-[calc(100dvh-30px-120px)]')"
        v-if="!loading && (propertyList?.length ?? 0) > 0">
        <div v-bind="containerProps" class="scroller w-full h-full overflow-auto">
          <div v-bind="wrapperProps">
            <div v-for="{ index, data: rowItems } in list" :key="index" class="grid items-center"
              :class="` grid-cols-${gridPropertyItems}`" :style="{ height: `${gridPropertyItemHeight}px` }">
              <div v-for="(item, i) in rowItems" :key="i" class="mx-auto w-full">
                <PropertyThumb :key="propertyListContainerKey" @hide-property="onThumbHideProperty"
                  @update-my-list="emit('update-my-list')" :locked="lockedMap[item.id]" class="border m-1"
                  :class="{ 'ring-2 ring-primary animate-pulse': highlightedId === item.id }"
                  @click="openPropertyDetail(item.id, lockedMap[item.id])" v-model:selection-ids="selectionIds"
                  :show-btn-favorite="true" :show-btn-hide="true" :property-list-data="item" :showSelection="true"
                  :showMapButton="true" @showInMap="focusMarker(item)" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="overflow-hidden w-full h-[calc(100dvh-30px-140px)] md:h-[calc(100dvh-30px-130px)] grid grid-cols-1"
      v-if="ViewType == PropertyViewTypes.List">
      <ViewList :filters-value="filtersValue" :error="props.error" v-model:selection-ids="selectionIds"
        :loading="loading" :property-count="propertyCount ?? null" :lock-list="lockedMap"
        :property-list="propertyList ?? []" :on-open-property="openPropertyDetail" />
    </div>

  </div>

  <AddToFavoritePropertyDialog :selectionIds="selectionIds" v-model:open="addToFavoriteOpen"
    @success="emit('update-my-list')" />
</template>
<style scoped>
.grid-cols-4 {
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.grid-cols-6 {
  grid-template-columns: repeat(6, minmax(0, 1fr));
}
</style>
