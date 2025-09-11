<script setup lang="ts">
import FullScreenDialog from "@/components/ui/full-screen-dialog/FullScreenDialog.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { useScroll } from "@vueuse/core";
import {
  getZillowPropertyData,
  removeParamsFromUrl, ZillowPListingMain,
  ZillowPPriceRecord,
  ZillowPResoFacts,
  ZillowPTaxRecord
} from "@/lib/zilowAndlocationUtil";
import Toast from "@/components/ui/toast/Toast.vue";
import { Icon } from "@iconify/vue";
import { DBApiPropertyFull } from "@/types/DBApi";
import { getProperty } from "@/lib/DBApiUtil";
import { getClassByNumber } from "@/components/unlisted-property-sheet";
import { Button } from "@/components/ui/button";
import { PropertyComparable } from "@/components/property-comparables";
import { Tooltip, TooltipContent, TooltipTrigger } from "@/components/ui/tooltip";
import Separator from "@/components/ui/separator/Separator.vue";
import { useScreen } from '@/composables/useScreen'
import { useDateFormat, useNumber, useTextFormat } from "@/composables/useFormat";
import PropertyStatusBadge from "@/components/ui/proprety-status-badge/PropertyStatusBadge.vue";
import PropertyDescription from "@/components/property-detail-sheet/PropertyDescription.vue";
import { getMainImage, PropertyStatus } from "@/types/property";
import { ImageSlider, FullScreenImageSlider } from "@/components/ui/image-slider";
import { PropertyRentalCalculator, PropertyFlipCalculator } from "@/components/property-calculator";
import AddToFavoritePropertyDialog from "@/components/add-to-favorite-property-dialog/AddToFavoritePropertyDialog.vue";
import { useToast } from "@/composables/useToast";
import { RequestShowingDialog, AskAQuestionDialog } from "@/components/property-dialogs";
import SharePropertyDialog from "@/components/property-dialogs/SharePropertyDialog.vue";
import PropertyAskAiDialog from "@/components/property-dialogs/PropertyAskAiDialog.vue";
import Label from "@/components/ui/label/Label.vue";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogHeader,
  DialogFooter,
  DialogDescription,
  DialogTitle,
} from "@/components/ui/dialog";


import { SwitchRoot, SwitchThumb, VisuallyHidden } from "reka-ui";
import { PropertyRecentViewParamsType, requestWholesaleAddress, updateRecentViewProperty } from "@/lib/propertyUtil";
import { useClipboardCopy } from "@/composables/useCopy";
import { getUser, isCountySubscribedUser, isFullAccessUser } from "@/composables/useUser";
import { upgradeDialog } from "@/stores/DialogStore";
import { initUnlistedPropertyMap } from "@/pages/property-search/subs/mapHelper";

import InputError from "@/components/InputError.vue";
import axios from "axios";
import { GetInAppData, User } from "@/types";
import { returnResponseInApp } from "@/lib/utils";
import PropertyDetailSubImagesThumbs from "@/components/property-detail-sheet/PropertyDetailSubImagesThumbs.vue";
import BuyerFinancingDialog from "@/components/property-dialogs/BuyerFinancingDialog.vue";

const { formatNumber, formatPrice, formatPercent, formatPhone } = useNumber()
const { formatDate, formatDateTime } = useDateFormat()
const { formatToCapitalizeEachWord, } = useTextFormat()


const { isMobile } = useScreen()
const page = usePage()

const user: User = page.props.auth?.user;

const propertyId = ref<number>(null);
const show = ref<boolean>(false);
const loading = ref<boolean>(false);
const error = ref<string>('');
const zillowLoading = ref<boolean>(false);
// const zillowError = ref<string>('');
const pageInitiated = ref<boolean>(false);
const imageSliderOpen = ref<boolean>(false);
const isLocked = ref<boolean>(false);

const zillowData = ref<ZillowPListingMain>();
const propertyDetail = ref<DBApiPropertyFull>(null);
const mapRef = ref<HTMLElement | null>(null)
const selectedTab = ref<string>('detail');
const selectedImageIndex = ref<number>(0);

// Add scroll container reference for tracking scroll position
const scrollContainer = ref<HTMLElement | null>(null)
const { y: scrollPosition } = useScroll(scrollContainer)

const AddressRequestAllowed = ref<boolean>(false);
const AddressRequestAllowedLoading = ref<boolean>(true);

const { showToast } = useToast()


onMounted(() => {
  const propId = page.url.split('?')[1]?.split('&').find(param => param.startsWith('propertyid='));

  if (propId) {
    propertyId.value = decodeURIComponent(propId.split('=')[1])
    openSheet();

  }
  pageInitiated.value = true;
});


watch(() => show.value, (value) => {
  if (!value) {
    removeParamsFromUrl();
  }
})

watch(() => page.url,
  (newUrl) => {
    if (pageInitiated.value) {
      const searchParams = new URLSearchParams(new URL(window.location.origin + newUrl).search)
      const propId = searchParams.get('propertyid')
      if (propId) {
        propertyId.value = decodeURIComponent(propId);
        openSheet()
      }
    }
  },
  { immediate: true })


