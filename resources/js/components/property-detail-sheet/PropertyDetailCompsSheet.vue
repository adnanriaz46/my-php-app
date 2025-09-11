<script setup lang="ts">
import FullScreenDialog from "@/components/ui/full-screen-dialog/FullScreenDialog.vue";
import {computed, nextTick, onMounted, ref} from "vue";
import {
  getZillowPropertyData,
  ZillowPListingMain,
  ZillowPPriceRecord,
  ZillowPResoFacts,
  ZillowPTaxRecord
} from "@/lib/zilowAndlocationUtil";
import Toast from "@/components/ui/toast/Toast.vue";
import {Icon} from "@iconify/vue";
import {DBApiPropertyFull} from "@/types/DBApi";
import {getProperty} from "@/lib/DBApiUtil";
import {getClassByNumber} from "@/components/unlisted-property-sheet";
import {Button} from "@/components/ui/button";
import {Tooltip, TooltipContent, TooltipTrigger} from "@/components/ui/tooltip";
import Separator from "@/components/ui/separator/Separator.vue";
import {useScreen} from '@/composables/useScreen'
import {useDateFormat, useNumber, useTextFormat} from "@/composables/useFormat";
import PropertyStatusBadge from "@/components/ui/proprety-status-badge/PropertyStatusBadge.vue";
import PropertyDescription from "@/components/property-detail-sheet/PropertyDescription.vue";
import {getMainImage, PropertyStatus} from "@/types/property";
import {ImageSlider, FullScreenImageSlider} from "@/components/ui/image-slider";
import AddToFavoritePropertyDialog from "@/components/add-to-favorite-property-dialog/AddToFavoritePropertyDialog.vue";

import PropertyAskAiDialog from "@/components/property-dialogs/PropertyAskAiDialog.vue";

import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";


import {useClipboardCopy} from "@/composables/useCopy";
import {getUser, isCountySubscribedUser, isFullAccessUser} from "@/composables/useUser";
import {upgradeDialog} from "@/stores/DialogStore";
import {useIntersectionObserver} from '@vueuse/core'

const {formatNumber, formatPrice, formatPercent, formatPhone} = useNumber()
const {formatDate, formatDateTime} = useDateFormat()
const {formatToCapitalizeEachWord,} = useTextFormat()


const {isMobile} = useScreen()

const propertyId = ref<number>(null);
const loading = ref<boolean>(false);
const error = ref<string>('');
const zillowLoading = ref<boolean>(false);

const imageSliderOpen = ref<boolean>(false);
const isLocked = ref<boolean>(false);

const zillowData = ref<ZillowPListingMain>();
const propertyDetail = ref<DBApiPropertyFull>(null);
const mapRef = ref<HTMLElement | null>(null)

const selectedImageIndex = ref<number>(0);

const visibleImages = ref(new Set())

const props = defineProps<{
  _PropertyId: number;
  open: boolean;
}>();

onMounted(async () => {
  propertyId.value = props._PropertyId;
  await openSheet();
})

function observe(index, el) {
  useIntersectionObserver(
      el,
      ([{isIntersecting}]) => {
        if (isIntersecting) {
          visibleImages.value.add(index)
        }
      },
      {threshold: 0.1}
  )
}

const openSheet = async () => {
  loading.value = true;
  const data = await getProperty({id: propertyId.value});
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

  if (isLocked.value) return;

  const address = propertyDetail.value?.full_street_address + ", " + propertyDetail.value?.city_name + ', ' + propertyDetail.value?.state_or_province + " " + propertyDetail.value?.zip_code;
  zillowLoading.value = true;
  zillowData.value = await getZillowPropertyData(address)
  zillowLoading.value = false;

  await nextTick()
  mapInitiate(propertyDetail.value?.latitude, propertyDetail.value?.longitude);
}


