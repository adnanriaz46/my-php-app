<script setup lang="ts">

import { SharedData, User } from '@/types';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import AddressSearchInput from '@/components/ui/address-search/AddressSearchInput.vue';
import { ref, watch } from 'vue';
import { addUnlistedIdToUrl, GeoAddressObject, getZillowPropertyData, ZillowPListingMain } from '@/lib/zilowAndlocationUtil';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { DBApi, DBApiAverageCompsProperty, DBApiCalValData, DBApiDatatreePropertyInfo, DBApiPropertyList, paramsDBApiGetProperty } from '@/types/DBApi';
import { Icon } from '@iconify/vue';
import { useNumber } from '@/composables/useFormat';
import UnlistedPropertySheet from '@/components/unlisted-property-sheet/UnlistedPropertySheet.vue';
import Label from '@/components/ui/label/Label.vue';
import Combobox from '@/components/ui/combobox/Combobox.vue';
import InputNumber from '@/components/ui/input-number/InputNumber.vue';
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import { getLimitedMailRanges, objectToStringArray, useFilterOptions } from '@/composables/useFilterOptions';
import { getPropertyTypeByZillow } from '@/types/property';
import { getDateBeforeDays } from '@/components/property-comparables';

import { useScreen } from '@/composables/useScreen';
import Button from '@/components/ui/button/Button.vue';
import { getAvgComps, GetAvgCompsParams, getDatatreePropertyInfo, getDBApiCalculatedValueData, getPropertyList } from '@/lib/DBApiUtil';
import LoaderAnimiatedTexts from './reusables/LoaderAnimiatedTexts.vue';

import TooltipProvider from '@/components/ui/tooltip/TooltipProvider.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import PropertyDetailCompsSheet from '@/components/property-detail-sheet/PropertyDetailCompsSheet.vue';
import PropertyListMapView from '@/components/property-list-map-view/PropertyListMapView.vue';
import ComboboxServer from '@/components/ui/combobox-server/ComboboxServer.vue';
import InstantCompsPropertyThumb from '@/components/property-thumb/InstantCompsPropertyThumb.vue';
import CustomTooltip from '@/components/ui/custom-tooltip/CustomTooltip.vue';
import { isInMarketCoverage, marketCoverageStates } from '@/lib/propertyUtil';
import CompsReportContainer from './reusables/CompsReportContainer.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { formatNumber } = useNumber()
// const { formatDate } = useDateFormat()
const { isMobile } = useScreen()

const selectedIds = ref<number[]>([]);
const address = ref<GeoAddressObject | null>(null);
const zillowData = ref<ZillowPListingMain>();
const CalculatedValues = ref<DBApi<DBApiCalValData>>();
// const MlsHistory = ref<DBApi<DBApiMlsHistory[]>>();
const loadingZillow = ref<boolean>(false);
const loadingCalculateData = ref<boolean>(false);


const initialFilterDone = ref<boolean>(false);
const compsAverageData = ref<DBApi<DBApiAverageCompsProperty>>();
const datatreePropertyInfo = ref<DBApiDatatreePropertyInfo>();
const errorAverage = ref<string>();
const loadingAverage = ref<boolean>(false);

const compsPropertyData = ref<DBApiPropertyList[]>();
const errorProperty = ref<string>();
const loadingProperty = ref<boolean>(false);
const loadingPropertyTexts = ref<string[]>([
    'Initializing property analysis...',
    'Preparing search parameters...',
    'Setting up comparison data...'
]);
const closedOnOptions = ref<{ key: string, value: string }[]>([
    { key: '90', value: '3 Months' },
    { key: '180', value: '6 Months' },
    { key: '365', value: '12 Months' },
    { key: '540', value: '18 Months' },
    { key: '730', value: '24 Months' },
    { key: '1095', value: '36 Months' }
])


const sortBy = ref<'closed_price' | 'closed_date' | 'distance' | 'relevance' | 'sqft'>('relevance');

const sortByOptions = ref<{ key: string, value: string }[]>([
    { key: 'relevance', value: 'Relevance' },
    { key: 'closed_price', value: 'Closed Price 🔽' },
    { key: 'closed_date', value: 'Closed Date 🔼' },
    { key: 'distance', value: 'Distance 🔼' },
    { key: 'sqft', value: 'Area Sqft 🔼' }
])

const step3Loading = ref<boolean>(false);

const viewType = ref<'thumb' | 'list' | 'map'>('thumb');

const propertyViewRef = ref<HTMLDivElement | null>(null);

const compsReportContainerRef = ref<HTMLDivElement | null>(null);

const detailLoadingContainerRef = ref<HTMLDivElement | null>(null);


const toggleViewType = (type: 'thumb' | 'list' | 'map') => {
    viewType.value = type;

}
watch(viewType, (newVal) => {
    if (newVal == 'map') {
        toggleFullScreen(true);
    } else {
        toggleFullScreen(false);
    }
})