const openSheet = async () => {
  loading.value = true;
  show.value = true;
  const data = await getProperty({ id: propertyId.value });
  loading.value = false;

  if (data.error) {
    error.value = data.error;
    if (error.value == 'The request or response is invalid.') {
      error.value = 'Could not find a property with the given Property ID.';
    }
    return;
  } else if (data.data?.length == 0) {
    error.value = 'Could not find a property with the given Property ID.';
    return;
  }

  propertyDetail.value = data?.data[0] ?? null;
  const isFull = isFullAccessUser();
  isLocked.value = isFull
    ? false
    : !isCountySubscribedUser(propertyDetail.value.county + ', ' + propertyDetail.value.state_or_province);

  const recentViewParams: PropertyRecentViewParamsType = {
    id: propertyId.value,
    status: propertyDetail.value.status,
    street_address: propertyDetail.value.full_street_address,
    city: propertyDetail.value.city_name,
    state: propertyDetail.value.state_or_province,
    zip: propertyDetail.value.zip_code,
    geo_address: propertyDetail.value.geo_address,
    is_locked: isLocked.value,
  }

  updateRecentViewProperty(recentViewParams);

  if (isLocked.value) return;

  const address = propertyDetail.value?.full_street_address + ", " + propertyDetail.value?.city_name + ', ' + propertyDetail.value?.state_or_province + " " + propertyDetail.value?.zip_code;
  zillowLoading.value = true;
  zillowData.value = await getZillowPropertyData(address)
  zillowLoading.value = false;

  await nextTick()
  if (propertyDetail.value?.latitude && propertyDetail.value?.longitude) {
    await mapInitiate(propertyDetail.value.latitude, propertyDetail.value.longitude);
  }

  if (propertyDetail.value.wholesale == 'Wholesale') {
    await requestWholesaleDetail();
  }
  AddressRequestAllowedLoading.value = false;
}

const requestWholesaleDetail = async () => {
  const req = await axios.post(route('my_properties.is_address_allowed'), { property_id: propertyId.value });
  const addressRequest: GetInAppData<boolean> = returnResponseInApp(req.data);

  if (addressRequest.success) {
    AddressRequestAllowed.value = addressRequest.data;
    return;
  }
}

const propAddress = computed(() => {
  return `${propertyDetail.value.full_street_address || ''}, ${propertyDetail.value.city_name || ''} ${propertyDetail.value.state_or_province || ''} ${propertyDetail.value.zip_code || ''}`.trim()
})
const propAddressShort = computed(() => {
  return `${propertyDetail.value.city_name || ''} ${propertyDetail.value.state_or_province || ''} ${propertyDetail.value.zip_code || ''}`.trim()
})

const propImages = computed(() => {
  const string = propertyDetail.value.full_location;
  if (string) {
    const imageArray: string[] = propertyDetail.value.full_location
      ?.split(',')
      .map(part => part.trim())
      .filter(part => part !== '') ?? [];
    if (imageArray.length < 1) {
      return [getMainImage('')]
    } else {
      return imageArray;
    }
  }
  return [getMainImage('')];
})

const priceHistory: ZillowPPriceRecord[] = computed(() => {
  const value: ZillowPResoFacts = zillowData.value?.priceHistory
  return value;
})
const taxHistory: ZillowPTaxRecord[] = computed(() => {
  const value: ZillowPTaxRecord = zillowData.value?.taxHistory
  return value;
})

const pageUrl: string = window.location.origin + page.url;

const detailTabRef = ref<HTMLElement | null>(null)
const valueTabRef = ref<HTMLElement | null>(null)
const calculatorTabRef = ref<HTMLElement | null>(null)

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

const mapInitiate = async (lat: number, lng: number) => {
  if (!mapRef.value) {
    console.warn('Map element not found')
    return
  }

  await initUnlistedPropertyMap(mapRef.value, lat, lng)
}

const agentDetailDialog = ref<boolean>(false);
const addToFavoriteOpen = ref<boolean>(false);
const requestAShowingOpen = ref<boolean>(false);
const askAQuestionOpen = ref<boolean>(false);
const askAiOpen = ref<boolean>(false);
const sharePropertyOpen = ref<boolean>(false);
const previousChatPropertyID = ref<number | null>(null)
const buyerFinancingOpen = ref<boolean>(false);

const openFullScreenImage = (index: number = 0) => {
  selectedImageIndex.value = index;
  imageSliderOpen.value = true;
}


const ifValuesYes = (value) => {
  return value && value.trim() == 'Y';
}
const selectionIds = ref<number[]>([]);

const onAddFavorite = () => {
  if (!user) {
    showToast('Login Required', 'You should logged in to proceed!', 'warning')
    return;
  }
  if (propertyId.value) {
    selectionIds.value = [Number(propertyId.value)];
    addToFavoriteOpen.value = true;
  }
}

const onAskAQuestion = () => {
  askAQuestionOpen.value = true;
}
const onRequestShowing = () => {
  requestAShowingOpen.value = true;
}
const onShareProperty = () => {
  sharePropertyOpen.value = true;
}
const onAskAi = () => {
  askAiOpen.value = true;
}
const onBuyerFinance = () => {
  buyerFinancingOpen.value = true;
}


// Wholesale address
const wholesaleAddressDialog = ref<boolean>(false);
const wholesaleUserAgreed = ref<boolean>(false);
const wholesaleRequestLoading = ref<boolean>(false);
const wholesaleAddressError = ref<string>('')

const submitAddressRequest = async () => {
  wholesaleAddressError.value = '';
  wholesaleRequestLoading.value = true;
  const data = await requestWholesaleAddress({ property_id: propertyId.value });
  wholesaleRequestLoading.value = false;

  if (data.error) {
    wholesaleAddressError.value = data.error ?? "Could not send a request please try again";
    return;
  }
  wholesaleAddressDialog.value = false;
  showToast('Address Request', data.success ?? "Your request has been sent!", 'success')
}

