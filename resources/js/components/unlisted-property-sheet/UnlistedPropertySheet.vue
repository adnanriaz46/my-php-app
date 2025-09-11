<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import {
  getZillowPropertyData,
  removeParamsFromUrl, ZillowPAddress,
  ZillowPListingMain,
  ZillowPPriceRecord,
  ZillowPResoFacts, ZillowPTaxRecord,
} from "@/lib/zilowAndlocationUtil";

import { useToast } from '@/composables/useToast'
import Toast from "@/components/ui/toast/Toast.vue";
import { FullScreenDialog } from "@/components/ui/full-screen-dialog";
import { Icon } from '@iconify/vue'
import Separator from "@/components/ui/separator/Separator.vue";

import { useNumber, useDateFormat, useTextFormat } from '@/composables/useFormat'
import { getClassByNumber, yesOrNo } from "@/components/unlisted-property-sheet/index";
// import { MbOwnershipData } from "@/types/mbOwnershipData";
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip'
import { DBApi, DBApiCalValData, DBApiMlsHistory, DBApiOwnershipData } from "@/types/DBApi";
import { getDBApiCalculatedValueData, getDBApiMLSHistory, getOwnershipData } from "@/lib/DBApiUtil";
import { PropertyComparable } from "@/components/property-comparables";

const { formatNumber, formatPrice, formatPercent } = useNumber()
const { formatDate } = useDateFormat()
const { formatToCapitalizeEachWord, } = useTextFormat()

const { showToast } = useToast()

const page = usePage()
const address = ref<string>('');
const show = ref<boolean>(false);
const loading = ref<boolean>(false);
const loadingOwnership = ref<boolean>(false);
const loadingCalculateData = ref<boolean>(false);
const loadingMlsHistory = ref<boolean>(true);
const pageInitiated = ref<boolean>(false);

const selectedTab = ref('tab2')

const zillowData = ref<ZillowPListingMain>();
const OwnershipData = ref<DBApi<DBApiOwnershipData[]>>();
const CalculatedValues = ref<DBApi<DBApiCalValData>>();
const MlsHistory = ref<DBApi<DBApiMlsHistory[]>>();
import { useScreen } from '@/composables/useScreen'
import PropertyRentalCalculator from "@/components/property-calculator/PropertyRentalCalculator.vue";
import PropertyFlipCalculator from "@/components/property-calculator/PropertyFlipCalculator.vue";
import SharePropertyDialog from "@/components/property-dialogs/SharePropertyDialog.vue";
import PropertyAskAiDialog from "@/components/property-dialogs/PropertyAskAiDialog.vue";
import { useClipboardCopy } from "@/composables/useCopy";
import AddToListUnlisted from "@/components/unlisted-property-sheet/AddToListUnlisted.vue";
import PropertyDescription from "@/components/property-detail-sheet/PropertyDescription.vue";
import { UnlistedRecentViewParamsType, updateRecentViewUnlisted } from "@/lib/propertyUtil";
import { initUnlistedPropertyMap } from "@/pages/property-search/subs/mapHelper";

const { isMobile } = useScreen()

onMounted(() => {
  const unlistedId = page.url.split('?')[1]?.split('&').find(param => param.startsWith('unlistedid='));
  if (unlistedId) {
    address.value = decodeURIComponent(unlistedId.split('=')[1])
    openSheet();
  }
  pageInitiated.value = true;
});

watch(() => show.value, (value) => {
  if (!value) {
    removeParamsFromUrl();
  }
})

watch(
  () => page.url,
  (newUrl) => {
    if (pageInitiated.value) {
      const searchParams = new URLSearchParams(new URL(window.location.origin + newUrl).search)
      const unlistedId = searchParams.get('unlistedid')
      if (unlistedId) {
        address.value = decodeURIComponent(unlistedId);
        openSheet()
      }
    }
  },
  { immediate: true } // Trigger immediately on load
)