const form = useForm(
    {
        propertyTypes: [] as string[],
        closedOn: 180,
        sqftMin: 0,
        sqftMax: 0,
        distanceMax: 1,
        zip: '',
        lat: 0,
        lng: 0,
        county: '',
        state: '',
        schoolDistrict: [],
        beds: '',
        baths: '',
        status: [] as string[],
        geo_address: '' as string
    }
)

const fetchDataZillow = async () => {

    selectedIds.value = [];
    compsPropertyData.value = [];
    compsAverageData.value = undefined;
    errorAverage.value = '';
    errorProperty.value = '';
    loadingAverage.value = false;
    loadingProperty.value = false;
    sortBy.value = 'relevance';
    pickforMeList.value = [];

    loadingZillow.value = true;
    zillowData.value = await getZillowPropertyData(address.value?.geoAddress || '')
    if (zillowData.value?.homeType) {
        form.propertyTypes = [...getPropertyTypeByZillow(zillowData.value?.homeType as string)];
    }
    form.closedOn = 180;
    form.sqftMin = Math.floor((zillowData.value?.livingArea ?? 0) * 0.8);
    form.sqftMax = Math.floor((zillowData.value?.livingArea ?? 0) * 1.2);
    form.distanceMax = 1;
    form.zip = zillowData.value?.address?.zipcode ?? '';
    form.lat = zillowData.value?.latitude ?? 0;
    form.lng = zillowData.value?.longitude ?? 0;
    form.geo_address = zillowData.value?.address?.streetAddress + ", " + zillowData.value?.address?.city + ", " + zillowData.value?.address?.state + " " + zillowData.value?.address?.zipcode;
    form.beds = '';
    form.baths = '';
    form.status = ['Closed'];
    form.county = (zillowData.value?.county ?? '').replace('County', '').trim();
    form.state = zillowData.value?.address?.state ?? '';
    form.schoolDistrict = [];

    loadingZillow.value = false;
}


const setParamsAverage = () => {
    const params: GetAvgCompsParams = {
        lat: form.lat,
        lng: form.lng,
        ids: selectedIds.value.map(id => id.toString())
    }

    return params;
}

const setParamsProperty = () => {
    const params: paramsDBApiGetProperty = {
        _limit: 50,
        total_finished_sqft_max: form.sqftMax,
        total_finished_sqft_min: form.sqftMin,
        status: form.status,
        structure_type: form.propertyTypes,
        closed_date_min: getDateBeforeDays(form.closedOn),
        county: [form.county],
        state_or_province_keyword: [form.state],
        school_district_name: form.schoolDistrict,

        comps_sub_prop_id: String(form.lat + "|" + form.lng),
        distance_max: form.distanceMax
    }

    if (form.beds) {
        params.bedrooms_min = Number(form.beds);
        params.bedrooms_max = Number(form.beds);
    }
    if (form.baths) {
        params.bathrooms_min = Number(form.baths);
        params.bathrooms_max = Number(form.baths);
    }

    return params;
}

const requestDataTreePropertyInfo = async () => {
    const propertyInfo: DBApi<DBApiDatatreePropertyInfo[]> = await getDatatreePropertyInfo({
        full_street_address: zillowData.value?.address?.streetAddress || '',
        city: zillowData.value?.address?.city || '',
        state: zillowData.value?.address?.state || '',
        zip: zillowData.value?.address?.zipcode || '',
        county: zillowData.value?.county || ''
    });
    if (propertyInfo.status && propertyInfo.data && propertyInfo.data.length > 0) {
        datatreePropertyInfo.value = propertyInfo.data[0];
    }
}
const isInMarketCoverageAddress = ref<boolean>(true);
watch(address, async () => {
    isInMarketCoverageAddress.value = true;
    zillowData.value = undefined;
    if (!isInMarketCoverage(address.value?.state || '', address.value?.county || '')) {
        isInMarketCoverageAddress.value = false;
        return;
    }

    if (address.value?.geoAddress) {
        await fetchDataZillow();
        await requestDataTreePropertyInfo();
    }
})
const filterOptionValues = useFilterOptions();
const propertyTypeList = ref<string[]>(Object.values(filterOptionValues.structureType));
const statusOptions = ref<string[]>(objectToStringArray(filterOptionValues.status));
const mileRanges = ref<{ key: string, value: string }[]>(getLimitedMailRanges(5))

const resetData = () => {
    initialFilterDone.value = true;
    selectedIds.value = [];
    compsPropertyData.value = [];
    compsAverageData.value = undefined;
    errorAverage.value = '';
    errorProperty.value = '';
    loadingAverage.value = false;
    loadingProperty.value = false;
    showCompsReoprt.value = false;
    viewType.value = 'thumb';
    openCompsDialog.value = false;
    openCompsReportDialog.value = false;
    selectedPropertyId.value = 0;
}

const onConfirmInformation = async () => {

    resetData();

    await requestAvgData();
    await requestCalculatedData();
    loadingAverage.value = false;


    await requestPropertyListData();
    await new Promise(resolve => setTimeout(resolve, 3000));

    onSortBy(sortBy.value);

    loadingProperty.value = false;


    step3Loading.value = true;
    await new Promise(resolve => setTimeout(resolve, 2000));
    step3Loading.value = false;

    setTimeout(() => {
        scrollToDetailLoadingContainer();
    }, 500)
}