const { handleCopy, copied } = useClipboardCopy()

const openUpgrade = () => {
  upgradeDialog.user = getUser();
  upgradeDialog.upgradeDialogOpen = true;
}

const onWholesaleAddressShow = () => {
  if (!user) {
    showToast('Login Required', 'You should logged in to proceed!', 'warning')
    return;
  }
  wholesaleAddressDialog.value = true
}
</script>

<template>
  <FullScreenDialog v-if="show" title="Property Detail Dialog" description="Property Detail Dialog" v-model="show"
    aria-describedby="Property Detail title">

    <RequestShowingDialog v-if="requestAShowingOpen && propertyDetail" :propertyData="propertyDetail"
      v-model:open="requestAShowingOpen" />

    <AskAQuestionDialog v-if="askAQuestionOpen && propertyDetail" :propertyData="propertyDetail"
      v-model:open="askAQuestionOpen" />

    <PropertyAskAiDialog v-if="askAiOpen && propertyDetail" :listing-type="'Listed'" :property-address="propAddress"
      :property-id="propertyId" :previous-id="previousChatPropertyID"
      @update:previous-id="previousChatPropertyID = $event" v-model:open="askAiOpen" />

    <SharePropertyDialog v-if="propertyDetail && sharePropertyOpen" :property-address="propAddress"
      :listing-type="'Listed'" :property-id="Number(propertyId)" :property-image="propImages?.[0]"
      :property-url="pageUrl" v-model:open="sharePropertyOpen" />

    <AddToFavoritePropertyDialog v-if="addToFavoriteOpen" :selectionIds="selectionIds"
      v-model:open="addToFavoriteOpen" />
    <FullScreenImageSlider v-if="imageSliderOpen && propImages.length && propImages.length > 0"
      v-model:open="imageSliderOpen" :images="propImages" :index="selectedImageIndex" />

    <BuyerFinancingDialog v-if="buyerFinancingOpen && propertyDetail" :property-data="propertyDetail"
      v-model:open="buyerFinancingOpen" />

    <div v-if="error && !loading" class="w-full h-[100dvh] animate-pulse flex flex-col justify-center">
      <div class="w-fit mx-auto text-center">
        <Icon icon="tabler:info-square-rounded" class="loaderIcon mx-auto text-red-600" />
        {{ error }}
      </div>
    </div>

    <div v-if="loading" class="w-full h-[100dvh] animate-pulse flex flex-col justify-center">
      <div class="w-fit mx-auto">
        <Icon icon="tabler:loader-2" class="animate-spin loaderIcon mx-auto" />
        Getting information...
      </div>
    </div>

    <div v-if="!loading && isLocked" class="w-full h-[100dvh]  flex flex-col justify-center">
      <Icon icon="solar:lock-keyhole-bold-duotone" class="loaderIcon mx-auto animate-pulse" />
      <div class="w-fit mx-auto grid my-2 px-2 text-center">
        This property is locked because you haven't subscribed to the county it belongs to.
      </div>
      <Button class="w-fit mx-auto" variant="secondary" @click.stop="openUpgrade()">Upgrade</Button>
    </div>

    <div class="w-full flex text-base" v-if="!loading && propertyDetail && !isLocked">
      <!------------------- [LEFT CONTAINER] ------------------------------------------>

      <PropertyDetailSubImagesThumbs :prop-address="propAddress" v-if="!isMobile" :prop-images="propImages"
        :open-image="openFullScreenImage" />

      <!------------------- [RIGHT CONTAINER] ------------------------------------------>
      <div class="flex-1 h-[calc(100dvh-15px)] relative right-container">
        <div class="w-full bg-card dark:bg-stone-900 pt-1 flex h-[70px] justify-between rounded-t-lg fixed-top-bar">
          <div class="grid text-md gap-1 pl-1 pt-2 pr-2 text-sm md:text-base">
            <div class="pl-2">
              <div class="prop-status">
                <PropertyStatusBadge :status="propertyDetail.status" />
              </div>
              <div class="prop-price">
                <span class="font-semibold text-4xl" v-if="propertyDetail.status != PropertyStatus.Closed">{{
                  formatPrice(propertyDetail.list_price)
                  }}</span>
                <span class="font-semibold text-4xl" v-else>{{ formatPrice(propertyDetail.close_price) }}</span>
                <Button v-if="!isMobile && propertyDetail.status != PropertyStatus.Closed"
                  @click.stop="onBuyerFinance()" class="text-primary-strong" variant="ghost">Get Financing
                </Button>
              </div>
            </div>
          </div>
          <div class="flex gap-1 md:gap-2  pr-3 mb-auto mt-2">
            <Tooltip>
              <TooltipTrigger>
                <div class="relative cursor-pointer" @click="onAskAi()">
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
                <Icon @click="onShareProperty()" class="pIcon my-auto cursor-pointer" icon="tabler:share" />
              </TooltipTrigger>
              <TooltipContent side="bottom" align="end">
                Share your property
              </TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger>
                <Icon @click="onAddFavorite()" class="pIcon my-auto cursor-pointer" icon="tabler:heart" />
              </TooltipTrigger>
              <TooltipContent side="bottom" align="end">
                Add to favorite list
              </TooltipContent>
            </Tooltip>
            <Icon class="pIcon opacity-0 pr-10 md:pr-1" aria-describedby="Hidden Icon.." icon="tabler:heart" />
          </div>
        </div>
        <div
          class="w-full bg-card dark:bg-stone-900 border-b-1 pt-1 flex max-h-[55px] justify-start rounded-t-lg pb-2 pl-2">
          <Icon class="pIcon  my-auto" icon="solar:map-point-wave-bold" />
          <div class="my-auto font-semibold" v-if="propertyDetail?.wholesale == 'Wholesale'">
            <Button @click="onWholesaleAddressShow()" variant="link">Show
            </Button>&nbsp;{{ propAddressShort }}
          </div>
          <div class="my-auto font-semibold cursor-pointer" v-else @click="handleCopy(propAddress)">
            {{ copied ? 'Copied!' : propAddress }}
          </div>
        </div>
        <div ref="scrollContainer" class="overflow-auto h-[calc(100dvh-125px)] relative px-1.5">
          <div class="w-full sticky -top-[1px] bg-card dark:bg-stone-900 z-20 border-b border-border md:px-2"
            v-if="scrollPosition > 700">
            <div class="tab-bar grid grid-cols-3 ">
              <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                :class="selectedTab == 'detail' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                @click="onTabChanged('detail')">
                <Icon icon="solar:info-square-bold-duotone" class="size-5 mr-1" />
                Detail
              </div>
              <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                :class="selectedTab == 'value' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                @click="onTabChanged('value')">
                <Icon icon="solar:graph-new-up-bold-duotone" class="size-5 mr-1" />
                Value
              </div>
              <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                :class="selectedTab == 'calculator' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                @click="onTabChanged('calculator')">
                <Icon icon="solar:calculator-bold-duotone" class="size-5 mr-1" />
                Calculator
              </div>
            </div>
          </div>
          <div class="h-[1000px] justify-items-start md:px-2">
            <div class="w-full" v-if="propImages && isMobile">
              <ImageSlider :images="propImages" :autoPlay="true" :interval="5000" />
            </div>
            <div class="flex gap-1 md:gap-2 pt-3 mb-3 text-sm lg:text-base">
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:bed-broken" class="size-5 text-primary-strong" />
                {{ propertyDetail?.bedrooms_count }}
              </div>
              <Icon icon="tabler:minus-vertical" class="size-5 divider text-muted-foreground" />
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:bath-linear" class="size-5 text-primary-strong" />
                {{ propertyDetail?.bathrooms_total_count }}
              </div>
              <Icon icon="tabler:minus-vertical" class="size-5 divider text-muted-foreground" />
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:maximize-square-minimalistic-broken" class="size-5 text-primary-strong" />
                {{ formatNumber(propertyDetail?.total_finished_sqft) }} sqft
              </div>
              <Icon icon="tabler:minus-vertical" class="size-5 divider text-muted-foreground" />
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:buildings-3-outline" class="size-5 text-primary-strong" />
                {{ propertyDetail.structure_type }}
              </div>
            </div>
            <div class="w-full mb-3">
              <div ref="mapRef" class="w-full h-[250px] rounded-lg "></div>
            </div>

            <div class="w-full flex gap-2 justify-between my-2">
              <Button @click="onRequestShowing()" variant="default" class="flex-2">Request Showing</Button>
              <Button @click="onAskAQuestion()" variant="secondary" class="flex-1">Ask Question</Button>
            </div>
            <Separator />
            <div class="w-full sticky top-[0px] bg-card dark:bg-stone-900 z-10 border-b border-border">
              <div class="tab-bar grid grid-cols-3 ">
                <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                  :class="selectedTab == 'detail' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                  @click="onTabChanged('detail')">
                  <Icon icon="solar:info-square-bold-duotone" class="size-5 mr-1" />
                  Detail
                </div>
                <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                  :class="selectedTab == 'value' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                  @click="onTabChanged('value')">
                  <Icon icon="solar:graph-new-up-bold-duotone" class="size-5 mr-1" />
                  Value
                </div>
                <div class="text-center py-2 select-none flex justify-center cursor-pointer"
                  :class="selectedTab == 'calculator' ? `font-semibold text-primary-strong border-b-2 border-primary` : `text-gray-600 dark:text-gray-300`"
                  @click="onTabChanged('calculator')">
                  <Icon icon="solar:calculator-bold-duotone" class="size-5 mr-1" />
                  Calculator
                </div>
              </div>
            </div>
            <div class="tab-content w-full text-sm">
              <div class="tab-detail" ref="detailTabRef">
                <div class="w-full flex gap-2 my-4 font-semibold text-2xl">
                  <Icon icon="solar:info-square-bold-duotone" class="size-8 mr-1 text-primary-strong" />Property Detail
                </div>

                <div class="font-semibold text-lg mt-5 mb-2">Overview</div>
                <div class="font-light">
                  <PropertyDescription :property-description="propertyDetail.remarks_public" />
                </div>
                <div class="pt-4 text-sm" v-if="propertyDetail.wholesale == 'Wholesale'">
                  <div class="font-semibold text-lg mt-5 mb-2" v-if="propertyDetail.wholesale == 'Wholesale'">Wholesale
                    Details
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Wholesale Deal</div>
                    <div class="">Yes</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">List Price</div>
                    <div class="">{{ formatPrice(propertyDetail.list_price) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Status</div>
                    <div class="">{{ propertyDetail.status }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">List Date</div>
                    <div class="">{{ formatDate(propertyDetail.listing_entry_date) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">DOM</div>
                    <div class="">{{ propertyDetail.dom }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Listing Office</div>
                    <div class="">{{ propertyDetail.list_office_name }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Listing Agent</div>
                    <div class="">
                      <Button variant="link" class="text-left p-0" @click="agentDetailDialog = true">
                        {{ propertyDetail.list_agent_first_name }}
                        {{ propertyDetail.list_agent_last_name }}
                      </Button>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Seller AVM</div>
                    <div class="">{{ formatPrice(propertyDetail.seller_avm) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Seller ARV</div>
                    <div class="">{{ formatPrice(propertyDetail.seller_arv) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Seller Est Rental Rehab</div>
                    <div class="">{{ formatPrice(propertyDetail.seller_est_rental_rehab) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Seller Est Flip Rehab</div>
                    <div class="">{{ formatPrice(propertyDetail.seller_est_flip_rehab) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Seller Est Cash Flow</div>
                    <div class="">{{ formatPrice(propertyDetail.seller_est_cashflow) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Seller Est Flip Profit</div>
                    <div class="">{{ formatPrice(propertyDetail.seller_est_flip_profit) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Last Updated<span class="text-xs text-muted-foreground">(revamp)</span>
                    </div>
                    <div class="">{{ formatDateTime(propertyDetail.modification_timestamp) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Last Checked<span class="text-xs text-muted-foreground">(system)</span>
                    </div>
                    <div class="">{{ formatDateTime(new Date().toDateString()) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Revamp ID</div>
                    <div class="">{{ propertyDetail.id }}</div>
                  </div>

                </div>
                <div class="pt-4 text-sm">
                  <div class="font-semibold text-lg mt-5 mb-2">Feature Details</div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">APN</div>
                    <div class="">{{ propertyDetail.tax_id_number }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Type</div>
                    <div class="">{{ propertyDetail.structure_type }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="propertyDetail.status == PropertyStatus.Closed">
                    <div class="font-medium">Closed Date</div>
                    <div class="">{{ formatDate(propertyDetail.close_date) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Zoning</div>
                    <div class="">{{ propertyDetail.zoning }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">County</div>
                    <div class="">{{ propertyDetail.county }}, {{
                      propertyDetail.state_or_province
                      }}
                    </div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Finished Sqft</div>
                    <div class="">{{ formatNumber(propertyDetail.total_finished_sqft) }} sqft</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Beds</div>
                    <div class="">{{ propertyDetail.bedrooms_count }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Baths</div>
                    <div class="">{{ propertyDetail.bathrooms_total_count }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="propertyDetail.status == PropertyStatus.Closed">
                    <div class="font-medium">Closed Price</div>
                    <div class="">{{ formatPrice(propertyDetail.close_price) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="propertyDetail.status == PropertyStatus.Closed">
                    <div class="font-medium">Closed PPSF</div>
                    <div class="">{{ formatPrice(propertyDetail.price_per_sqft_closed) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Lot Size</div>
                    <div class="">{{ formatNumber(propertyDetail.lot_sqft) }} sqft</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Year Built</div>
                    <div class="">{{ propertyDetail.year_built }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Heating Type</div>
                    <div class="">{{ propertyDetail.heating_type }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Cooling Type</div>
                    <div class="">{{ propertyDetail.cooling_type }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Hot Water</div>
                    <div class="">{{ propertyDetail.hot_water ?? 'N/A' }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Water Source</div>
                    <div class="">{{ propertyDetail.water_source ?? 'N/A' }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Sewer Source</div>
                    <div class="">{{ propertyDetail.sewer_septic ?? 'N/A' }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.hoa_yn)">
                    <div class="font-medium">HOA</div>
                    <div class="">{{ propertyDetail.hoa_yn == 'Y' ? 'Yes' : 'No' }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.hoa_yn)">
                    <div class="font-medium">HOA Amount</div>
                    <div class="">{{ formatPrice(propertyDetail.hoa_fee) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.hoa_yn)">
                    <div class="font-medium">HOA Freq</div>
                    <div class="">{{ propertyDetail.hoa_fee_freq }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.land_lease_yn)">
                    <div class="font-medium">Land Lease</div>
                    <div class="">{{ propertyDetail.land_lease_yn }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.land_lease_yn)">
                    <div class="font-medium">Land Lease Amt</div>
                    <div class="">{{ formatPrice(propertyDetail.land_lease_amount) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.land_lease_yn)">
                    <div class="font-medium">Land Lease Freq</div>
                    <div class="">{{ propertyDetail.land_lease_amt_freq }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.ground_rent_exists_yn)">
                    <div class="font-medium">Ground Rent</div>
                    <div class="">{{ propertyDetail.ground_rent_exists_yn }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.ground_rent_exists_yn)">
                    <div class="font-medium">Ground Rent Amt</div>
                    <div class="">{{ formatPrice(propertyDetail.ground_rent_amt) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="ifValuesYes(propertyDetail.ground_rent_exists_yn)">
                    <div class="font-medium">Ground Rend Freq</div>
                    <div class="">{{ propertyDetail.ground_rent_pymnt_freq }}</div>
                  </div>
                </div>
                <div class="pt-4 text-sm">
                  <div class="font-semibold text-lg mt-5 mb-2">Tax Details</div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Annual Tax</div>
                    <div class="">{{ formatPrice(propertyDetail.tax_annual_amount) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Assessment<span class="text-xs text-muted-foreground">(total)</span>
                    </div>
                    <div class="">{{ formatPrice(propertyDetail.tax_assessed_value) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Delinquent</div>
                    <div class="">N/A</div>
                  </div>
                </div>
                <div class="pt-4 text-sm">
                  <div class="font-semibold text-lg mt-5 mb-2">Municipality & Schools</div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Municipality</div>
                    <div class="">
                      {{
                        propertyDetail.municipality ? formatToCapitalizeEachWord(propertyDetail.municipality) : 'N/A'
                      }}
                    </div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">School District</div>
                    <div class="">{{
                      propertyDetail.school_district_name ?
                        formatToCapitalizeEachWord(propertyDetail.school_district_name) : 'N/A'
                    }}
                    </div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">High School</div>
                    <div class="">
                      {{
                        propertyDetail.high_school ? formatToCapitalizeEachWord(propertyDetail.high_school) : 'N/A'
                      }}
                    </div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Middle School</div>
                    <div class="">{{
                      propertyDetail.middle_or_junio_school ?
                        formatToCapitalizeEachWord(propertyDetail.middle_or_junio_school) : 'N/A'
                    }}
                    </div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Elementary School</div>
                    <div class="">{{
                      propertyDetail.elementary_school ? formatToCapitalizeEachWord(propertyDetail.elementary_school) :
                        'N/A'
                    }}
                    </div>
                  </div>
                </div>
                <div class="pt-4 text-sm">
                  <div class="font-semibold text-lg mt-5 mb-2">Listing Details</div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Status</div>
                    <div class="">{{ propertyDetail.status }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">List Date</div>
                    <div class="">{{ formatDate(propertyDetail.listing_entry_date) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="propertyDetail.status == PropertyStatus.Closed">
                    <div class="font-medium">Close Date</div>
                    <div class="">{{ formatDate(propertyDetail.close_date) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Revamp ID</div>
                    <div class="">{{ propertyDetail.id }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">MLS #</div>
                    <div class="">{{ propertyDetail.mls_number }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Wholesale Deal</div>
                    <div class="">{{ propertyDetail.wholesale == 'Wholesale' ? 'No' : 'Yes' }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">List Price</div>
                    <div class="">{{ formatPrice(propertyDetail.list_price) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="propertyDetail.status == PropertyStatus.Closed">
                    <div class="font-medium">Closed Price</div>
                    <div class="">{{ formatPrice(propertyDetail.close_price) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2" v-if="propertyDetail.status == PropertyStatus.Closed">
                    <div class="font-medium">Closed PPSF</div>
                    <div class="">{{ formatPrice(propertyDetail.price_per_sqft_closed) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Listing Office</div>
                    <div class="">{{ propertyDetail.list_office_name }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Listing Agent</div>
                    <div class="">
                      <Button variant="link" class="text-left p-0" @click="agentDetailDialog = true">
                        {{ propertyDetail.list_agent_first_name }}
                        {{ propertyDetail.list_agent_last_name }}
                      </Button>
                    </div>
                    <Dialog v-if="agentDetailDialog" v-model:open="agentDetailDialog">
                      <DialogContent>
                        <DialogHeader class="space-y-3">
                          <DialogTitle class="text-center text-2xl">Agent Information</DialogTitle>
                        </DialogHeader>
                        <div class="w-full flex justify-center">
                          <img class="w-[80px] rounded-full"
                            src="https://revamp365-storage.s3.amazonaws.com/assets/user/684fadc2b0d03.jpg"
                            alt="Agent empty user image" />
                        </div>
                        <div class="w-full p-2 text-center">
                          <div class="font-medium text-lg">
                            {{ propertyDetail.list_agent_first_name }}
                            {{ propertyDetail.list_agent_last_name }}
                          </div>
                          <div class="text-muted-foreground text-sm">
                            {{ propertyDetail.list_agent_email }}
                          </div>
                          <div class="text-sm">
                            {{ formatPhone(propertyDetail.list_agent_cell) }}
                          </div>
                          <div class="mt-2">
                            {{ propertyDetail.list_office_name }}
                          </div>
                        </div>
                      </DialogContent>
                    </Dialog>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Last Updated <span class="text-xs text-muted-foreground">(Listing)</span>
                    </div>
                    <div class="">{{ formatDateTime(propertyDetail.mls_modification_at) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Last Updated <span class="text-xs text-muted-foreground">(Revamp)</span>
                    </div>
                    <div class="">{{ formatDateTime(propertyDetail.modification_timestamp) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Last Checked <span class="text-xs text-muted-foreground">(System)</span>
                    </div>
                    <div class="">{{ formatDateTime(new Date().toDateString()) }}</div>
                  </div>
                </div>
                <div class="pt-4">
                  <div class="font-semibold text-lg mt-5 mb-2">Price and Tax History</div>

                  <div class="font-semibold text-sm mt-4 mb-2">Price History</div>
                  <div class="w-full bg-input animate-pulse h-[150px] rounded-lg mb-4" v-if="zillowLoading"></div>
                  <div class="w-full bg-input animate-pulse h-[150px] rounded-lg mb-2" v-if="zillowLoading"></div>
                  <table class="w-full text-center table-fixed" v-if="priceHistory">
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

                  <div class="font-semibold text-sm mt-4 mb-2">Public tax history</div>
                  <table class="w-full text-center table-fixed" v-if="taxHistory">
                    <thead>
                      <tr>
                        <th>Year</th>
                        <th>Property Taxes</th>
                        <th>Tax Assessment</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="tax in taxHistory" :key="tax.time + '-' + tax.taxPaid">
                        <td class="py-1 border-b ">{{ formatDate(tax.time) }}</td>
                        <td class="py-1 border-b ">{{ formatPrice(tax.taxPaid) }}<span
                            :class="getClassByNumber(tax.taxIncreaseRate) + ' text-xs'">({{
                              formatPercent(tax.taxIncreaseRate)
                            }})</span>
                        </td>
                        <td class="py-1 border-b ">{{ formatPrice(tax.value) }}<span
                            :class="getClassByNumber(tax.valueIncreaseRate) + ' text-xs'">({{
                              formatPercent(tax.valueIncreaseRate)
                            }})</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="pt-4"></div>
              </div>
              <div class="tab-detail" ref="valueTabRef">
                <div class="border-b border-border border-8 -mx-2"></div>
                <div class="w-full flex gap-2 my-4 font-semibold text-2xl">
                  <Icon icon="solar:graph-new-up-bold-duotone" class="size-8 mr-1 text-primary-strong" />Valuation
                </div>
                <div class="pt-4 text-base">
                  <div class="font-semibold text-lg mt-5 mb-2">Auto Valuations</div>
                  <div class="grid grid-cols-2 text-xl font-semibold my-4">
                    <div class="grid grid-cols-1 text-center">
                      <Icon icon="solar:buildings-2-bold-duotone" class="mx-auto pIconLg text-primary-strong" />
                      <div class="text-base">AVM</div>

                      <span v-if="propertyDetail.est_avm">{{
                        formatPrice(propertyDetail.est_avm)
                        }}</span>
                      <span v-else>
                        N/A
                      </span>
                    </div>
                    <div class="grid grid-cols-1 text-center">
                      <Icon icon="solar:refresh-circle-bold-duotone" class="mx-auto pIconLg text-primary-strong" />
                      <div class="text-base">ARV</div>

                      <span v-if="propertyDetail.est_arv">{{
                        formatPrice(propertyDetail.est_arv)
                        }}</span>
                      <span v-else>
                        N/A
                      </span>

                    </div>
                    <div class="col-span-3 text-center grid grid-cols-1">
                      <Icon icon="solar:verified-check-bold-duotone" class="mx-auto pIconLg text-primary-strong" />
                      <div class="text-base">Accuracy of Valuations</div>

                      <span v-if="propertyDetail.accuracy_score_value">{{
                        formatPercent(propertyDetail.accuracy_score_value / 10)
                        }}</span>
                      <span v-else>
                        N/A
                      </span>
                    </div>
                  </div>
                  <div class="pt-2 text-base">
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">List Price</div>
                      <div class="">{{ formatPrice(propertyDetail.list_price) }}</div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">List PPSF</div>
                      <div class="">{{ formatPrice(propertyDetail.price_per_sqft) }} /per sqft
                      </div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Average PPSF</div>
                      <div class="">{{ formatPrice(propertyDetail.avg_p_sqft) }} /per sqft</div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">High PPSF</div>
                      <div class="">{{ formatPrice(propertyDetail.high_comps_ppsf) }} /per sqft
                      </div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Highest 3 Avg</div>
                      <div class="">{{ formatNumber(propertyDetail.high_comps_cluster) }} sqft
                      </div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Low PPSF</div>
                      <div class="">{{ formatPrice(propertyDetail.low_comps_ppsf) }} /per sqft
                      </div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Lowest 3 Avg</div>
                      <div class="">{{ formatNumber(propertyDetail.low_comps_cluster) }} sqft
                      </div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Avg Rent</div>
                      <div class="">{{ formatPrice(propertyDetail.medianrent) }} /per mon</div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Low Rent</div>
                      <div class="">{{ formatPrice(propertyDetail.close_price) }} /per mo</div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">High Rent</div>
                      <div class="">{{ formatPrice(propertyDetail.price_per_sqft_closed) }} /per
                        mo
                      </div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium"># Rent Comps</div>
                      <div class="">{{ formatNumber(propertyDetail.comparablerentals) }}</div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium"># Sales <span class="text-xs text-muted-foreground">(6 Months)</span>
                      </div>
                      <div class="">{{ formatNumber(propertyDetail.sales) }}</div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Est AVM</div>
                      <div class="">{{ formatPrice(propertyDetail.est_avm) }}</div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Est ARV</div>
                      <div class="">{{ formatPrice(propertyDetail.est_arv) }}</div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Accuracy Score <span class="text-xs text-muted-foreground">(Value)</span>
                      </div>
                      <div class="">{{
                        formatPercent(propertyDetail.accuracy_score_value / 10)
                        }}
                      </div>
                    </div>
                    <div class="grid grid-cols-2 border-b py-2">
                      <div class="font-medium">Accuracy Score <span
                          class="text-xs text-muted-foreground">(Rental)</span>
                      </div>
                      <div class="">{{
                        formatPercent(propertyDetail.accuracy_score_rent / 10)
                        }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="pt-4 text-base">
                  <div class="px-3 py-4 bg-primary/30 rounded-lg shadow-md flex">
                    <div class="w-[40px]">
                      <Icon class="size-6" icon="tabler:businessplan" />
                    </div>
                    <div class="flex-1">
                      <p>
                        Investment funding at your fingertips. Get connected in as little as 2
                        minutes with no impact to
                        your credit score.
                      </p>
                      <Button class="float-right" @click="onBuyerFinance()" variant="ghost">Start
                        Now
                      </Button>
                    </div>
                  </div>
                </div>
                <div class="pt-4 text-base w-full">
                  <div class="font-semibold text-lg mt-5 mb-2">Comparable Properties</div>
                  <div class="grid grid-cols-1">
                    <PropertyComparable :property-types="[propertyDetail.structure_type]"
                      :sqft-min="((propertyDetail.total_finished_sqft ?? 0) * 0.8).toFixed()"
                      :sqft-max="((propertyDetail.total_finished_sqft ?? 0) * 1.2).toFixed()"
                      :zip="propertyDetail.zip_code" :lat="propertyDetail.latitude" :lng="propertyDetail.longitude" />
                  </div>

                </div>

              </div>
              <div class="tab-detail" ref="calculatorTabRef" v-if="propertyDetail.status != PropertyStatus.Closed">
                <div class="border-b border-border border-8 -mx-2"></div>
                <div class="w-full flex gap-2 my-4 font-semibold text-2xl">
                  <Icon icon="solar:calculator-bold-duotone" class="size-8 mr-1 text-primary-strong" />Calculator
                </div>
                <div class="font-semibold text-lg mt-5 mb-2">Rental Calculator</div>
                <PropertyRentalCalculator :property-data="propertyDetail" v-if="propertyDetail" :instant-offer="true"
                  :request-a-showing="true"
                  :params="{ listPrice: propertyDetail.list_price, arv: propertyDetail.est_arv, avm: propertyDetail.est_avm, avgRent: propertyDetail.medianrent, sqft: propertyDetail.total_finished_sqft, taxAnnualAmt: propertyDetail.tax_annual_amount }"
                  :edit-allowed="true" />
                <div class="font-semibold text-lg mt-5 mb-2">Flip Calculator</div>
                <PropertyFlipCalculator v-if="propertyDetail"
                  :params="{ listPrice: propertyDetail.list_price, arv: propertyDetail.est_arv, avm: propertyDetail.est_avm, avgRent: propertyDetail.medianrent, sqft: propertyDetail.total_finished_sqft, taxAnnualAmt: propertyDetail.tax_annual_amount }"
                  :edit-allowed="true" />
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
    <Toast />
  </FullScreenDialog>
  <Dialog v-if="wholesaleAddressDialog" v-model:open="wholesaleAddressDialog">
    <DialogContent>
      <DialogHeader class="space-y-3">
        <DialogTitle> {{ AddressRequestAllowed ? `Address` : `Address Request` }}</DialogTitle>
        <VisuallyHidden>
          <DialogDescription>
            Address Request
          </DialogDescription>
        </VisuallyHidden>
      </DialogHeader>
      <div class="w-full h-full text-sm text-center flex justify-center items-center gap-2"
        v-if="AddressRequestAllowedLoading">
        <Icon icon="tabler:loader-2" class="animate-spin size-6" />
        Validating...
      </div>
      <div class="w-full" v-else>
        <div class="w-full text-sm" v-if="!AddressRequestAllowed">
          <p>
            I agree to conduct all inquiries into and discussions about this property solely through the
            Disposition
            Manager and shall not directly contact the seller.
          </p>
          <br />
          <p>
            Any unauthorized contact with the seller will be considered intentional interference with a
            Contract. I
            agree
            to pay damages of $75,000 to the contract holder and $25,000 to Revamp365 Inc if I attempt to
            circumvent or
            intentionally interfere with this Contract.
          </p>
          <br />
          <Label class="text-stone-700 dark:text-white text-sm leading-none pr-2 select-none" for="user-agree">
            I totally agree to the terms & conditions
            <SwitchRoot id="user-agree" v-model="wholesaleUserAgreed"
              class="w-[32px] h-[20px] shadow-sm flex data-[state=unchecked]:bg-stone-300 data-[state=checked]:bg-stone-800 dark:data-[state=unchecked]:bg-stone-800 dark:data-[state=checked]:bg-stone-700 border border-stone-300 data-[state=checked]:border-stone-700  dark:border-stone-700 rounded-full relative transition-[background] focus-within:outline-none focus-within:shadow-[0_0_0_1px] focus-within:border-stone-800 focus-within:shadow-stone-800">
              <SwitchThumb
                class="w-3.5 h-3.5 my-auto bg-white text-xs flex items-center justify-center shadow-xl rounded-full transition-transform translate-x-0.5 will-change-transform data-[state=checked]:translate-x-full" />
            </SwitchRoot>
          </Label>
        </div>
        <div class="w-full " v-else>
          <div class="flex gap-1 cursor-pointer" @click="handleCopy(propAddress)">
            <Icon class="size-6" icon="tabler:map-pin" />
            {{ copied ? 'Copied!' : propAddress }}
          </div>
        </div>
      </div>

      <DialogFooter class="gap-2">
        <InputError :message="wholesaleAddressError" />
        <DialogClose as-child>
          <Button variant="secondary"> Cancel</Button>
        </DialogClose>
        <Button v-if="!AddressRequestAllowed" @click="submitAddressRequest" variant="default"
          :disabled="!wholesaleUserAgreed || wholesaleRequestLoading">
          Submit Request
        </Button>
      </DialogFooter>
    </DialogContent>

  </Dialog>

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

.pIconLg {
  width: 45px;
  height: 45px;
  stroke-width: 1;
}
</style>
