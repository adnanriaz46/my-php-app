<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Icon } from '@iconify/vue'
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import Button from '@/components/ui/button/Button.vue';
import { ref, watch, onMounted, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { getLimitedMailRanges } from '@/composables/useFilterOptions';
import AddressSearchInput from '@/components/ui/address-search/AddressSearchInput.vue';
import { GeoAddressObject } from '@/lib/zilowAndlocationUtil';
import { Head } from '@inertiajs/vue3';
import { searchBuyersWithSkipTrace, SearchBuyersParams } from '@/lib/DBApiUtil';
import { DBApi, DBApiBuyer } from '@/types/DBApi';
import BuyerListViewMapGridList from './BuyerListViewMapGridList.vue';
import SaveBuyerListModal from './SaveBuyerListModal.vue';
import BuyerDetailDialog from './BuyerDetailDialog.vue';
import { useNumber, useDateFormat } from '@/composables/useFormat';

const { formatPrice } = useNumber();
const { formatDate } = useDateFormat();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Buyers Search',
    href: '/buyers/search',
  },
];

const address = ref<GeoAddressObject | null>(null);
const addressString = ref('');
const mileRange = ref(1); // Changed from 2 to 1
const searchPeriod = ref(365); // Changed from 2 to 365 (1 year)
const showMobileFilters = ref(false);

watch(address, (newAddress) => {
  addressString.value = newAddress?.geoAddress || '';
}, { deep: true });

const mileRangeOptions = getLimitedMailRanges(15);
const searchPeriods = [
  { key: '365', value: '1 Year' },
  { key: '730', value: '2 Years' },
  { key: '1825', value: '5 Years' },
];
const buyers = ref<DBApiBuyer[]>([]);

const selectedBuyers = ref<DBApiBuyer[]>([]);
const selectedBuyer = ref<DBApiBuyer | null>(null);
const showBuyerDialog = ref(false);
// if selectedBuyers/buyers are refs, use .value in your file accordingly

function toggleBuyerSelection(buyer: DBApiBuyer) {
  const idx = selectedBuyers.value.findIndex(b => b.investor_identifier === buyer.investor_identifier);
  if (idx === -1) {
    selectedBuyers.value.push(buyer);
  } else {
    selectedBuyers.value.splice(idx, 1);
  }
}

function saveSelectedBuyers() {
  console.log('Saving buyers:', selectedBuyers.value);
  alert('Buyers to save: ' + selectedBuyers.value.map(b => b.mrp_fullstreet).join(', '));
}

function selectAllBuyers() {
  if (selectedBuyers.value.length !== buyers.value.length) {
    selectedBuyers.value = [...buyers.value];
  } else {
    selectedBuyers.value = [];
  }
}

function openBuyerDialog(buyer: DBApiBuyer) {
  selectedBuyer.value = buyer;
  showBuyerDialog.value = true;
}

function closeBuyerDialog() {
  showBuyerDialog.value = false;
}

const searchBuyersRequest = async () => {
  const params: SearchBuyersParams = {
    address: address.value?.geoAddress || '',
    zip: address?.value?.zip || '',
    lat: address?.value?.lat || 0,
    county: address?.value?.county || '',
    state: address?.value?.state || '',
    lng: address?.value?.lng || 0,
    range: mileRange.value,
    days: searchPeriod.value
  }
  const response: DBApi<DBApiBuyer[]> = await searchBuyersWithSkipTrace(params);
  if (response.error) {
    console.log(response.error);
  } else {
    buyers.value = response.data || [];
  }
}

const showSavePopup = ref(false);
function openSavePopup() {
  showSavePopup.value = true;
}
function closeSavePopup() {
  showSavePopup.value = false;
}

// Add this computed property after the existing refs
const isSearchDisabled = computed(() => {
  return !address.value?.geoAddress || address.value.geoAddress.trim() === '';
});

// Add this to trigger initial search when component mounts
onMounted(() => {
  // Trigger initial search with default values
  searchBuyersRequest();
});
</script>