const requestCalculatedData = async () => {
    loadingCalculateData.value = true;
    if (zillowData.value) {
        CalculatedValues.value = await getDBApiCalculatedValueData(
            zillowData.value?.address?.zipcode ?? '',
            zillowData.value?.homeType ?? '',
            String(zillowData.value?.latitude ?? ''),
            String(zillowData.value?.longitude ?? ''),
            Number(zillowData.value?.livingArea ?? 0),
            Number(zillowData.value?.price ?? 0),
            address.value?.geoAddress || ''
        );
    }
    loadingCalculateData.value = false;

    if (CalculatedValues.value?.error) {
        errorAverage.value = CalculatedValues.value?.error;
    }
}

const requestAvgData = async () => {
    errorAverage.value = '';
    loadingAverage.value = true;
    if (selectedIds.value.length == 0) {
        return;
    }

    compsAverageData.value = await getAvgComps(setParamsAverage());

    if (compsAverageData.value?.error) {
        errorAverage.value = compsAverageData.value.error;
    }
}

const requestPropertyListData = async () => {
    errorProperty.value = '';
    loadingProperty.value = true;
    compsPropertyData.value = [];
    selectedIds.value = [];

    // Set initial loading texts
    loadingPropertyTexts.value = [
        'Fetching property data...',
        'Searching for comparable properties...',
        'Filtering by criteria...',
        'Preparing analysis...'
    ];

    const requestData: DBApi<DBApiPropertyList[]> = await getPropertyList(setParamsProperty())

    if (requestData?.error) {
        errorProperty.value = requestData.error;
        loadingPropertyTexts.value = [
            'Error occurred while fetching data...',
            'Please try again...'
        ];
    } else {
        compsPropertyData.value = requestData.data;
        if (compsPropertyData.value && compsPropertyData.value.length > 0) {
            compsPropertyData.value = compsPropertyData.value.filter(p => p.mile_range_from_subject !== 0) // Removing subject property from the list
            loadingPropertyTexts.value = compsPropertyData.value
                ?.slice(0, 10)
                .map(item => `Analyzing ${item.address}`);

        } else {
            loadingPropertyTexts.value = [
                'No properties found matching criteria...',
                'Try adjusting your search parameters...'
            ];
        }
    }
}

const openUnlistedPropertyDetail = (address: string) => {
    addUnlistedIdToUrl(address);
    return true;
}


const selectAllProperties = () => {
    if (compsPropertyData.value) {
        selectedIds.value = compsPropertyData.value.map(property => property.id);
    }
}

const openCompsDialog = ref<boolean>(false);
const selectedPropertyId = ref<number>(0);
const openPropertyDetail = (id: number) => {
    selectedPropertyId.value = id;
    openCompsDialog.value = true;
}

const deselectAllProperties = () => {
    selectedIds.value = [];
}

const openCompsReportDialog = ref<boolean>(false);
const showCompsReoprt = ref<boolean>(false);
const getReport = () => {
    openCompsReportDialog.value = true;
    showCompsReoprt.value = true;
    viewType.value = 'thumb';

    setTimeout(() => {
        scrollToCompsReport();
    }, 500)
}


const scrollToProperty = (index: number | string) => {
    console.log(index);
}

const fullScreen = ref<boolean>(false);
const toggleFullScreen = (value: boolean = false) => {
    fullScreen.value = value;
}
const MoreFiltersOpen = ref<boolean>(false);

