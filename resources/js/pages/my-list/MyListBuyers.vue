<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { fetchBuyerLists, deleteBuyerList } from '@/lib/buyerListApi';
import {Head} from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import {BreadcrumbItem} from "@/types";
import { Icon } from '@iconify/vue';

import PageHeading from "@/components/ui/page-heading/PageHeading.vue";
import Toast from "@/components/ui/toast/Toast.vue";
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
// import {useToast} from "@/composables/useToast";

// const {showToast} = useToast()

// const props = defineProps<{
//   list: MyPropertyList[],
//   user: User,
//   success?: string | null,
//   error?: string | null
// }>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My List - Buyers',
    href: route('my_list.buyers').toString(),
  },
];

const buyerLists = ref<{ name: string, buyers: any[] }[]>([]);
const selectedListName = ref('');
const selectedList = ref<any>(null);

onMounted(async () => {
  buyerLists.value = await fetchBuyerLists();
  // Don't auto-select the first list - let user choose
});

function onListChange() {
  selectedList.value = buyerLists.value.find(l => l.name === selectedListName.value);
}

async function deleteSelectedList() {
  if (!selectedList.value) return;
  if (!confirm(`Delete list "${selectedList.value.name}"? This cannot be undone.`)) return;

  try {
    // Call the API to delete the list from the backend
    await deleteBuyerList(selectedList.value.name);

    // Remove from local state
    buyerLists.value = buyerLists.value.filter(l => l.name !== selectedList.value.name);

    // Update selection
    if (buyerLists.value.length > 0) {
      selectedListName.value = buyerLists.value[0].name;
      selectedList.value = buyerLists.value[0];
    } else {
      selectedListName.value = '';
      selectedList.value = null;
    }
  } catch (error) {
    console.error('Failed to delete list:', error);
    alert('Failed to delete the list. Please try again.');
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="My List Buyers"/>
    <PageHeading title="My List - Buyers"/>
    <div v-if="buyerLists.length === 0" class="p-8 text-gray-400 text-center">
      No buyer lists saved yet.
    </div>
    <div v-else class="p-8">
      <!-- Dropdown for selecting a list -->
      <div class="mb-6 flex items-center justify-center gap-2">
        <div class="flex-1 w-full min-w-0">
          <label class="block mb-1 font-semibold dark:text-gray-200">Select List</label>
                     <SelectBox 
             v-model="selectedListName" 
             :options="buyerLists.map(list => ({ key: list.name, value: list.name + ' (' + list.buyers.length + ')' }))" 
             placeholder="Select a list" 
             class="w-full min-w-0"
             @update:model-value="onListChange"
           />
        </div>
        <!-- Delete button outside the dropdown -->
        <button
          v-if="selectedList"
          @click="deleteSelectedList"
          class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
          title="Delete this list"
        >
          <Icon icon="tabler:trash" class="w-5 h-5" />
        </button>
      </div>

      <!-- Show buyers for the selected list -->
      <div v-if="selectedListName && selectedList && selectedList.buyers.length">
        <Card v-for="buyer in selectedList.buyers" :key="buyer.id" class="mb-4">
          <CardHeader class="pb-3">
            <CardTitle class="text-lg font-bold truncate">{{ buyer.mrp_fullstreet }}</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-2">
              <div class="text-sm text-gray-500 truncate">
                {{ buyer.mrp_city }}, {{ buyer.mrp_state }} {{ buyer.mrp_zip }}
              </div>
              <div class="flex items-center gap-2">
                <Icon icon="tabler:currency-dollar" class="text-gray-400 w-4 h-4" />
                <span class="text-sm text-gray-700">
                  Paid {{ buyer.mrp_sales_price }}
                </span>
              </div>
              <div class="flex items-center gap-4 text-sm text-gray-600 flex-wrap">
                <div class="flex items-center gap-1">
                  <Icon icon="tabler:home" class="w-4 h-4" />
                  <span>{{ buyer.mrp_beds }} bd</span>
                </div>
                <div class="flex items-center gap-1">
                  <Icon icon="tabler:bath" class="w-4 h-4" />
                  <span>{{ buyer.mrp_bath }} ba</span>
                </div>
                <div class="flex items-center gap-1">
                  <Icon icon="tabler:ruler-measure" class="w-4 h-4" />
                  <span>{{ buyer.mrp_sqft }} sqft</span>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
      <div v-else-if="selectedListName && selectedList && selectedList.buyers.length === 0" class="text-gray-400 text-center mt-8">
        No buyers in this list.
      </div>
      <div v-else class="text-gray-400 text-center mt-8">
        Please select a list to view buyers.
      </div>
    </div>
    <Toast/>
  </AppLayout>
</template>

<style scoped>

</style>
