<script setup lang="ts">

import {Head, router} from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";

import {onMounted, onUpdated, ref, computed} from "vue";
import {BreadcrumbItem} from "@/types";

import {getPropertyList} from "@/lib/DBApiUtil";
import {DBApi, DBApiPropertyList} from "@/types/DBApi";
import PropertyDetailSheet from "@/components/property-detail-sheet/PropertyDetailSheet.vue";
import PageHeading from "@/components/ui/page-heading/PageHeading.vue";
import PropertyThumb from "@/components/property-thumb/PropertyThumb.vue";
import PropertyThumbLoading from "@/components/property-thumb/PropertyThumbLoading.vue";
import {Icon} from '@iconify/vue';
import ConfirmDialog from "@/components/ui/confirm-dialog/ConfirmDialog.vue";
import {useConfirmDialog} from "@/composables/useConfirmDialog";
import axios from "axios";
import Toast from "@/components/ui/toast/Toast.vue";
import {useToast} from "@/composables/useToast";

// import {addPropertyIdToUrl} from "@/lib/zilowAndlocationUtil";
import {useGridItems} from "@/composables/useScrnBreakpoints";
import {useVirtualList} from "@vueuse/core";
import {PropertyViewType, PropertyViewTypes} from "@/types/property";

const {showToast} = useToast()

const props = defineProps<{
  idList: number[],
  success?: string | null,
  error?: string | null
}>();

const selectedPropertyIds = ref<number[]>([]);
const propertiesLoading = ref<boolean>();
const properties = ref<DBApiPropertyList[]>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My List - Suppressed',
    href: route('my_list.suppressed').toString(),
  },
];

onMounted(() => {
  fetchMyListProperties();
})

onUpdated(() => {
  fetchMyListProperties();
})

const fetchMyListProperties = async () => {
  properties.value = [];
  selectedPropertyIds.value = [];
  if (props.idList.length > 0) {
    propertiesLoading.value = true;
    try {
      const listData: DBApi<DBApiPropertyList[]> = await getPropertyList({
        filter_ids: props.idList,
        ignore_default_user_suppress: 1
      });
      if (listData?.error) return;
      if (listData?.data) {
        properties.value = listData.data;
      }
    } catch (error) {
      console.error('Unexpected fetch error:', error);
    } finally {
      propertiesLoading.value = false;
    }
  }
};

const ViewType = ref<PropertyViewType>(PropertyViewTypes.Grid)

const {gridPropertyItems, gridPropertyItemHeight} = useGridItems(ViewType)

const virtualGridRows = computed(() => {
  const cols = gridPropertyItems.value;
  if (!properties.value?.length) return [];
  return Array.from(
      {length: Math.ceil(properties.value.length / cols)},
      (_, rowIndex) => {
        return properties.value.slice(
            rowIndex * cols,
            rowIndex * cols + cols
        )
      }
  )
})

const {list, containerProps, wrapperProps} = useVirtualList(
    computed(() => virtualGridRows.value),
    {
      itemHeight: gridPropertyItemHeight.value,
      overscan: 2,
    }
)

const confirmDialog = useConfirmDialog()
const onHandlingConfirmation = async (message: string) => {
  const confirmed = await confirmDialog.openConfirm(message, 'Delete')
  if (confirmed) {
    try {
      const response = await axios.post(route('my_list.suppressed.remove'), {property_ids: selectedPropertyIds.value})
      if (response.data?.success) {
        await router.reload()
        showToast('My List Suppressed', response.data.success, 'success');
      }
    } catch (err) {
      showToast('My List Suppressed', err?.message ?? err ?? 'Something went wrong!', 'error');
    } finally {

    }
  }
}

const onClickThumb = (id: number) => {
  // addPropertyIdToUrl(id)
  console.log('Click: ', id);
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="My Suppressed Properties"/>
    <PageHeading title="My List - Suppressed"/>
    <div class="px-4 py-2 flex-col items-start justify-start">

      <div class="mb-1 mt-4 font-semibold flex justify-between" v-if="!propertiesLoading">
        <div class="my-auto" v-if="properties?.length">
          {{ properties?.length }} Properties Found.
        </div>
        <div class="my-auto" v-else>
          You haven't add any properties yet.
        </div>
        <div v-if="selectedPropertyIds.length"
             class="pl-2 bg-primary text-black flex flex-shrink gap-1 items-center rounded-lg shadow-md">
          <span class="">{{ selectedPropertyIds.length }}</span>
          <Icon class="size-8 p-1 hover:bg-gray-600 rounded-lg" icon="tabler:trash-x"
                @click.stop="onHandlingConfirmation('Do you want to remove properties from your list?', 'update-property-ids')"/>
        </div>
      </div>
      <div v-else class="h-[30px] animate-pulse bg-input rounded-lg"></div>
    </div>

    <div class="grid gap-4 w-full"
         :class=" ` grid-cols-${gridPropertyItems}` " v-if="propertiesLoading">
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
      <PropertyThumbLoading/>
    </div>

    <div
        class=" h-[calc(100dvh-30px-160px)] md:h-[calc(100dvh-30px-150px)]" v-else>
      <div v-bind="containerProps" class="scroller w-full h-full overflow-auto bg-default">
        <div v-bind="wrapperProps">
          <div
              v-for="{ index, data: rowItems } in list"
              :key="index"
              class="grid gap-4"
              :class=" ` grid-cols-${gridPropertyItems}` "
              :style="{ height: `${gridPropertyItemHeight}px` }"
          >
            <div v-for="(item, i) in rowItems" :key="i"
                 class="mx-auto w-full px-2 py-2">
              <PropertyThumb class="border" @click="onClickThumb(item.id)" :property-list-data="item"
                             :show-selection="true"
                             v-model:selection-ids="selectedPropertyIds"/>

            </div>
          </div>
        </div>
      </div>
    </div>
    <ConfirmDialog :dialog="confirmDialog"/>
    <Toast/>
    <PropertyDetailSheet/>
  </AppLayout>
</template>

<style scoped>

</style>