const removePropertyIdFromUrl = () => {
    const url = new URL(window.location.href);
    url.searchParams.delete('propertyid');
    // Use Inertia's router to update the URL so the watcher will trigger
    router.visit(url.pathname + url.search, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}

watch(
    () => page.url,
    (newUrl) => {
        console.log('newUrl', newUrl);
        if (initialFilterDone.value) {
            const searchParams = new URLSearchParams(new URL(window.location.origin + newUrl).search)
            const propertyId = searchParams.get('propertyid')
            if (propertyId) {
                openPropertyDetail(Number(propertyId));
            }
        }
    },
    { immediate: false } // Trigger immediately on load
)

watch(
    () => openCompsDialog.value,
    (newOpenCompsDialog: boolean) => {
        if (!newOpenCompsDialog) {
            removePropertyIdFromUrl();
        }
    }
)

const pickforMeList = ref<number[]>([]);

const onSortBy = (sortBy: 'closed_price' | 'closed_date' | 'distance' | 'relevance' | 'sqft') => {
    if (!compsPropertyData.value) return;

    const subjectProperty = {
        total_finished_sqft: zillowData.value?.livingArea ?? 0,
        bedrooms: zillowData.value?.bedrooms ?? 0,
        lot_size: Number((zillowData.value?.resoFacts?.lotSize || '0').replace(/[^0-9]/g, '')) ?? 0,
        price: zillowData.value?.price ?? 0,
        currentDate: new Date()
    };

    if (sortBy === 'closed_price') {
        compsPropertyData.value = [...compsPropertyData.value].sort((a, b) => b.close_price - a.close_price);
    } else if (sortBy === 'closed_date') {
        compsPropertyData.value = [...compsPropertyData.value].sort((a, b) => new Date(a.close_date).getTime() - new Date(b.close_date).getTime());
    } else if (sortBy === 'distance') {
        compsPropertyData.value = [...compsPropertyData.value].sort((a, b) => a.mile_range_from_subject - b.mile_range_from_subject);
    } else if (sortBy === 'sqft') {
        compsPropertyData.value = [...compsPropertyData.value].sort((a, b) => a.total_finished_sqft - b.total_finished_sqft);
    } else if (sortBy === 'relevance') {
        // high close price
        compsPropertyData.value = [...compsPropertyData.value].sort((a, b) => b.close_price - a.close_price);
        // Calculate relevance score for each property
        const propertiesWithScore = compsPropertyData.value.map(property => {
            let totalScore = 0;

            // Distance score (closer = higher score, max 25 points)
            const distanceScore = Math.max(0, 25 - (property.mile_range_from_subject * 5));
            totalScore += distanceScore;

            // Bedrooms score (exact match = 20 points, 1 difference = 10 points, 2+ difference = 0 points)
            const bedroomDiff = Math.abs(property.bedrooms_count - subjectProperty.bedrooms);
            const bedroomScore = bedroomDiff === 0 ? 20 : bedroomDiff === 1 ? 10 : 0;
            totalScore += bedroomScore;

            // Total finished sqft score (closer = higher score, max 20 points)
            const sqftDiff = Math.abs(property.total_finished_sqft - subjectProperty.total_finished_sqft);
            const sqftPercentage = Math.min(1, sqftDiff / subjectProperty.total_finished_sqft);
            const sqftScore = Math.max(0, 20 - (sqftPercentage * 20));
            totalScore += sqftScore;

            // Lot size score (closer = higher score, max 15 points)
            const lotDiff = Math.abs(property.lot_sqft - subjectProperty.lot_size);
            const lotPercentage = Math.min(1, lotDiff / Math.max(subjectProperty.lot_size, 1));
            const lotScore = Math.max(0, 15 - (lotPercentage * 15));
            totalScore += lotScore;

            // Closed date score (more recent = higher score, max 20 points)
            const daysSinceClosed = Math.abs((subjectProperty.currentDate.getTime() - new Date(property.close_date).getTime()) / (1000 * 60 * 60 * 24));
            const dateScore = Math.max(0, 20 - (daysSinceClosed / 30)); // 20 points for recent, decreasing over time
            totalScore += dateScore;
            return {
                ...property,
                relevanceScore: totalScore
            };
        });

        // Sort by relevance score (highest first)
        compsPropertyData.value = propertiesWithScore
            .sort((a, b) => b.relevanceScore - a.relevanceScore)
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
            .map(({ relevanceScore, ...property }) => property); // Remove relevanceScore from final result

        pickforMeList.value = propertiesWithScore.slice(0, 3).map(property => property.id);
    }
}


const scrollToCompsReport = () => {
    if (compsReportContainerRef.value) {
        compsReportContainerRef.value.scrollIntoView({ behavior: 'smooth' });
    }
}

const scrollToDetailLoadingContainer = () => {
    if (detailLoadingContainerRef.value) {
        detailLoadingContainerRef.value.scrollIntoView({ behavior: 'smooth' });
    }
}

</script>

<template>
    <TooltipProvider :delay-duration="0">
        <UnlistedPropertySheet />
        <PropertyDetailCompsSheet v-if="openCompsDialog" :_PropertyId="selectedPropertyId"
            v-model:open="openCompsDialog" />

    </TooltipProvider>
    <GuestAppLayout>
        <div class="min-h-[calc(60dvh)] flex flex-col justify-center items-center relative py-10" id="page-container">
            <div class="max-w-7xl mx-auto w-full px-4">
                <div class="flex flex-col gap-4 items-center max-w-lg mx-auto">
                    <div class="text-center">
                        <span class="text-2xl font-bold">Good morning, {{ user?.name }}</span>
                        <br>
                        To create your first report, search for a property below.
                    </div>
                    <AddressSearchInput v-model:address="address" class="h-[50px] text-base md:text-lg" />
                </div>
                <div class="grid grid-cols-2 gap-2 items-center w-full max-w-7xl mx-auto mt-10 h-full"
                    v-if="zillowData && !loadingZillow">
                    <div @click="openUnlistedPropertyDetail(address?.geoAddress || '')"
                        class="shadow-sm hover:shadow-md transition-all duration-300 border rounded-lg text-sm cursor-pointer w-full max-w-sm mx-auto col-span-2 md:col-span-1 mb-5 dark:border-gray-700 dark:bg-gray-800/40">
                        <div class="h-full w-full flex flex-col">
                            <div class="img relative min-h-[150px] md:min-h-[200px] w-full p-2 bg-white rounded-t-lg bg-cover bg-center flex-1"
                                :style="{ backgroundImage: `url(${zillowData?.imgSrc})` }">
                                <div class="absolute top-2 right-2 flex flex-col items-end gap-2 text-sm">
                                    <!-- <div class="flex-1 p-1 rounded-lg bg-gray-800/40 cursor-pointer select-none"
                                            v-if="selectionShow" @click.stop="onCheck">
                                            <Icon
                                                :class="'action-icon ' + (selectionAddresses?.some(a => a.zpid === props.address.zpid && a.list_price === props.address.list_price && a.zEstimateRent === props.address.zEstimateRent) ? 'text-primary' : 'text-white hover:text-primary')"
                                                :icon="selectionAddresses?.some(a => a.zpid === props.address.zpid && a.list_price === props.address.list_price && a.zEstimateRent === props.address.zEstimateRent) ? 'tabler:square-rounded-check-filled' : 'tabler:square-rounded'" />
                                        </div> -->
                                </div>
                                <!-- <div class="absolute bottom-2 right-2 flex flex-col items-end gap-2 text-sm">
                                        <div
                                            class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold flex gap-1">
                                            <Icon icon="solar:list-cross-broken" class="size-5" />
                                            Unlisted
                                        </div>
                                    </div> -->
                            </div>
                            <div class="bottom-container flex-1 gap-2 flex flex-col">
                                <div
                                    class="address-container pt-2 px-2 truncate overflow-hidden whitespace-nowrap text-base">
                                    <span>{{ address?.geoAddress }}</span>
                                </div>
                                <div class="flex flex-row p5-2 gap-2 px-2">
                                    <Icon icon="tabler:home-2" class="size-5" />
                                    <div class=""><span class="font-semibold">{{ zillowData?.bedrooms }}</span> Beds,
                                    </div>
                                    <div class=""><span class="font-semibold"> {{ zillowData?.bathrooms
                                    }}</span>
                                        Baths
                                    </div>

                                    <div class="" v-if="zillowData?.propertyTypeDimension">|&nbsp;<span
                                            class="font-semibold"> {{ zillowData?.propertyTypeDimension
                                                ?? 'Unknown' }}</span>
                                    </div>
                                </div>
                                <div class=" px-2 gap-2 flex flex-row">
                                    <Icon icon="tabler:box-align-bottom-left" class="size-5" />
                                    <div class="">
                                        <span class="font-semibold"> {{
                                            formatNumber(zillowData?.livingArea ?? 0)
                                            }}</span> sqft;
                                    </div>
                                    <div class="font-semibold">
                                        {{ formatNumber(Number((zillowData?.resoFacts?.lotSize ||
                                            '0').replace(/[^0-9]/g, '')), 0) }}
                                    </div> sqft
                                </div>
                                <div class=" px-2 gap-2 flex flex-row">
                                    <Icon icon="tabler:building-community" class="size-5" />
                                    <div class=""> <span class="font-semibold"> {{
                                        zillowData?.yearBuilt ?? 'Unknown' }}</span>
                                    </div>

                                </div>

                                <div class=" px-2 gap-2 flex flex-row">
                                    <Icon icon="tabler:school" class="size-5" />
                                    <div class="truncate"> {{
                                        datatreePropertyInfo?.SchoolDistrictName ?? '-'
                                    }}
                                    </div>
                                </div>
                                <div class="p-1 bg-transparent"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 items-center max-w-lg mx-auto mt-5 mb-5 col-span-2 md:col-span-1 md:min-w-lg"
                        :class="{ 'hidden md:flex': loadingAverage || loadingProperty || step3Loading }">
                        <div class="form-container grid grid-cols-1 gap-3 w-full" v-if="zillowData">
                            <div class="w-full grid grid-cols-1 md:flex">
                                <Label class="md:flex-1">
                                    Property Type:
                                </Label>
                                <Combobox class="w-full md:flex-2" :optionValues="propertyTypeList"
                                    :tag-font-size="!isMobile ? '13px' : null" v-model="form.propertyTypes" />
                            </div>
                            <div class="w-full grid grid-cols-1 md:flex ">
                                <Label class="md:flex-1">
                                    Closed With-in
                                </Label>
                                <div class="w-full md:flex-2">
                                    <SelectBox v-model="form.closedOn" :options="closedOnOptions"
                                        placeholder="Select a closed on date" aria-label="Closed on date" />
                                </div>
                                <!-- <Input type="date" v-model="form.closedOn"
                                    class="text-right md:flex-2 md:ml-[10px] custom-date-input" /> -->
                            </div>
                            <div class="w-full grid grid-cols-1 md:flex">
                                <Label class="md:flex-1">
                                    Max Distance
                                </Label>
                                <div class="w-full md:flex-2">
                                    <SelectBox v-model="form.distanceMax" :options="mileRanges"
                                        placeholder="Select a mail range" aria-label="Mail range" />
                                </div>
                            </div>
                            <div class="w-full grid grid-cols-1 md:flex">
                                <Button variant="ghost" class="w-fit cursor-pointer ml-auto"
                                    @click="MoreFiltersOpen = !MoreFiltersOpen">
                                    More
                                    <Icon :icon="MoreFiltersOpen ? 'tabler:chevron-up' : 'tabler:chevron-down'"
                                        class="size-5 ml-2 transition-transform" />
                                </Button>
                            </div>
                            <Transition enter-active-class="transition-all duration-300 ease-out"
                                enter-from-class="opacity-0 max-h-0 overflow-hidden"
                                enter-to-class="opacity-100 max-h-96"
                                leave-active-class="transition-all duration-300 ease-in"
                                leave-from-class="opacity-100 max-h-96"
                                leave-to-class="opacity-0 max-h-0 overflow-hidden">
                                <div class="w-full grid grid-cols-1 gap-3" v-if="MoreFiltersOpen">
                                    <div class="w-full grid grid-cols-1 md:flex">
                                        <Label class="md:flex-1">
                                            Sqft
                                        </Label>
                                        <div class="w-full grid grid-cols-2 md:flex-2 gap-1">
                                            <InputNumber v-model="form.sqftMin" />
                                            <InputNumber v-model="form.sqftMax" />
                                        </div>
                                    </div>
                                    <div class="w-full grid grid-cols-1 md:flex">
                                        <Label class="md:flex-1">
                                            School District
                                        </Label>
                                        <ComboboxServer v-model="form.schoolDistrict" class="w-full md:flex-2"
                                            :route="route('get.data.combobox_school_districts')"
                                            :tag-font-size="!isMobile ? '13px' : null">
                                        </ComboboxServer>
                                    </div>
                                    <div class="w-full grid grid-cols-1 md:flex">
                                        <Label class="md:flex-1">
                                            Beds
                                        </Label>
                                        <InputNumber v-model="form.beds" />
                                    </div>
                                    <div class="w-full grid grid-cols-1 md:flex">
                                        <Label class="md:flex-1">
                                            Baths
                                        </Label>
                                        <InputNumber v-model="form.baths" />

                                    </div>
                                    <div class="w-full grid grid-cols-1 md:flex">
                                        <Label class="md:flex-1">
                                            Status
                                        </Label>
                                        <div class="w-full md:flex-2">
                                            <Combobox :tag-font-size="!isMobile ? '13px' : null" v-model="form.status"
                                                :optionValues="statusOptions" />
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                            <div class="w-full grid grid-cols-1 md:flex gap-2">
                                <Button class="w-full" @click="onConfirmInformation"
                                    :disabled="loadingAverage || loadingProperty || step3Loading"
                                    :class="{ 'animate-pulse': loadingAverage || loadingProperty || step3Loading }">
                                    <Icon icon="tabler:sparkles" class="size-5" />
                                    Confirm Information
                                </Button>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 grid grid-cols-1 gap-4 items-center w-full mx-auto mt-5 mb-10" ref="detailLoadingContainerRef"
                        v-if="loadingAverage || loadingProperty || step3Loading">
                        <div class="px-4 py-4 bg-gray-100 dark:bg-gray-800 rounded-lg w-full" v-if="loadingAverage">
                            <div
                                class="flex max-w-fit text-sm gap-2 px-2 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 mb-2">
                                Step 1 / 3</div>
                            Getting Closed Properties

                            <span class="text-sm text-muted-foreground mb-2">
                                <LoaderAnimiatedTexts :texts="[`Fetching properties from ${form.zip} Zip Code`,
                                `Fetching properties with in ${form.distanceMax} miles`,
                                `Fetching properties with in ${form.sqftMin} - ${form.sqftMax} sqft`,
                                `Fetching properties matching ${form.propertyTypes.join(', ')} Property Types`,
                                `Fetching properties closed on after ${form.closedOn} days`]" />
                            </span>
                            <br />
                            <div class="grid grid-cols-1">
                                <div
                                    class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden relative">
                                    <div class="bg-yellow-500 h-2 rounded-full progress-slider" style="width: 20%;">
                                    </div>
                                </div>
                                <div
                                    class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 mx-auto inline-flex items-center gap-2 animate-pulse">
                                    Fetching Data...
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-4 bg-gray-100 dark:bg-gray-800 rounded-lg w-full" v-if="loadingProperty">
                            <div
                                class="flex max-w-fit text-sm gap-2 px-2 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 mb-2">
                                Step 2 / 3</div>
                            Analyzing Properties
                            <span class="text-sm text-muted-foreground mb-2">
                                <LoaderAnimiatedTexts :texts="loadingPropertyTexts" />
                            </span>
                            <br />
                            <div class="grid grid-cols-1">
                                <div
                                    class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden relative">
                                    <div class="bg-yellow-500 h-2 rounded-full progress-slider" style="width: 20%;">
                                    </div>
                                </div>
                                <div
                                    class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 mx-auto inline-flex items-center gap-2 animate-pulse">
                                    Fetching Data...
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-4 bg-gray-100 dark:bg-gray-800 rounded-lg w-full" v-if="step3Loading">
                            <div
                                class="flex max-w-fit text-sm gap-2 px-2 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 mb-2">
                                Step 3 / 3</div>
                            Generating List
                            <span class="text-sm text-muted-foreground mb-2">
                                <LoaderAnimiatedTexts :texts="['Finalizing list...']" />
                            </span>
                            <br />
                            <div class="grid grid-cols-1">
                                <div
                                    class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden relative">
                                    <div class="bg-yellow-500 h-2 rounded-full progress-slider" style="width: 20%;">
                                    </div>
                                </div>
                                <div
                                    class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 mx-auto inline-flex items-center gap-2 animate-pulse">
                                    Fetching Data...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-4 items-center max-w-lg mx-auto mt-10" v-if="loadingZillow">
                    <div class="text-center">
                        <Icon icon="tabler:loader-2" class="animate-spin text-2xl" />
                    </div>
                </div>
                <div class="flex flex-col gap-4 items-center max-w-lg mx-auto mt-10"
                    v-if="errorAverage || errorProperty">
                    <div class="text-center">
                        <Icon icon="tabler:alert-circle" class="text-red-500 text-2xl" />
                        <div class="text-red-500 text-sm">
                            {{ errorAverage || errorProperty }}
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="isInMarketCoverageAddress" class="">
                <div class="grid gap-2 items-center w-full mx-auto mt-10 h-full"
                    :class="{ 'fixed -top-10 left-0 z-50 bg-input': fullScreen }">
                    <div class="flex flex-row gap-2 justify-center items-center w-full py-10"
                        v-if="initialFilterDone && !loadingAverage && !loadingProperty && !step3Loading && compsPropertyData && compsPropertyData.length === 0">
                        <div class="text-sm text-muted-foreground">
                            No Properties Found
                        </div>
                    </div>
                    <div class="properties-viewer w-full text-sm pt-5 relative bg-input/30 dark:bg-gray-800/40 rounded-lg py-5 px-2  "
                        :class="{ 'shadow-none': fullScreen }"
                        v-if="!loadingAverage && !loadingProperty && !step3Loading && compsPropertyData && compsPropertyData.length > 0">
                        <div class="flex flex-row gap-1 md:gap-2 justify-end items-end w-full" v-if="!showCompsReoprt">
                            <div class="flex-row gap-2 my-auto flex-1 text-lg font-semibold hidden md:flex">
                                Comparable Properties
                            </div>
                            <div class="flex-row gap-2 my-auto md:hidden px-2 text-center">
                                {{compsPropertyData.filter(p => p.mile_range_from_subject !== 0).length}}
                                <br /><span class="text-xs">Found</span>
                            </div>
                            <div class="flex-row gap-2 my-auto hidden md:flex">
                                Found {{compsPropertyData.filter(p => p.mile_range_from_subject !== 0).length}}
                                properties
                            </div>
                            <div class="flex-row  my-auto">
                                <SelectBox class="w-fit m-0 bg-white dark:bg-gray-700" v-model="sortBy"
                                    :options="sortByOptions" @update:modelValue="onSortBy(sortBy)" />
                            </div>
                            <!-- <CustomTooltip :text="fullScreen ? 'Close Full Screen' : 'Full Screen'" position="top">
                                <Button class="w-fit hidden md:flex" @click="toggleFullScreen">
                                    <Icon :icon="fullScreen ? 'tabler:window-minimize' : 'tabler:window-maximize'"
                                        class="size-5" />
                                </Button>
                            </CustomTooltip> -->
                            <CustomTooltip :text="viewType === 'map' ? 'Thumb View' : 'Map View'" position="top">
                                <Button class="w-fit" @click="toggleViewType(viewType === 'map' ? 'thumb' : 'map')">
                                    <Icon :icon="viewType === 'map' ? 'tabler:grid' : 'tabler:map-2'" class="size-5" />
                                </Button>
                            </CustomTooltip>
                            <Button class="w-fit md:w-fit relative" @click="getReport"
                                :disabled="selectedIds.length < 1">
                                <Icon icon="tabler:report" class="size-5" /> <span class="hidden md:flex">Get
                                    Report</span>
                                <div class="bg-gray-500 text-white rounded-full px-2 py-1 absolute -top-2 -right-2"
                                    v-if="selectedIds.length > 0">
                                    {{ selectedIds.length }}
                                </div>
                            </Button>
                            <Button variant="outline" class="w-fit md:w-fit relative"
                                @click="selectedIds = pickforMeList">
                                <Icon icon="tabler:checkup-list" class="size-5" /> <span class="hidden md:flex">Pick For
                                    Me</span>
                            </Button>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline" class="w-fit">
                                        <Icon icon="tabler:dots-vertical" class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem @click="selectAllProperties">
                                        <Icon icon="tabler:select-all" class="size-4 mr-2" />
                                        Select All
                                    </DropdownMenuItem>
                                    <DropdownMenuItem @click="deselectAllProperties">
                                        <Icon icon="tabler:deselect" class="size-4 mr-2" />
                                        Deselect All
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                        <div v-if="showCompsReoprt"
                            class="flex flex-row gap-1 md:gap-2 justify-between items-end w-full px-2">
                            <div class="flex-row gap-2 my-auto flex-wrap text-lg font-semibold">
                                Comparable Properties
                            </div>
                            <div class="flex-row gap-2 my-auto flex-wrap text-right">
                                {{ selectedIds.length }} selected
                            </div>
                        </div>
                        <div v-if="viewType === 'thumb'"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 overflow-auto max-w-7xl mt-4 mx-auto"
                            :class="{ 'h-[calc(88dvh)]': fullScreen }">
                            <div class="min-w-[350px] w-full"
                                v-for="property in showCompsReoprt ? compsPropertyData?.filter(p => selectedIds.includes(p.id)) : compsPropertyData"
                                :key="property.id">
                                <InstantCompsPropertyThumb @click="openPropertyDetail(property.id)"
                                    class=" bg-input/30 border m-2" v-model:selectionIds="selectedIds"
                                    :subjectPropertyData="zillowData" :propertyListData="property"
                                    :showSelection="!showCompsReoprt" :show-distance-from="true"
                                    :calculatedData="CalculatedValues?.data" />
                            </div>
                        </div>
                        <div v-if="viewType === 'map'"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-2 gap-2 overflow-auto  pt-4"
                            :class="{ 'h-[calc(90dvh)]': fullScreen }" ref="propertyViewRef">
                            <div
                                class="flex-1 md:col-span-1 min-h-[300px] lg:col-span-2 xl:col-span-1 max-h-[calc(90dvh)]">
                                <PropertyListMapView :properties="compsPropertyData"
                                    @scrollToProperty="scrollToProperty"
                                    :filterValues="{ comps_sub_prop_id: `${zillowData?.latitude}|${zillowData?.longitude}`, distance_max: form.distanceMax }" />
                            </div>
                            <div
                                class="flex-1 md:col-span-1 h-[calc(45dvh - 50px)] md:h-[calc(90dvh - 50px)] overflow-auto ">
                                <div class="grid grid-cols-1 xl:grid-cols-2 gap-2">
                                    <div class="min-w-[350px]" v-for="property in compsPropertyData" :key="property.id">
                                        <InstantCompsPropertyThumb @click="openPropertyDetail(property.id)"
                                            class=" bg-input/30 border m-2" v-model:selectionIds="selectedIds"
                                            :subjectPropertyData="zillowData" :propertyListData="property"
                                            :showSelection="!showCompsReoprt" :showDistanceFrom="true" :isMap="true" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-row gap-2 justify-end items-end w-full">
                        <Button v-if="!showCompsReoprt" class="w-fit md:w-fit relative" @click="getReport"
                            :disabled="selectedIds.length < 1">
                            <Icon icon="tabler:report" class="size-5" /> <span class="hidden md:flex">Get
                                Report</span>
                            <div class="bg-gray-500 text-white rounded-full px-2 py-1 absolute -top-2 -right-2"
                                v-if="selectedIds.length > 0">
                                {{ selectedIds.length }}
                            </div>
                        </Button>
                        <Button v-if="!showCompsReoprt" variant="outline" class="w-fit md:w-fit relative"
                            @click="selectedIds = pickforMeList">
                            <Icon icon="tabler:checkup-list" class="size-5" /> <span class="hidden md:flex">Pick For
                                Me</span>
                        </Button>
                        <Button v-if="showCompsReoprt" variant="outline" class="w-fit"
                            @click="showCompsReoprt = false">Select
                            Different Comps</Button>
                    </div>
                    </div>
                    
                    <div ref="compsReportContainerRef">
                        <CompsReportContainer v-if="showCompsReoprt" :zillowData="zillowData ?? null"
                            :formParams="setParamsAverage()" :selectedIds="selectedIds"
                            :calculatedValues="CalculatedValues?.data" :properties="compsPropertyData" />
                    </div>
                </div>
            </div>
            <div v-if="!isInMarketCoverageAddress"
                class="text-left mt-10 flex gap-2 items-start bg-gray-100 dark:bg-gray-800 rounded-lg p-4 mx-2 max-w-3xl">
                <Icon icon="tabler:alert-circle" class=" text-muted-foreground text-2xl min-w-5 min-h-5 " />
                <div class="text-sm">
                    <div class="font-semibold text-base">Sorry, this address is currently outside our supported market
                        area.</div>
                    <div class="text-muted-foreground">
                        At this time, instant comps are only available for properties located in these states:
                    </div>
                    <div class="text-muted-foreground font-medium">{{ marketCoverageStates.join(', ') }}</div>
                    <br />
                    <div class="text-muted-foreground">
                        Want to see where we operate? View our <a :href="route('guest.market_coverage')" target="_blank"
                            class="text-primary hover:underline cursor-pointer">market coverage map</a> for details.
                    </div>
                </div>
            </div>
        </div>

    </GuestAppLayout>

</template>

<style scoped>
.progress-slider {
    animation: slide 1.5s ease-in-out infinite;
}

@keyframes slide {
    0% {
        transform: translateX(-100%);
    }

    50% {
        transform: translateX(400%);
    }

    100% {
        transform: translateX(-100%);
    }
}
</style>