const openSheet = async () => {
  loading.value = true;
  loadingOwnership.value = true;
  loadingCalculateData.value = true;
  loadingMlsHistory.value = true;

  show.value = true;
  zillowData.value = await getZillowPropertyData(address.value)
  loading.value = false;


  if (zillowData.value?.error) {
    showToast('Unlisted Property', zillowData.value?.error, 'error')
  } else if (zillowData.value?.streetAddress) {
    const address: ZillowPAddress = zillowData.value.address;


    CalculatedValues.value = await getDBApiCalculatedValueData(address?.zipcode, zillowData.value.homeType, zillowData.value.latitude, zillowData.value.longitude,
      zillowData.value.livingArea, zillowData.value.price, propAddress.value);
    loadingCalculateData.value = false;
    if (CalculatedValues.value?.error) {
      showToast('Calculated Data', CalculatedValues.value?.error, 'error');
    } else if (CalculatedValues.value?.message) {
      showToast('Calculated Data', CalculatedValues.value?.message, 'error');
    }
    const recentViewParams: UnlistedRecentViewParamsType = {
      zpid: zillowData.value?.zpid,
      address: address.streetAddress + ', ' + address.city + ', ' + address.state + ' ' + address.zipcode,
    }

    updateRecentViewUnlisted(recentViewParams);

    await nextTick()
    if (zillowData.value?.latitude && zillowData.value?.longitude) {
      await mapInitiate(zillowData.value.latitude, zillowData.value.longitude);
    }

    OwnershipData.value = await getOwnershipData({
      full_street_address: address?.streetAddress,
      city: address?.city,
      state: address?.state,
      zip: address?.zipcode,
      county: zillowData.value?.county || '',
    });

    loadingOwnership.value = false;

    if (OwnershipData.value?.error) {
      // showToast('Ownership Data', OwnershipData.value?.error, 'error');
    }

    MlsHistory.value = await getDBApiMLSHistory(propAddress.value);
    loadingMlsHistory.value = false;
    if (MlsHistory.value?.error) {
      // showToast('Mls History Data', OwnershipData.value?.error, 'error', 1000);
    }
  }
}

const mapRef = ref<HTMLElement | null>(null)

const mapInitiate = async (lat: number, lng: number) => {
  if (!mapRef.value) {
    console.warn('Map element not found')
    return
  }

  await initUnlistedPropertyMap(mapRef.value, lat, lng)
}


const propAddress = computed(() => {
  const addr: ZillowPAddress = zillowData.value?.address
  if (!addr) return ''
  return `${addr.streetAddress || ''}, ${addr.city || ''} ${addr.state || ''} ${addr.zipcode || ''}`.trim()
})

const resoFacts: ZillowPResoFacts = computed(() => {
  const value: ZillowPResoFacts = zillowData.value?.resoFacts
  return value;
})
const priceHistory: ZillowPPriceRecord[] = computed(() => {
  const value: ZillowPResoFacts = zillowData.value?.priceHistory
  return value;
})
const taxHistory: ZillowPTaxRecord[] = computed(() => {
  const value: ZillowPTaxRecord = zillowData.value?.taxHistory
  return value;
})

const scrollContainer = ref<HTMLElement | null>(null)

const detailTabRef = ref<HTMLElement | null>(null)
const valueTabRef = ref<HTMLElement | null>(null)
const calculatorTabRef = ref<HTMLElement | null>(null)
const mlsHistoryTabRef = ref<HTMLElement | null>(null)

const onTabChanged = (tab: string) => {
  let targetElement: HTMLElement | null = null;
  let offset = 50;
  if (tab == 'detail') {
    targetElement = detailTabRef.value;
    offset = 100;
  } else if (tab == 'value') {
    targetElement = valueTabRef.value;
  } else if (tab == 'calculator') {
    targetElement = calculatorTabRef.value;
  } else if (tab == 'mls-history') {
    targetElement = mlsHistoryTabRef.value;
  }

  if (targetElement && scrollContainer.value) {
    const containerRect = scrollContainer.value.getBoundingClientRect();
    const elementRect = targetElement.getBoundingClientRect();
    const scrollTop = scrollContainer.value.scrollTop;
    const elementTop = elementRect.top - containerRect.top + scrollTop;

    scrollContainer.value.scrollTo({
      top: elementTop - offset, // Subtract 50px to add offset
      behavior: 'smooth'
    });
  }

  selectedTab.value = tab;
}



const sharePropertyOpen = ref<boolean>(false);
const pageUrl: string = window.location.origin + page.url;
const addToMyListOpen = ref<boolean>(false);

const askAiOpen = ref<boolean>(false);
const previousChatPropertyID = ref<number | null>(null)



const { handleCopy, copied } = useClipboardCopy()

