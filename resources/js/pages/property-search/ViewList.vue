<script setup lang="ts">
import { computed, ref } from "vue";
import { DBApiPropertyCount, DBApiPropertyList, paramsDBApiGetProperty } from "@/types/DBApi";
import {
  addColumn,
  formatValue,
  getColumns,
  isColumnExists,
  ListTableColumn,
  removeColumn,
  tableColumns
} from "./subs/listViewHelper"
import { useVirtualList } from "@vueuse/core";

import { getMainImage } from "@/types/property";
import {
  Dialog,
  // DialogClose,
  DialogContent,
  // DialogFooter,
  DialogHeader,
  DialogTitle
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Icon } from '@iconify/vue'


const props = defineProps<{
  selectionIds: number[],
  filtersValue: paramsDBApiGetProperty
  propertyCount: DBApiPropertyCount | null,
  propertyList: DBApiPropertyList[],
  loading: boolean,
  error: string | null,
  class?: string,
  lockList: Record<number, boolean>,
  onOpenProperty: (propertyId: number | string, locked: boolean) => void,
}>()

const emit = defineEmits<{
  (e: 'update:selection-ids', value: number[]): void
}>();

const selectedTableColumns = ref<ListTableColumn[]>(getColumns(
  'id',
  'image',
  'status',
  'wholesale',
  'list_price',
  'address',
  'bedrooms_count',
  'bathrooms_total_count',
  'total_finished_sqft',
  'structure_type',
  // 'medianrent',
  'office_info',
  'est_avm',
  'est_arv',
  'est_cashflow',
  'delta_psf',
  'est_profit',
  // 'latitude',
  // 'longitude',
  'real_dom',
  // 'close_date',
  // 'buyer_financing',
  // 'dom',
  'mls_number',
  // 'low_comps_ppsf',
  // 'high_comps_ppsf',
  // 'listing_entry_date',
  'price_per_sqft',
  // 'close_price',
  // 'price_per_sqft_closed',
  // 'list_agent_email',
  // 'seller_avm',
  // 'seller_arv',
  // 'seller_est_flip_rehab',
  // 'seller_est_flip_profit',
  // 'seller_est_rental_rehab',
  // 'seller_est_cashflow',
  // 'seller_avg_rent',
  'accuracy_score_value',
  // 'accuracy_score_rent',
  'short_list_price',
  'county_state',
));


const rowHeight = 60
const _containerRef = ref();

const reactiveList = computed(() => props.propertyList);

const { list: virtualRows, containerProps, wrapperProps } = useVirtualList(
  reactiveList,
  {
    itemHeight: rowHeight,
    overscan: 5,
  }
)

const showSelectColumnsDialog = ref<boolean>(false);
const searchColumnString = ref<string>('');

const filteredColumns = computed(() => {
  const keyword = searchColumnString.value.toLowerCase().trim();
  if (!keyword) return tableColumns;

  return tableColumns.filter(col =>
    col.title.toLowerCase().includes(keyword) ||
    col.shortTitle.toLowerCase().includes(keyword)
  );
});

const onSelectColumn = (item: ListTableColumn) => {
  if (isColumnExists(item.key, selectedTableColumns.value)) {
    selectedTableColumns.value = removeColumn(item.key, selectedTableColumns.value);
  } else {
    selectedTableColumns.value = addColumn(item.key, selectedTableColumns.value);
  }
}

const onSelectAllColumn = () => {
  selectedTableColumns.value = tableColumns;
}

const onCheck = (propertyId: number) => {
  const isSelected = props.selectionIds.includes(propertyId);
  const updatedSelection = isSelected
    ? props.selectionIds.filter(id => id !== propertyId)
    : [...props.selectionIds, propertyId];

  emit('update:selection-ids', updatedSelection);
};