const propAddress = computed(() => {
  return `${propertyDetail.value.full_street_address || ''}, ${propertyDetail.value.city_name || ''} ${propertyDetail.value.state_or_province || ''} ${propertyDetail.value.zip_code || ''}`.trim()
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

const emit = defineEmits(['update:open'])
const mapInitiate = (lat: number, lng: number) => {
  if (!window.google || !mapRef.value) {
    console.warn('Google Maps not ready or mapRef missing')
    return
  }

  const center = {lat, lng}

  const map = new google.maps.Map(mapRef.value, {
    center,
    zoom: 15,
  })

  new google.maps.Marker({
    position: center,
    map,
  })
}

const agentDetailDialog = ref<boolean>(false);
const addToFavoriteOpen = ref<boolean>(false);
const askAiOpen = ref<boolean>(false);
const previousChatPropertyID = ref<number | null>(null)

const openFullScreenImage = (index: number = 0) => {
  selectedImageIndex.value = index;
  imageSliderOpen.value = true;
}

const ifValuesYes = (value) => {
  return value && value.trim() == 'Y';
}
const selectionIds = ref<number[]>([]);

const onAddFavorite = () => {
  if (propertyId.value) {
    selectionIds.value = [Number(propertyId.value)];
    addToFavoriteOpen.value = true;
  }
}
const onAskAi = () => {
  askAiOpen.value = true;
}
const isOpen = computed({
  get: () => props.open,
  set: (val) => emit('update:open', val)
})

const {handleCopy, copied} = useClipboardCopy()

const openUpgrade = () => {
  upgradeDialog.user = getUser();
  upgradeDialog.upgradeDialogOpen = true;
}

</script>
<template>
  <FullScreenDialog v-if="isOpen"
                    title="Property Detail Dialog"
                    description="Property Detail Dialog"
                    v-model="isOpen"
                    aria-describedby="Property Detail title">


    <PropertyAskAiDialog v-if="askAiOpen && propertyDetail"
                         :listing-type="'Listed'"
                         :property-address="propAddress"
                         :property-id="propertyId"
                         :previous-id="previousChatPropertyID"
                         @update:previous-id="previousChatPropertyID = $event"
                         v-model:open="askAiOpen"/>


    <AddToFavoritePropertyDialog v-if="addToFavoriteOpen" :selectionIds="selectionIds"
                                 v-model:open="addToFavoriteOpen"/>
    <FullScreenImageSlider v-if="imageSliderOpen && propImages.length && propImages.length > 0"
                           v-model:open="imageSliderOpen"
                           :images="propImages" :index="selectedImageIndex"/>

    <div v-if="error && !loading" class="w-full h-[100dvh] animate-pulse flex flex-col justify-center">
      <div class="w-fit mx-auto text-center">
        <Icon icon="tabler:info-square-rounded" class="loaderIcon mx-auto text-red-600"/>
        {{ error }}
      </div>
    </div>

    <div v-if="loading" class="w-full h-[100dvh] animate-pulse flex flex-col justify-center">
      <div class="w-fit mx-auto">
        <Icon icon="tabler:loader-2" class="animate-spin loaderIcon mx-auto"/>
        Getting information...
      </div>
    </div>

    <div v-if="!loading && isLocked" class="w-full h-[100dvh]  flex flex-col justify-center">
      <Icon icon="solar:lock-keyhole-bold-duotone" class="loaderIcon mx-auto animate-pulse"/>
      <div class="w-fit mx-auto grid my-2 px-2 text-center">
        This property is locked because you haven't subscribed to the county it belongs to.
      </div>
      <Button class="w-fit mx-auto" variant="secondary" @click.stop="openUpgrade()">Upgrade</Button>
    </div>

    <div class="w-full flex text-base" v-if="!loading && propertyDetail && !isLocked">
      <!------------------- [LEFT CONTAINER] ------------------------------------------>
      <div class="flex-1 h-[calc(100dvh-15px)] relative overflow-auto left-container" v-if="!isMobile">
        <div class="w-full" v-if="propImages.length != 0">
          <img @click="openFullScreenImage(0)" class="w-full mt-[-2px] rounded-tl-lg"
               :src="propImages[0]"
               :alt="propAddress">
        </div>
        <div class="w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 mt-2 gap-2"
             v-if="propImages.length && propImages.length > 1">
          <div class="image-container"
               v-for="( img, index) in propImages" :ref="el => observe(index, el)"
               :key="img" @click="openFullScreenImage(index)">
            <div v-if="visibleImages.has(index)" class="bg-cover bg-center h-[200px] shadow-md border rounded-lg"
                 :style="{ backgroundImage: `url(${img})` }"></div>
          </div>
        </div>
      </div>
      <!------------------- [RIGHT CONTAINER] ------------------------------------------>
      <div class="flex-1 h-[calc(100dvh-15px)] relative right-container">
        <div
            class="w-full bg-card dark:bg-stone-900 pt-1 flex h-[70px] justify-between rounded-t-lg fixed-top-bar">
          <div class="grid text-md gap-1 pl-1 pt-2 pr-2 text-sm md:text-base">
            <div class="pl-2">
              <div class="prop-status">
                <PropertyStatusBadge :status="propertyDetail.status"/>
              </div>
              <div class="prop-price">
                <span class="font-semibold text-4xl" v-if="propertyDetail.status != PropertyStatus.Closed">{{
                    formatPrice(propertyDetail.list_price)
                  }}</span>
                <span class="font-semibold text-4xl" v-else>{{ formatPrice(propertyDetail.close_price) }}</span>
              </div>
            </div>
          </div>
          <div class="flex flex-grow pr-3 mb-auto mt-2 justify-end">
            <div class="text-sm md:text-base lg:text-lg font-semibold">
              Quick Detail
              <div class="text-muted-foreground text-xs">Comparable</div>
            </div>
          </div>
          <div class="flex gap-1 md:gap-2  pr-3 mb-auto mt-2">
            <Tooltip>
              <TooltipTrigger>
                <div class="relative" @click="onAskAi()">
                  <Icon class="pIcon my-auto" icon="solar:chat-round-bold"/>
                  <Icon icon="tabler:ai"
                        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-black"/>
                </div>
              </TooltipTrigger>
              <TooltipContent side="bottom" align="end">
                Ask AI About Property
              </TooltipContent>
            </Tooltip>
            <Tooltip>
              <TooltipTrigger>
                <Icon @click="onAddFavorite()" class="pIcon my-auto" icon="tabler:heart"/>
              </TooltipTrigger>
              <TooltipContent side="bottom" align="end">
                Add to favorite list
              </TooltipContent>
            </Tooltip>
            <Icon class="pIcon opacity-0 pr-10 md:pr-1" aria-describedby="Hidden Icon.."
                  icon="tabler:heart"/>
          </div>

        </div>
        <div
            class="w-full bg-card dark:bg-stone-900 border-b-1 pt-1 flex max-h-[55px] justify-start rounded-t-lg pb-2 pl-2">
          <Icon class="pIcon  my-auto" icon="solar:map-point-wave-bold"/>
          <div class="my-auto font-semibold cursor-pointer" @click="handleCopy(propAddress)">
            {{ copied ? 'Copied!' : propAddress }}
          </div>
        </div>
        <div class="overflow-auto h-[calc(100dvh-125px)] relative px-1.5">
          <div class="h-[1000px] justify-items-start md:px-2">
            <div class="w-full" v-if="propImages && isMobile">
              <ImageSlider :images="propImages" :autoPlay="true" :interval="5000"/>
            </div>
            <div class="flex gap-1 md:gap-2 pt-3 mb-3 text-sm lg:text-base">
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:bed-broken" class="size-5 text-primary-strong"/>
                {{ propertyDetail?.bedrooms_count }}
              </div>
              <Icon icon="tabler:minus-vertical" class="size-5 divider text-muted-foreground"/>
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:bath-linear" class="size-5 text-primary-strong"/>
                {{ propertyDetail?.bathrooms_total_count }}
              </div>
              <Icon icon="tabler:minus-vertical" class="size-5 divider text-muted-foreground"/>
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:maximize-square-minimalistic-broken"
                      class="size-5 text-primary-strong"/>
                {{ formatNumber(propertyDetail?.total_finished_sqft) }} sqft
              </div>
              <Icon icon="tabler:minus-vertical" class="size-5 divider text-muted-foreground"/>
              <div class="flex gap-2 font-medium">
                <Icon icon="solar:buildings-3-outline" class="size-5 text-primary-strong"/>
                {{ propertyDetail.structure_type }}
              </div>
            </div>
            <div class="w-full mb-3">
              <div ref="mapRef" class="w-full h-[250px] rounded-lg "></div>
            </div>
            <Separator/>
            <div class="tab-content w-full text-sm">
              <div class="tab-detail">
                <div class="font-semibold text-lg mt-5 mb-2">Overview</div>
                <div class="font-light">
                  <PropertyDescription :property-description="propertyDetail.remarks_public"/>
                </div>
                <div class="pt-4 text-base" v-if="propertyDetail.wholesale == 'Wholesale'">
                  <div class="font-semibold text-lg mt-5 mb-2"
                       v-if="propertyDetail.wholesale == 'Wholesale'">Wholesale
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
                      <Button variant="link" class="text-left p-0"
                              @click="agentDetailDialog = true">
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
                    <div class="font-medium">Last Updated<span
                        class="text-xs text-muted-foreground">(revamp)</span>
                    </div>
                    <div class="">{{ formatDateTime(propertyDetail.modification_timestamp) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Last Checked<span
                        class="text-xs text-muted-foreground">(system)</span>
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
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="propertyDetail.status == PropertyStatus.Closed">
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
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="propertyDetail.status == PropertyStatus.Closed">
                    <div class="font-medium">Closed Price</div>
                    <div class="">{{ formatPrice(propertyDetail.close_price) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="propertyDetail.status == PropertyStatus.Closed">
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
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium" v-if="ifValuesYes(propertyDetail.hoa_yn)">HOA</div>
                    <div class="">{{ propertyDetail.hoa_yn == 'Y' ? 'Yes' : 'No' }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="ifValuesYes(propertyDetail.hoa_yn)">
                    <div class="font-medium">HOA Amount</div>
                    <div class="">{{ formatPrice(propertyDetail.hoa_fee) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="ifValuesYes(propertyDetail.hoa_yn)">
                    <div class="font-medium">HOA Freq</div>
                    <div class="">{{ propertyDetail.hoa_fee_freq }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="ifValuesYes(propertyDetail.land_lease_yn)">
                    <div class="font-medium">Land Lease</div>
                    <div class="">{{ propertyDetail.land_lease_yn }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="ifValuesYes(propertyDetail.land_lease_yn)">
                    <div class="font-medium">Land Lease Amt</div>
                    <div class="">{{ formatPrice(propertyDetail.land_lease_amount) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="ifValuesYes(propertyDetail.land_lease_yn)">
                    <div class="font-medium">Land Lease Freq</div>
                    <div class="">{{ propertyDetail.land_lease_amt_freq }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="ifValuesYes(propertyDetail.ground_rent_exists_yn)">
                    <div class="font-medium">Ground Rent</div>
                    <div class="">{{ propertyDetail.ground_rent_exists_yn }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="ifValuesYes(propertyDetail.ground_rent_exists_yn)">
                    <div class="font-medium">Ground Rent Amt</div>
                    <div class="">{{ formatPrice(propertyDetail.ground_rent_amt) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="ifValuesYes(propertyDetail.ground_rent_exists_yn)">
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
                        propertyDetail.school_district_name ? formatToCapitalizeEachWord(propertyDetail.school_district_name) : 'N/A'
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
                        propertyDetail.middle_or_junio_school ? formatToCapitalizeEachWord(propertyDetail.middle_or_junio_school) : 'N/A'
                      }}
                    </div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Elementary School</div>
                    <div class="">{{
                        propertyDetail.elementary_school ? formatToCapitalizeEachWord(propertyDetail.elementary_school) : 'N/A'
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
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="propertyDetail.status == PropertyStatus.Closed">
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
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="propertyDetail.status == PropertyStatus.Closed">
                    <div class="font-medium">Closed Price</div>
                    <div class="">{{ formatPrice(propertyDetail.close_price) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2"
                       v-if="propertyDetail.status == PropertyStatus.Closed">
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
                      <Button variant="link" class="text-left text-secondary-foreground p-0"
                              @click="agentDetailDialog = true">
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
                               alt="Agent empty user image"/>
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
                    <div class="font-medium">Last Updated <span
                        class="text-xs text-muted-foreground">(Listing)</span>
                    </div>
                    <div class="">{{ formatDateTime(propertyDetail.mls_modification_at) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Last Updated <span
                        class="text-xs text-muted-foreground">(Revamp)</span>
                    </div>
                    <div class="">{{ formatDateTime(propertyDetail.modification_timestamp) }}</div>
                  </div>
                  <div class="grid grid-cols-2 border-b py-2">
                    <div class="font-medium">Last Checked <span
                        class="text-xs text-muted-foreground">(System)</span>
                    </div>
                    <div class="">{{ formatDateTime(new Date().toDateString()) }}</div>
                  </div>
                </div>
                <div class="pt-4">
                  <div class="font-semibold text-lg mt-5 mb-2">Price and Tax History</div>

                  <div class="font-semibold text-sm mt-4 mb-2">Price History</div>
                  <div class="w-full bg-input animate-pulse h-[150px]" v-if="zillowLoading"></div>
                  <div class="w-full bg-input animate-pulse h-[150px]" v-if="zillowLoading"></div>
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
                    <tr v-for="tax in taxHistory" :key="tax.time+'-'+tax.taxPaid">
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
            </div>
          </div>
        </div>
      </div>
    </div>

  </FullScreenDialog>
  <Toast/>
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