</script>

<template>
  <FullScreenDialog v-if="show" title="Unlisted Property Dialog" description="Unlisted Property Dialog" v-model="show"
    aria-describedby="Unlisted property title">
    <Toast />
    <SharePropertyDialog v-if="propAddress && sharePropertyOpen" :property-address="propAddress"
      :listing-type="'Unlisted'" :property-id="Number(zillowData?.zpid)" :property-image="zillowData?.imgSrc"
      :property-url="pageUrl" v-model:open="sharePropertyOpen" />

    <PropertyAskAiDialog v-if="askAiOpen && propAddress && CalculatedValues" :calculated-data="CalculatedValues?.data"
      :listing-type="'Unlisted'" :property-address="propAddress" :property-id="zillowData?.zpid"
      :previous-id="previousChatPropertyID" @update:previous-id="previousChatPropertyID = $event"
      v-model:open="askAiOpen" />

    <AddToListUnlisted v-if="zillowData && addToMyListOpen" :zillow-data="zillowData" v-model:open="addToMyListOpen" />

    <div v-if="loading" class="w-full h-[100dvh] animate-pulse flex flex-col justify-center">
      <div class="w-fit mx-auto">
        <Icon icon="tabler:loader-2" class="animate-spin loaderIcon mx-auto" />
        Getting information...
      </div>
    </div>
    <div v-if="!loading && !zillowData?.address" class="w-full h-[100dvh] animate-pulse flex flex-col justify-center">
      <div class="w-fit mx-auto">
        <Icon icon="tabler:info-square-rounded" class="loaderIcon mx-auto text-red-600" />
        Provided address did not has any information
      </div>
    </div>
    <div class="w-full flex text-base" v-if="!loading && zillowData?.address">
      <div class="flex-1 h-[calc(100dvh-15px)] relative overflow-auto left-container" v-if="!isMobile">
        <div class="w-full">
          <img class="w-full mt-[-2px] rounded-tl-lg" v-if="zillowData?.imgSrc" :src="zillowData?.imgSrc"
            :alt="propAddress">
        </div>
        <div class="w-full">
          <div ref="mapRef" class="w-full h-96"></div>
        </div>
      </div>
      <div class="flex-1 h-[calc(100dvh-15px)] relative right-container">
        <div
          class="w-full bg-card dark:bg-stone-900 border-b-1 pt-1 flex h-[50px] justify-between rounded-t-lg fixed-top-bar">
          <div class="flex text-md gap-1 pl-1 pr-2 text-sm md:text-base">
            <Icon class="pIcon  my-auto" icon="solar:map-point-wave-bold" />
            <div class="my-auto font-semibold max-w-[220px] md:max-w-[500px] truncate overflow-hidden whitespace-nowrap"
              @click="handleCopy(propAddress)">
              {{ copied ? 'Copied!' : propAddress }}
            </div>
          </div>
          <div class="flex gap-1 md:gap-2  pr-3">
            <Tooltip>
              <TooltipTrigger>
                <div class="relative cursor-pointer" @click="askAiOpen = true">
                  <Icon class="pIcon my-auto" icon="solar:chat-round-bold" />
                  <Icon icon="tabler:ai"
                    class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-black" />
                </div>
              </TooltipTrigger>
              <TooltipContent side="bottom" align="end">
                Ask AI About Property
              </TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger>
                <Icon @click="sharePropertyOpen = true" class="pIcon my-auto cursor-pointer" icon="tabler:share" />
              </TooltipTrigger>
              <TooltipContent side="bottom" align="end">
                Share your property
              </TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger>
                <Icon class="pIcon my-auto cursor-pointer" icon="tabler:heart" @click="addToMyListOpen = true" />
              </TooltipTrigger>
              <TooltipContent side="bottom" align="end">
                Add to favorite list
              </TooltipContent>
            </Tooltip>
            <Icon class="pIcon opacity-0 pr-10 md:pr-1" icon="tabler:heart" />
          </div>
          <!--          <Separator/>-->
        </div>
        <div class="overflow-auto h-[calc(100dvh-75px)] relative px-1.5" ref="scrollContainer">
          <div class="h-[1000px] justify-items-start md:px-2">
            <div class="image-and-map-container-mobile" v-if="isMobile">
              <div class="w-full">
                <img class="w-full rounded-t-lg" v-if="zillowData?.imgSrc" :src="zillowData?.imgSrc" :alt="propAddress">
              </div>
              <div class="w-full">
                <div ref="mapRef" class="w-full h-[200px] rounded-b-lg "></div>
              </div>
            </div>

            <div class="flex gap-2 pt-3 mb-3">
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:bed-broken" class="size-5 text-primary-strong" />
                {{ zillowData?.bedrooms }}
              </div>
              <Icon icon="tabler:minus-vertical" class="pIcon divider text-muted-foreground" />
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:bath-linear" class="size-5 text-primary-strong" />
                {{ zillowData?.bathrooms }}
              </div>
              <Icon icon="tabler:minus-vertical" class="pIcon divider text-muted-foreground" />
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:maximize-square-minimalistic-broken" class="size-5 text-primary-strong" />
                {{ formatNumber(zillowData?.livingArea) }} sqft
              </div>
            </div>

            <div class="grid off-market-container">
              <div class="flex gap-2 mt-2 font-medium text-sm">
                <Icon icon="tabler:circle" class="mt-1 text-muted-foreground" />
                <div>Off-Market</div>
              </div>
              <div class="grid grid-cols-1" v-if="loadingCalculateData">
                <Icon icon="tabler:loader-2" class="pIcon animate-spin" />
              </div>
              <div class="flex gap-3 text-sm " v-else>
                <span class="p-1" v-if="CalculatedValues.data?.average_data?.avm">Revamp Est. AVM: {{
                  formatPrice(CalculatedValues.data?.average_data?.avm)
                  }}</span>
                <Icon v-if="CalculatedValues.data?.average_data?.avm" icon="tabler:minus-vertical"
                  class="pIcon divider text-muted-foreground" />
                <span v-if="CalculatedValues.data?.average_data?.arv" class="p-1">Revamp Est. ARV: {{
                  formatPrice(CalculatedValues.data?.average_data?.arv)
                  }}</span>
              </div>

              <div class="flex gap-3 text-sm ">
                <span class="p-1">Zillow Est.: {{
                  formatPrice(zillowData?.zestimate)
                  }} <span :class="getClassByNumber(zillowData?.zestimateHighPercent)">({{
                    formatPercent(zillowData?.zestimateHighPercent / 100)
                    }})</span></span>
                <Icon icon="tabler:minus-vertical" class="pIcon divider text-muted-foreground" />
                <span class="p-1">Est. Rent: {{ formatPrice(zillowData?.rentZestimate) }}/mo</span>
              </div>
            </div>
            <Separator class="m-1 w-full" />

            <div class="right-tab-container w-full">
              <div class="w-full sticky top-[0px] bg-card dark:bg-stone-900 z-10 border-b border-border">
                <div class="tab-bar flex gap-2 justify-around">
                  <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                    :class="selectedTab == 'detail' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                    @click="onTabChanged('detail')">
                    <Icon icon="solar:info-square-bold-duotone" class="size-5 mr-1 hidden sm:inline" />
                    Detail
                  </div>
                  <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                    :class="selectedTab == 'value' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                    @click="onTabChanged('value')">
                    <Icon icon="solar:graph-new-up-bold-duotone" class="size-5 mr-1 hidden sm:inline" />
                    Value
                  </div>
                  <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                    :class="selectedTab == 'calculator' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                    @click="onTabChanged('calculator')">
                    <Icon icon="solar:calculator-bold-duotone" class="size-5 mr-1 hidden sm:inline" />
                    Calculator
                  </div>
                  <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                    :class="selectedTab == 'mls-history' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                    @click="onTabChanged('mls-history')">
                    <Icon icon="solar:calendar-bold-duotone" class="size-5 mr-1 hidden sm:inline" />
                    MLS History
                  </div>
                </div>
              </div>

              <div ref="detailTabRef">
                <div class="w-full flex gap-2 my-4 font-semibold text-2xl">
                  <Icon icon="solar:info-square-bold-duotone" class="size-8 mr-1 text-primary-strong" />Property Details
                </div>
                <div class="overview mb-3">
                  <div class="font-semibold mb-2">Overview</div>
                  <p class="text-sm mb-2"><i>Note: This property is not currently for sale or
                      for rent on Revamp365.
                      The
                      description and
                      property data below may’ve been provided by a third party, the homeowner
                      or public records.</i>
                  </p>
                  <p class="text-sm">
                    <PropertyDescription :property-description="zillowData?.description" />
                  </p>
                </div>
                <div class="fact-features mb-4">
                  <div class="font-semibold mb-3">Facts and features: &nbsp;</div>
                  <div class="grid grid-cols-2 text-sm gap-3">
                    <div class="flex">
                      <Icon icon="tabler:home-spark" class="pIconSm" />
                      <span class="font-medium">Type: &nbsp;</span>
                      <span class="font-normal">{{
                        zillowData?.propertyTypeDimension
                        }}</span>
                    </div>
                    <div class="flex">
                      <Icon icon="tabler:air-conditioning" class="pIconSm" />
                      <span class="font-medium">Cooling: &nbsp;</span>
                      <span class="font-normal">{{ yesOrNo(resoFacts.hasCooling) }}</span>
                    </div>
                    <div class="flex">
                      <Icon icon="tabler:calendar-stats" class="pIconSm" />
                      <span class="font-medium">Year Built: &nbsp;</span>
                      <span class="font-normal">{{ zillowData?.yearBuilt }}</span>
                    </div>
                    <div class="flex">
                      <Icon icon="tabler:temperature-plus" class="pIconSm" />
                      <span class="font-medium">Heating: &nbsp;</span>
                      <span class="font-normal">{{ yesOrNo(resoFacts.hasHeating) }}</span>
                    </div>
                    <div class="flex">
                      <Icon icon="tabler:parking-circle" class="pIconSm" />
                      <span class="font-medium">Parking: &nbsp;</span>
                      <span class="font-normal">{{
                        resoFacts.parkingFeatures?.[0]
                        }}</span>
                    </div>
                    <div class="flex">
                      <Icon icon="tabler:window-maximize" class="pIconSm" />
                      <span class="font-medium">Lot: &nbsp;</span>
                      <span class="font-normal">{{ resoFacts.lotSize }}</span>
                    </div>
                  </div>
                </div>

                <div class="price-history mb-4">
                  <div class="font-semibold mb-3">Price and Tax History</div>

                  <div class="font-semibold text-sm mb-3">Price History</div>
                  <table class="w-full table-fixed text-center text-sm">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Event</th>
                        <th>Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(his, index) in priceHistory" :key="index">
                        <td class="py-1 border-b ">{{ formatDate(his.date) }}</td>
                        <td class="py-1 border-b ">{{ his.event }}</td>
                        <td class="py-1 border-b ">{{ formatPrice(his.price) }}<span
                            :class="getClassByNumber(his.priceChangeRate) + ' text-xs'">({{
                              formatPercent(his.priceChangeRate)
                            }})</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <div class="font-semibold text-sm mb-3 mt-4">Public tax history</div>
                  <table class="w-full table-fixed text-center text-sm">
                    <thead>
                      <tr>
                        <th>Year</th>
                        <th>Property Taxes</th>
                        <th>Tax Assessment</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="tax in taxHistory" :key="tax.time + '-' + tax.taxPaid">
                        <td class="py-1 border-b">{{ formatDate(tax.time) }}</td>
                        <td class="py-1 border-b">{{ formatPrice(tax.taxPaid) }}<span
                            :class="getClassByNumber(tax.taxIncreaseRate) + ' text-xs'">({{
                              formatPercent(tax.taxIncreaseRate)
                            }})</span>
                        </td>
                        <td class="py-1 border-b">{{ formatPrice(tax.value) }}<span
                            :class="getClassByNumber(tax.valueIncreaseRate) + ' text-xs'">({{
                              formatPercent(tax.valueIncreaseRate)
                            }})</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="price-history mb-6">
                  <div class="font-semibold mb-2">Owner Information</div>
                  <Icon icon="tabler:loader-2" class="pIcon animate-spin" v-if="loadingOwnership" />
                  <div class="ownership-data" v-if="!loadingOwnership && OwnershipData?.data?.length > 0">
                    <div class="grid grid-cols-1 md:grid-cols-2 text-sm gap-2">
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Owner 1 Name</span>
                        <span class="pl-2 font-light">{{
                          formatToCapitalizeEachWord(OwnershipData?.data?.[0]?.OwnerNAME1FULL)
                          }}</span>
                      </div>
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Owner 2 Name</span>
                        <span class="pl-2 font-light">{{
                          formatToCapitalizeEachWord(OwnershipData?.data?.[0]?.OwnerNAME2FULL)
                          }}</span>
                      </div>
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Mailing Address</span>
                        <span class="pl-2 font-light">
                          {{
                            formatToCapitalizeEachWord(OwnershipData?.data?.[0]?.MailingFullStreetAddress)
                          }},
                          {{
                            formatToCapitalizeEachWord(OwnershipData?.data?.[0]?.MailingCity)
                          }},
                          {{ OwnershipData?.data?.[0]?.MailingState }}
                          {{ OwnershipData?.data?.[0]?.MailingZIP5 }}
                        </span>
                      </div>
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Do not mail</span>
                        <span class="pl-2 font-light">{{
                          (OwnershipData?.data?.[0]?.MailingFullStreetAddress != '') ? 'Yes' :
                            'No'
                        }}</span>
                      </div>
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Mail Name</span>
                        <span class="pl-2 font-light">
                          {{
                            formatToCapitalizeEachWord(OwnershipData?.data?.[0]?.OwnerNAME1FULL)
                          }}</span>
                      </div>
                    </div>

                  </div>
                  <div class="ownership-data text-center text-sm"
                    v-if="!loadingOwnership && OwnershipData?.data?.length == 0">
                    Currently no data found
                  </div>
                  <div class="ownership-data text-center text-sm" v-if="!loadingOwnership && OwnershipData?.error">
                    {{ OwnershipData?.error }}
                  </div>
                </div>

                <div class="price-history mb-6">
                  <div class="font-semibold mb-2">Mortgage Information</div>
                  <Icon icon="tabler:loader-2" class="pIcon animate-spin" v-if="loadingOwnership" />
                  <div class="ownership-data"
                    v-if="loadingOwnership == false && OwnershipData?.data && OwnershipData?.data?.length > 0">
                    <div class="grid grid-cols-1 md:grid-cols-2 text-sm gap-2">
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Open Mortgages</span>
                        <span class="pl-2 font-light">{{
                          OwnershipData?.data?.length
                          }}</span>
                      </div>
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Original Balance</span>
                        <span class="pl-2 font-light">{{ formatPrice(OwnershipData?.data?.[0]?.ConcurrentMtg1LoanAmt)
                        }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="ownership-data text-center text-sm"
                    v-if="!loadingOwnership && OwnershipData?.data && OwnershipData?.data?.length == 0">
                    Currently no data found
                  </div>
                </div>

                <!-- <div class="price-history mb-6">
                  <div class="font-semibold mb-2">Last Sale</div>
                  <Icon icon="tabler:loader-2" class="pIcon animate-spin" v-if="loadingOwnership" />
                  <div class="ownership-data" v-if="!loadingOwnership && OwnershipData?.DeedRecord">
                    <div class="grid grid-cols-1 md:grid-cols-2 text-sm gap-2">
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Public Sale</span>
                        <span class="pl-2 font-light">{{
                          formatPrice(OwnershipData?.DeedRecord?.[0]?.CityTransferTax?.Amt)
                        }}</span>
                      </div>
                      <div class="grid grid-cols-1">
                        <span class="font-medium">MLS Price</span>
                        <span class="pl-2 font-light">{{ formatPrice(zillowData?.price) }}</span>
                      </div>
                      <div class="grid grid-cols-1">
                        <span class="font-medium">Deed Type</span>
                        <span class="pl-2 font-light">{{
                          formatToCapitalizeEachWord(OwnershipData?.DeedRecord?.[0]?.RecordType)
                        }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="ownership-data text-center text-sm"
                    v-if="!loadingOwnership && !OwnershipData?.DeedRecord">
                    Currently no data found
                  </div>
                </div> -->
              </div>
              <div ref="valueTabRef">
                <div class="border-b border-border border-8 -mx-2"></div>
                <div class="w-full flex gap-2 my-4 font-semibold text-2xl">
                  <Icon icon="solar:graph-new-up-bold-duotone" class="size-8 mr-1 text-primary-strong" />Valuation
                </div>
                <div class="grid grid-cols-2 text-xl font-semibold py-8">
                  <div class="grid grid-cols-1 text-center">
                    <Icon icon="solar:buildings-2-bold-duotone" class="mx-auto pIconLg text-primary-strong" />
                    <div class="text-base">AVM</div>
                    <span v-if="!loadingCalculateData">
                      <span v-if="!loadingCalculateData && CalculatedValues.data?.average_data?.avm">{{
                        formatPrice(CalculatedValues?.data.average_data.avm)
                        }}</span>
                      <span v-else>
                        N/A
                      </span>
                    </span>
                    <span v-else class="mx-auto h-[30px] w-[80px] bg-input animate-pulse rounded-lg"></span>

                  </div>
                  <div class="grid grid-cols-1 text-center">
                    <Icon icon="solar:refresh-circle-bold-duotone" class="mx-auto pIconLg text-primary-strong" />
                    <div class="text-base">ARV</div>
                    <span v-if="!loadingCalculateData">
                      <span v-if="!loadingCalculateData && CalculatedValues.data?.average_data?.arv">{{
                        formatPrice(CalculatedValues?.data.average_data.arv)
                        }}</span>
                      <span v-else>
                        N/A
                      </span>
                    </span>
                    <span v-else class="mx-auto h-[30px] w-[80px] bg-input animate-pulse rounded-lg"></span>
                  </div>
                  <div class="col-span-3 text-center grid grid-cols-1">
                    <Icon icon="solar:verified-check-bold-duotone" class="mx-auto pIconLg text-primary-strong" />
                    <div class="text-base">Accuracy of Valuations</div>
                    <span v-if="!loadingCalculateData">
                      <span v-if="CalculatedValues.data?.average_data?.accuracy_score">{{
                        formatPercent(CalculatedValues?.data.average_data.accuracy_score / 10)
                        }}</span>
                      <span v-else>
                        N/A
                      </span>
                    </span>
                    <span v-else class="mx-auto h-[30px] w-[80px] bg-input animate-pulse rounded-lg"></span>
                  </div>
                </div>
                <div class="grid grid-cols-1 mt-3">
                  <div class="font-semibold">Averages of Calculated Comparable</div>
                  <table class="table-auto border-collapse text-sm" v-if="!loadingCalculateData">
                    <tbody>
                      <tr>
                        <td class="border-b border-gray-200 dark:border-gray-700 py-2">
                          Comparables
                        </td>
                        <td class="border-b border-gray-200 dark:border-gray-700 py-2">
                          {{
                            CalculatedValues?.data?.average_data?.sales > 10 ? 10 :
                              CalculatedValues?.data?.average_data?.sales
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td class="font-medium border-b border-gray-200 dark:border-gray-700 py-2">
                          Average PPSF
                        </td>
                        <td class="border-b border-gray-200 dark:border-gray-700 py-2">
                          {{
                            formatNumber(CalculatedValues?.data?.average_data?.price_per_sqft)
                          }}/per sqft
                        </td>
                      </tr>
                      <tr>
                        <td class="font-medium border-b border-gray-200 dark:border-gray-700 py-2">
                          Highest Cluster
                        </td>
                        <td class="border-b border-gray-200 dark:border-gray-700 py-2">
                          {{
                            formatPrice(CalculatedValues?.data?.average_data?.high_comps_cluster)
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td class="font-medium border-b border-gray-200 dark:border-gray-700 py-2">
                          Lowest Cluster
                        </td>
                        <td class="border-b border-gray-200 dark:border-gray-700 py-2">
                          {{
                            formatPrice(CalculatedValues?.data?.average_data?.low_comps_cluster)
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td class="font-medium border-b border-gray-200 dark:border-gray-700 py-2">
                          Highest price per sqft
                        </td>
                        <td class="border-b border-gray-200 dark:border-gray-700 py-2">
                          {{
                            formatPrice(CalculatedValues?.data?.average_data?.high_comp_ppsf)
                          }}/per sqft
                        </td>
                      </tr>
                      <tr>
                        <td class="font-medium border-b border-gray-200 dark:border-gray-700 py-2">
                          Lowest price per sqft
                        </td>
                        <td class="border-b border-gray-200 dark:border-gray-700 py-2">
                          {{
                            formatPrice(CalculatedValues?.data?.average_data?.low_comp_ppsf)
                          }}/per sqft
                        </td>
                      </tr>
                      <tr>
                        <td class="font-medium border-b border-gray-200 dark:border-gray-700 py-2">
                          Average sqft
                        </td>
                        <td class="border-b border-gray-200 dark:border-gray-700 py-2">
                          {{
                            formatNumber(CalculatedValues?.data?.average_data?.sqft, 0)
                          }} sqft
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="bg-input animate-pulse h-[200px] rounded-lg" v-else>

                  </div>
                </div>

                <div class="grid grid-cols-1 mt-8">
                  <div class="font-semibold mb-2">Comparable Properties</div>
                  <PropertyComparable v-if="!loadingCalculateData && CalculatedValues?.data?.average_data"
                    :property-types="CalculatedValues?.data?.structure_type"
                    :sqft-min="((zillowData?.livingArea ?? 0) * 0.8).toFixed()"
                    :sqft-max="((zillowData?.livingArea ?? 0) * 1.2).toFixed()" :zip="zillowData?.address?.zipcode"
                    :lat="zillowData?.latitude" :lng="zillowData?.longitude" />
                </div>
              </div>

              <div ref="calculatorTabRef" v-if="!loadingCalculateData" class="text-sm">
                <div class="border-b border-border border-8 -mx-2"></div>
                <div class="w-full flex gap-2 my-4 font-semibold text-2xl">
                  <Icon icon="solar:calculator-bold-duotone" class="size-8 mr-1 text-primary-strong" />Calculators
                </div>
                <div class="font-semibold">Rental Calculator</div>
                <PropertyRentalCalculator
                  :params="{ arv: CalculatedValues?.data?.average_data.arv, avm: CalculatedValues?.data.average_data.avm, avgRent: zillowData.rentZestimate, sqft: zillowData?.livingArea, taxAnnualAmt: zillowData.taxHistory?.[0]?.taxPaid, listPrice: zillowData.price }"
                  :property-data="null" :edit-allowed="true" :instant-offer="false" :request-a-showing="false" />
                <div class="font-semibold">Flip Calculator</div>
                <PropertyFlipCalculator
                  :params="{ arv: CalculatedValues?.data?.average_data.arv, avm: CalculatedValues?.data.average_data.avm, avgRent: zillowData.rentZestimate, sqft: zillowData?.livingArea, taxAnnualAmt: zillowData.taxHistory?.[0]?.taxPaid, listPrice: zillowData.price }"
                  :property-data="null" :edit-allowed="true" :instant-offer="false" :request-a-showing="false" />

              </div>
              <div ref="mlsHistoryTabRef">
                <div class="border-b border-border border-8 -mx-2"></div>
                <div class="w-full flex gap-2 my-4 font-semibold text-2xl">
                  <Icon icon="solar:calendar-bold-duotone" class="size-8 mr-1 text-primary-strong" />MLS History
                </div>
                <div class="w-full h-[200px] bg-input animate-pulse rounded-lg" v-if="loadingMlsHistory"></div>
                <div class="w-full" v-else>
                  <table class="table-auto w-full text-sm" v-if="MlsHistory?.data?.length">
                    <thead>
                      <tr>
                        <th class="text-left">MLS #</th>
                        <th class="text-left">Listed Price<span class="text-xs"> (Date)</span></th>
                        <th class="text-left">Closed Price<span class="text-xs"> (Date)</span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(his, index) in MlsHistory.data" :key="index">
                        <td class="border-b py-1">{{ his.mls_number }}
                          <div class="text-xs"> ({{ his.status }})</div>
                        </td>
                        <td class="border-b py-1">{{ formatPrice(his.list_price) }}
                          <div class="text-xs"> ({{
                            formatDate(his.listing_entry_date)
                            }})
                          </div>
                        </td>
                        <td class="border-b py-1">{{ formatPrice(his.close_price) }}
                          <div class="text-xs"> ({{ formatDate(his.close_date) }})</div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="w-full text-sm mx-auto" v-else>No data found</div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </FullScreenDialog>

</template>

<style scoped>
.loaderIcon {
  width: 45px;
  height: 45px;
}

.pIcon {
  width: 25px;
  height: 25px;
  color: #c8a02e;
  stroke-width: 1;
}

.divider {
  color: var(--secondary-foreground);
}

.pIconSm {
  width: 22px;
  height: 22px;
  margin: 0px 5px;
  color: #c8a02e;
  stroke-width: 1;
}

.pIconLg {
  width: 45px;
  height: 45px;
  stroke-width: 1;
}
</style>