<template>

  <Head title="Buyers Search" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Direct use of BuyerListViewMapGridList - no extra containers -->
    <BuyerListViewMapGridList :buyers="buyers">
      <!-- Search/filter section - FIXED at top -->
      <template #search-filters>
        <!-- Desktop - Always visible -->
        <div class="hidden md:block">
          <!-- Search Address -->
          <div class="mb-4">
            <label class="block mb-2 font-medium text-sm text-gray-700 dark:text-gray-300">Search Address</label>
            <div class="flex gap-3">
              <div class="relative flex-1">
                <AddressSearchInput v-model:address="address" />
              </div>
              <div class="flex items-end">
                <button @click="searchBuyersRequest" :disabled="isSearchDisabled" :class="[
                  'font-medium h-[42px] w-[42px] rounded-md flex items-center justify-center',
                  isSearchDisabled
                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    : 'bg-yellow-500 hover:bg-yellow-600 text-black'
                ]" type="button" aria-label="Search">
                  <Icon icon="tabler:search" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>

          <!-- Mile Range and Search Period -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 w-full">
            <div>
              <label class="block mb-2 font-medium text-sm text-gray-700 dark:text-gray-300">Mile range</label>
              <SelectBox v-model="mileRange" :options="mileRangeOptions" placeholder="Select Mile Range"
                class="w-full" />
            </div>
            <div>
              <label class="block mb-2 font-medium text-sm text-gray-700 dark:text-gray-300">Search Period</label>
              <SelectBox v-model="searchPeriod" :options="searchPeriods" placeholder="Select Search Period"
                class="w-full" />
            </div>
          </div>
        </div>

        <!-- Mobile Filter Icon (fixed, only on mobile) -->
        <div class="fixed bottom-6 right-6 z-50 md:hidden">
          <button @click="showMobileFilters = true"
            class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-gray-700 shadow-lg rounded-full p-3 flex items-center justify-center"
            aria-label="Show Filters">
            <Icon icon="tabler:filter" class="w-6 h-6 text-gray-700 dark:text-gray-200" />
          </button>
        </div>

        <!-- Mobile Filter Modal (only on mobile) -->
        <div v-if="showMobileFilters" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 md:hidden">
          <div class="bg-white dark:bg-neutral-800 rounded-xl p-6 w-[95vw] max-w-md mx-auto relative">
            <!-- Close button -->
            <button @click="showMobileFilters = false"
              class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-2xl"
              aria-label="Close">&times;</button>

            <!-- Filter Inputs -->
            <div class="space-y-4">
              <div>
                <label class="block mb-2 font-medium text-sm text-gray-700 dark:text-gray-300">Search Address</label>
                <AddressSearchInput v-model:address="address" />
              </div>
              <div>
                <label class="block mb-2 font-medium text-sm text-gray-700 dark:text-gray-300">Mile range</label>
                <SelectBox v-model="mileRange" :options="mileRangeOptions" placeholder="Select Mile Range"
                  class="w-full" />
              </div>
              <div>
                <label class="block mb-2 font-medium text-sm text-gray-700 dark:text-gray-300">Search Period</label>
                <SelectBox v-model="searchPeriod" :options="searchPeriods" placeholder="Select Search Period"
                  class="w-full" />
              </div>
              <button @click="() => { searchBuyersRequest(); showMobileFilters = false; }" :disabled="isSearchDisabled"
                :class="[
                  'w-full font-medium h-[42px] rounded-md flex items-center justify-center mt-2',
                  isSearchDisabled
                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    : 'bg-yellow-500 hover:bg-yellow-600 text-black'
                ]" type="button">
                <Icon icon="tabler:search" class="w-5 h-5 mr-2" />
                Search
              </button>
            </div>
          </div>
        </div>
        <div v-if="buyers.length > 0" class="mt-4 flex items-center justify-between">
          <span class="text-gray-700 dark:text-gray-200 font-semibold text-sm">
            {{ buyers.length }} records are being displayed
          </span>

          <div class="flex items-center gap-2">
            <!-- Save (disabled until something is selected) -->
            <button @click="openSavePopup" :disabled="selectedBuyers.length === 0" class="h-9 w-9 inline-flex items-center justify-center rounded-md
             bg-white dark:bg-neutral-800 border border-gray-200 dark:border-gray-700
             hover:bg-gray-100 dark:hover:bg-neutral-700
             disabled:opacity-50 disabled:cursor-not-allowed
             focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 dark:focus:ring-neutral-600"
              aria-label="Save selected buyers" title="Save selected buyers">
              <Icon icon="mdi:content-save-outline" class="h-5 w-5 text-gray-700 dark:text-gray-200" />
            </button>

            <!-- Select All (always visible) -->
            <button @click="selectAllBuyers" class="h-9 w-9 inline-flex items-center justify-center rounded-md
             bg-white dark:bg-neutral-800 border border-gray-2 00 dark:border-gray-700
             hover:bg-gray-100 dark:hover:bg-neutral-700
             focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 dark:focus:ring-neutral-600"
              aria-label="Select all buyers"
              :title="selectedBuyers.length === buyers.length && buyers.length > 0 ? 'Clear selection' : 'Select all buyers'">
              <Icon icon="mdi:checkbox-multiple-marked-outline" class="h-5 w-5 text-gray-700 dark:text-gray-200" />
            </button>

            <!-- Count chip -->
            <span v-if="selectedBuyers.length > 0" class="text-[11px] px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700
                 text-gray-700 dark:text-gray-200">
              {{ selectedBuyers.length }}
            </span>
          </div>
        </div>




        <div v-if="buyers.length === 0" class="text-center text-gray-400 dark:text-gray-500 mt-8">
          No buyers found for your search.
        </div>
      </template>

      <!-- Buyer Cards List - SCROLLABLE -->
      <template #buyer-list>
        <!-- Results List -->


        <div v-for="buyer in buyers" :key="buyer.investor_identifier"
          class="mb-4 p-3 md:p-4 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm bg-white dark:bg-neutral-800 cursor-pointer hover:shadow-md transition-shadow"
          @click="openBuyerDialog(buyer)">
          <!-- Desktop Layout -->
          <div class="hidden md:flex md:flex-row md:items-center">
            <!-- Left Column -->
            <div class="flex-1 min-w-0">
              <div class="font-bold text-lg dark:text-white mb-1 truncate">{{ buyer.mrp_fullstreet }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-300 mb-1 truncate">
                {{ buyer.mrp_city }}, {{ buyer.mrp_state }} {{ buyer.mrp_zip }}
              </div>
              <div class="flex items-center gap-2 mb-1">
                <Icon icon="tabler:currency-dollar" class="text-gray-400" />
                <span class="text-sm text-gray-700 dark:text-gray-200">
                  Paid {{ formatPrice(buyer.mrp_sales_price) }}
                  <span v-if="buyer.mrp_purchase">({{ formatDate(buyer.mrp_purchase) }})</span>
                </span>
              </div>
              <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-300 flex-wrap">
                <div class="flex items-center gap-1">
                  <Icon icon="tabler:home" />
                  <span>{{ buyer.mrp_beds }} bd</span>
                </div>
                <div class="flex items-center gap-1">
                  <Icon icon="tabler:bath" />
                  <span>{{ buyer.mrp_bath }} ba</span>
                </div>
                <div class="flex items-center gap-1">
                  <Icon icon="tabler:ruler-measure" />
                  <span>{{ buyer.mrp_sqft }} sqft</span>
                </div>
              </div>
              <div class="flex items-center gap-2 mt-4">
                <!-- Similar conditional color logic for the buyer list -->
                <span v-if="buyer.skip_traced" :class="[
                  'text-xs px-2 py-1 rounded',
                  buyer.skip_trace_has_contact_info
                    ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
                    : 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200'
                ]">
                  Skip Traced
                </span>
              </div>
            </div>
            <!-- Right Column -->
            <div class="flex flex-col items-end justify-between w-auto min-w-[140px] max-w-[180px] ml-4">
              <div class="flex flex-col items-end gap-1 w-full">
                <div
                  class="font-semibold text-xs text-gray-700 dark:text-gray-200 whitespace-nowrap truncate max-w-full">
                  {{ buyer.investor_identifier }}
                </div>
                <!-- Owner Information (only if skip traced) -->
                <div v-if="buyer.skip_traced && buyer.owner_name" class="mb-1">
                  <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                      {{ buyer.owner_name }}
                    </span>
                  </div>
                </div>

                <!-- Skip Traced Badge -->

                <span v-if="buyer.investor_identifier"
                  class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-[10px] px-2 py-0.5 rounded">
                  LLC
                </span>
                <span v-if="buyer.most_likely_buyer_tag"
                  class="bg-yellow-200 dark:bg-yellow-700 text-yellow-800 dark:text-yellow-100 text-xs px-2 py-1 rounded">
                  {{ buyer.most_likely_buyer_tag }}
                </span>
              </div>
              <input type="checkbox" class="accent-primary-500 mt-4"
                :checked="selectedBuyers.some(b => b.investor_identifier === buyer.investor_identifier)"
                @change.stop="toggleBuyerSelection(buyer)" @click.stop />
            </div>
          </div>

          <!-- Mobile Layout -->
          <div class="md:hidden">
            <!-- Address and Location -->
            <div class="mb-2">
              <div class="font-bold text-base dark:text-white mb-1">{{ buyer.mrp_fullstreet }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-300">
                {{ buyer.mrp_city }}, {{ buyer.mrp_state }} {{ buyer.mrp_zip }}
              </div>
            </div>

            <!-- Price Information -->
            <div class="flex items-center gap-2 mb-2">
              <Icon icon="tabler:currency-dollar" class="text-gray-400 w-4 h-4" />
              <span class="text-sm text-gray-700 dark:text-gray-200">
                Paid {{ formatPrice(buyer.mrp_sales_price) }}
                <span v-if="buyer.mrp_purchase" class="text-gray-500">({{ formatDate(buyer.mrp_purchase) }})</span>
              </span>
            </div>

            <!-- Property Details -->
            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300 mb-3">
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

            <!-- Bottom Section - Investor Info and Checkbox -->
            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
              <div class="flex flex-col gap-1">
                <div class="font-semibold text-xs text-gray-700 dark:text-gray-200">
                  {{ buyer.investor_identifier }} |
                  <span v-if="buyer.skip_traced && buyer.owner_name"
                    class="font-semibold text-xs text-gray-700 dark:text-gray-200">
                    {{ buyer.owner_name }}
                  </span>
                </div>

                <div class="flex gap-1">
                  <span v-if="buyer.investor_identifier"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-[10px] px-2 py-0.5 rounded">
                    LLC
                  </span>
                  <span v-if="buyer.most_likely_buyer_tag"
                    class="bg-yellow-200 dark:bg-yellow-700 text-yellow-800 dark:text-yellow-100 text-[10px] px-2 py-0.5 rounded">
                    {{ buyer.most_likely_buyer_tag }}
                  </span>
                  <!-- Similar conditional color logic for the buyer list -->
                  <span v-if="buyer.skip_traced" :class="[
                    'text-xs px-2 py-1 rounded',
                    buyer.skip_trace_has_contact_info
                      ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
                      : 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200'
                  ]">
                    Skip Traced
                  </span>
                </div>
              </div>
              <input type="checkbox" class="accent-primary-500 ml-2"
                :checked="selectedBuyers.some(b => b.investor_identifier === buyer.investor_identifier)"
                @change.stop="toggleBuyerSelection(buyer)" @click.stop />
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <Button v-if="selectedBuyers.length" @click="saveSelectedBuyers" class="mt-4 w-full">
          Save Selected Buyers
        </Button>
      </template>
    </BuyerListViewMapGridList>

    <SaveBuyerListModal v-if="showSavePopup" :buyers="selectedBuyers" @close="closeSavePopup" />
    <BuyerDetailDialog v-if="showBuyerDialog && selectedBuyer" :buyer="selectedBuyer" :visible="showBuyerDialog"
      @close="closeBuyerDialog" />
  </AppLayout>
</template>