const moveColumnUp = (index: number) => {
  if (index <= 0) return;
  const temp = selectedTableColumns.value[index];
  selectedTableColumns.value[index] = selectedTableColumns.value[index - 1];
  selectedTableColumns.value[index - 1] = temp;
}

const moveColumnDown = (index: number) => {
  if (index >= selectedTableColumns.value.length - 1) return;
  const temp = selectedTableColumns.value[index];
  selectedTableColumns.value[index] = selectedTableColumns.value[index + 1];
  selectedTableColumns.value[index + 1] = temp;
}

</script>

<template>
  <div class="py-1 h-[40px]">
    <Button variant="filter" class="relative h-[30px]" @click="showSelectColumnsDialog = true">
      Select Columns
      <div class="absolute py-1 px-2 rounded-sm -right-1 -top-1 bg-primary text-black text-xs">
        {{ selectedTableColumns.length }}
      </div>
    </Button>
  </div>
  <div
    class="w-full overflow-auto scroll-smooth custom-scrollbar  text-sm h-[calc(100dvh-30px-140px-40px)] md:h-[calc(100dvh-30px-130px-40px)]"
    ref="_containerRef" v-bind="containerProps">
    <div class=" max-w-full">
      <!-- Table Header -->
      <div class="flex sticky top-0 z-10 text-left border-b bg-input w-max rounded-t-lg">
        <div class="px-2 py-2 flex-1 font-medium w-[45px]">
          <Icon icon="solar:check-read-bold-duotone" width="24" height="24" />
        </div>
        <div :title="column.title" v-for="column in selectedTableColumns" :key="column.key"
          class="px-2 py-2 flex-1 font-medium "
          :style="column.columnWidth ? { maxWidth: column.columnWidth + 'px', minWidth: column.columnWidth + 'px' } : ``">
          {{ column.shortTitle }}
        </div>
      </div>
      <!-- Virtualized Body -->
      <div v-bind="wrapperProps">
        <!--- Loading --->
        <template v-if="loading">
          <div class="flex border-b hover:bg-gray-100 dark:hover:bg-gray-800 w-max bg-input/50 animate-pulse"
            v-for="(id) in [1, 2, 3, 4, 5, 6, 7]" :key="id">
            <div v-for="column in selectedTableColumns" :key="column.key" class="px-2 py-2 flex-1 font-medium flex"
              :style="column.columnWidth ? { maxWidth: column.columnWidth + 'px', minWidth: column.columnWidth + 'px' } : ``">
              &nbsp;
            </div>
          </div>
        </template>
        <!--- Content Wrapper --->
        <div v-for="{ data } in virtualRows" :key="data.id"
          class="flex border-b hover:bg-gray-100 dark:hover:bg-gray-800 w-max cursor-pointer" :style="{ height: rowHeight + 'px' }"
          @click="onOpenProperty(data.id, props.lockList[data.id])">
          <template v-if="props.lockList[data.id]">
            <div class="px-2 py-2 flex-1 font-medium flex select-none w-[45px]">
              <Icon icon="solar:lock-keyhole-bold-duotone" class="size-6 my-auto" />
            </div>
            <div title="Locked" v-for="column in selectedTableColumns" :key="column.key"
              class="px-2 py-2 flex-1 font-normal flex"
              :style="column.columnWidth ? { maxWidth: column.columnWidth + 'px', minWidth: column.columnWidth + 'px' } : ``">
              <div class="bg-input/30 w-full rounded-lg"></div>
            </div>
          </template>
          <template v-else>
            <div class="px-2 py-2 flex-1 font-medium flex select-none w-[45px]">
              <Icon icon="solar:check-square-bold" class="size-6 my-auto text-primary"
                v-if="selectionIds.includes(data.id)" @click.stop="onCheck(data.id)" />
              <Icon v-if="!selectionIds.includes(data.id)" @click.stop="onCheck(data.id)"
                icon="solar:minus-square-line-duotone" class="size-6 my-auto" />
            </div>
            <div :title="data[column.key] ?? ''" v-for="column in selectedTableColumns" :key="column.key"
              class="px-2 py-2 flex-1 font-normal flex"
              :style="column.columnWidth ? { maxWidth: column.columnWidth + 'px', minWidth: column.columnWidth + 'px' } : ``">
              <img class="rounded-lg object-cover w-full" v-if="column.format == 'img'"
                :src="getMainImage(data[column.key])" alt="">
              <!--            <CustomTooltip :text="formatValue(data[column.key] ?? '', column.format)">-->
              <div v-else class="my-auto" :class="column.line2Line ? ` line-clamp-2 ` : ` truncate `">
                {{ formatValue(data[column.key] ?? '', column.format) }}
              </div>
              <!--            </CustomTooltip>-->

            </div>
          </template>
        </div>
      </div>
    </div>
  </div>

  <Dialog v-if="showSelectColumnsDialog" v-model:open="showSelectColumnsDialog">
    <DialogContent class="w-full max-w-full p-4 sm:max-w-lg sm:rounded-lg sm:p-6">
      <DialogHeader class="space-y-3">
        <DialogTitle>Choose Columns to Display</DialogTitle>
      </DialogHeader>
      <div class="w-full">
        <div class="w-full py-2 flex gap-2">
          <Input v-model="searchColumnString" placeholder="Search Column" />
          <Button v-if="selectedTableColumns.length != tableColumns.length" variant="outline"
            @click="onSelectAllColumn()">
            <Icon class="size-6" icon="tabler:list-check" />
          </Button>
          <Button variant="outline" v-if="selectedTableColumns.length > 0" @click="selectedTableColumns = []">
            <Icon class="size-6" icon="tabler:playlist-x" />
          </Button>
        </div>
        <div class="grid grid-cols-2">
          <div class="text-sm font-medium">Select Columns</div>
          <div class="text-sm font-medium">Re-Order Columns</div>
          <div class="h-[calc(100dvh-200px)] overflow-y-auto w-full flex flex-col gap-2 items-start">
            <Label class="rounded-md px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-800 w-full"
              v-for="(item, index) in filteredColumns" :key="index" @click="onSelectColumn(item)">
              <Icon v-if="isColumnExists(item.key, selectedTableColumns)" icon="tabler:square-rounded-check-filled"
                class="size-6 text-primary" />
              <Icon v-if="!isColumnExists(item.key, selectedTableColumns)" icon="tabler:square-rounded"
                class="size-6" />
              <div class="grid gap-1">
                {{ item.title }}
                <span class="text-muted-foreground">{{ item.shortTitle }}</span>
              </div>
            </Label>
          </div>
          <div class="h-[calc(100dvh-200px)] overflow-y-auto w-full flex flex-col gap-2 items-start">
            <Label class="rounded-md px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-800 w-full"
              v-for="(item, index) in selectedTableColumns" :key="index">
              <Icon title="Move Up" @click="moveColumnUp(index)" icon="solar:square-alt-arrow-up-line-duotone"
                class="size-6 hover:text-primary" />
              <div class="grid gap-1 flex-grow text-center">
                {{ item.title }}
                <span class="text-muted-foreground">{{ item.shortTitle }}</span>
              </div>
              <Icon title="Move Down" @click="moveColumnDown(index)" icon="solar:square-alt-arrow-down-line-duotone"
                class="size-6 hover:text-primary" />
            </Label>
          </div>
        </div>

      </div>
    </DialogContent>
  </Dialog>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Scrollbar styling for modern horizontal scroll */
.custom-scrollbar {
  scrollbar-color: #a0aec0 transparent;
  /* thumb & track */
  scrollbar-width: thin;
}

.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  /* horizontal only */
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(100, 116, 139, 0.5);
  /* slate-500/50 */
  border-radius: 6px;
  border: 2px solid transparent;
  background-clip: content-box;
}
</style>
