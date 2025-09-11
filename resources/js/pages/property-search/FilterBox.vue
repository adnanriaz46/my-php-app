<script setup lang="ts">
import { Icon } from '@iconify/vue'
import UnlistedPropertySearchInput from "@/pages/property-search/subs/UnlistedPropertySearchInput.vue";
import { useConfirmDialog } from '@/composables/useConfirmDialog'
import {
  PopoverArrow,
  PopoverClose,
  PopoverContent,
  PopoverPortal,
  PopoverRoot,
  PopoverTrigger, RadioGroupIndicator,
  RadioGroupItem, RadioGroupRoot
} from 'reka-ui'
import Button from "@/components/ui/button/Button.vue";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup, DropdownMenuItem,
  DropdownMenuTrigger, DropdownMenuSeparator
} from "@/components/ui/dropdown-menu";
import { ChevronsUpDown } from "lucide-vue-next";
import { SidebarMenuButton } from "@/components/ui/sidebar";

import Label from "@/components/ui/label/Label.vue";

import {
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectItemIndicator,
  SelectItemText,
  SelectPortal,
  SelectRoot,
  SelectScrollDownButton,
  SelectScrollUpButton,
  SelectTrigger,
  SelectValue,
  SelectViewport,
} from 'reka-ui'
import { computed, onMounted, ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { objectToSelectArray, useFilterOptions } from "@/composables/useFilterOptions";
import CheckboxLoop from "@/components/ui/checkbox-loop/CheckboxLoop.vue";
import MilePropertySearchInput from "@/pages/property-search/subs/MilePropertySearchInput.vue";
import SelectBox from "@/components/ui/select-box/SelectBox.vue";
import Combobox from "@/components/ui/combobox/Combobox.vue";
import axios from "axios";
import Input from "@/components/ui/input/Input.vue";
import InputNumber from "@/components/ui/input-number/InputNumber.vue";
import Separator from "@/components/ui/separator/Separator.vue";
import Checkbox from "@/components/ui/checkbox/Checkbox.vue";
import ListingAgentSearchInput from "@/pages/property-search/subs/ListingAgentSearchInput.vue";
import ComboboxServer from "@/components/ui/combobox-server/ComboboxServer.vue";
import {
  dealTypeFields,
  domFilterFields, goldFilterFields,
  isDealTypeValuesExists, isDomValuesExists, isGoldValuesExists,
  isLocationValuesExists, isMoreValuesExists,
  isStatusValuesExists, locationFields, mapSavedSearchToForm, MileSearchAddress,
  moreFilterFields, resetFormFields, statusFields
} from "@/pages/property-search/subs/filterHelper";
import PopupApplyFilterButton from "@/pages/property-search/subs/PopupApplyFilterButton.vue";
import Dialog from "@/components/ui/dialog/Dialog.vue";
import {
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { SavedSearchFull } from "@/types/propertySearch";
import { useToast } from '@/composables/useToast'
import Toast from "@/components/ui/toast/Toast.vue";
import ConfirmDialog from "@/components/ui/confirm-dialog/ConfirmDialog.vue";
import { usePage } from '@inertiajs/vue3';
import { useScreen } from '@/composables/useScreen'
import { paramsDBApiGetProperty } from "@/types/DBApi";
import { PropertyStatus, PropertyViewType, PropertyViewTypes } from "@/types/property";
import { getDateDaysBefore } from "@/lib/utils";
import { getMyPropertyList } from "@/lib/myListUtil";
import { MyPropertyList } from "@/types/myList";
import { UserTypes } from '@/types/userTypes';
import { Auth, User } from '@/types';


const { isMobile } = useScreen()

const page = usePage();

const auth = computed(() => page.props.auth as Auth);
const user = computed(() => auth.value.user as User);


const { showToast } = useToast()

interface filterPopoverOpenTypes {
  status: boolean,
  dealType: boolean,
  location: boolean,
  gold: boolean,
  more: boolean,
  dom: boolean,
}

interface pageAttrBool {
  page: boolean,
  map: boolean,
  list: boolean,
  counties: boolean;
  savedSearchList: boolean,
}

interface pageAttrString {
  page: string | null,
  map: string | null,
  list: string | null,
  counties: string | null;
  savedSearchList: string | null,
}

const filterPopoverOpen = ref<filterPopoverOpenTypes>(
  { status: false, dom: false, dealType: false, gold: false, location: false, more: false }
);

const props = defineProps<{
  viewType: PropertyViewType,
  defaultParams: paramsDBApiGetProperty,
}>()

const emit = defineEmits<{
  (e: 'update-view-type', PropertyViewType: PropertyViewType): void
  (e: 'update-my-list', selectedMyList: MyPropertyList | null): void
  (e: 'update-filters', payload: {
    filter: paramsDBApiGetProperty;
    initial: boolean
  }): void;
}>();


const form = useForm({
  // Status
  status: props.defaultParams?.status,

  // DealType
  deal_type: props.defaultParams?.deal_type,

  // Location
  proximity_object: null,
  comps_sub_prop_id: '', // lat|lng or id
  distance_max: '',
  county: [],
  city_name_keyword: '',
  zip: '',

  // Gold Filter
  gold_filter_type: '',
  delta_min: props.defaultParams?.delta_min,
  delta_max: '',
  est_profit_min: '',
  est_profit_max: '',
  est_cashflow_min: '',
  est_cashflow_max: '',
  accuracy_score_rent: '',
  accuracy_score_value: '',

  // DOM
  dom_min: '',
  dom_max: '',
  closed_date_min: '',
  closed_date_max: '',

  // More
  list_price_min: '',
  list_price_max: '',
  structure_type: [],
  fulladdress_avoid: '',
  total_finished_sqft_min: '',
  total_finished_sqft_max: '',
  lot_sqf_min: '',
  lot_sqf_max: '',
  year_build_min: '',
  year_build_max: '',
  bedrooms_min: '',
  bedrooms_max: '',
  remarks_public_keywords: '',
  list_agent_keyword: '',
  school_district: [],
  medianrent_min: '',
  medianrent_max: '',
  est_arv_min: '',
  est_arv_max: '',

  // Order BY
  // order_by: '', // controlled by other component

  // Limit
  // _limit: '',  // controlled by other component


  // Additional
  state_or_province_keyword: '',
  fulladdress_keyword: '',
  mls_number: '',
  listing_entry_date_min: '',
  listing_entry_date_max: '',
  user_lat: '',
  user_lng: '',
  test: '',
  city_names_avoid: '',
  filter_ids: [],
  all_wholesale: 0,
  map_bound_range: '',

  //SavedSearchFields
  savedSearchId: 0,
  savedSearchName: '',
  savedSearchAsNew: true,
});


const MobileMenuTypes = Object.freeze({
  Filter: 1,
  Search: 2,
  View: 3,
});

export type MobileMenuType = typeof MobileMenuTypes[keyof typeof MobileMenuTypes];

const GoldFilterTypes = Object.freeze({
  None: 0,
  FixAndFlip: 1,
  BuyAndHold: 2,
  BelowMarket: 3,
});
export type GoldFilterType = typeof GoldFilterTypes[keyof typeof GoldFilterTypes];

const isLoading = ref<pageAttrBool>({
  page: false,
  map: false,
  list: false,
  counties: false,
  savedSearchList: false,
})
const errors = ref<pageAttrString>({
  page: '',
  map: '',
  list: '',
  counties: '',
  savedSearchList: '',
})

const savedSelectBoxKey = ref<number>(0);

const filterOptionValues = useFilterOptions();
const savedSearchList = ref<SavedSearchFull[]>([]);

const countiesList = ref<string[]>([]);
const propertyTypeList = ref<string[]>(Object.values(filterOptionValues.structureType));

const selectedSavedSearchList = ref<SavedSearchFull | null>(null);
// const selectedMileSearchAddress = ref<MileSearchAddress | null>(null)

const showAdvanceGoldFilter = ref<boolean>(false);

const resetFormToEmpty = () => {
  const emptyValues: Record<string, any> = {};

  for (const key in form.data()) {
    const value = form.data()[key];
    if (Array.isArray(value)) {
      emptyValues[key] = [];
    } else if (typeof value === 'string') {
      emptyValues[key] = '';
    } else if (typeof value === 'number') {
      emptyValues[key] = 0;
    } else if (typeof value === 'boolean') {
      emptyValues[key] = false;
    } else if (value === null) {
      emptyValues[key] = null;
    } else {
      emptyValues[key] = ''; // fallback for anything else
    }
  }

  form.defaults(emptyValues);
  form.reset()
};

const fetchCounties = async () => {
  isLoading.value.counties = true;
  errors.value.counties = null

  try {
    const response = await axios.get('/data/get-counties')
    countiesList.value = response.data
  } catch (err: any) {
    errors.value.counties = err.message || 'Failed to load counties.'
  } finally {
    isLoading.value.counties = false;
  }
}
const fetchSavedSearch = async (setDefault = false) => {
  isLoading.value.savedSearchList = true;
  errors.value.savedSearchList = null
  try {
    const response = await axios.get(route('property.search.save_search.get_list'))
    savedSearchList.value = response.data?.data;
  } catch (err: any) {
    errors.value.savedSearchList = err.message || 'Failed to load counties.'
    showToast('Saved Search List', errors.value?.savedSearchList ?? '', 'error')
  } finally {
    isLoading.value.savedSearchList = false;
    if (setDefault) {
      if (user.value.user_type == UserTypes.FREE) {
        applyFilter();
        return;
      }
      //await nextTick()
      const defaultSearch = savedSearchList.value.find((v) => v?.default === true);
      defaultSaveSearchId.value = defaultSearch?.id ?? null;
      if (defaultSaveSearchId.value !== null) {
        selectedSavedSearchList.value = savedSearchList.value.find(
          (v) => v?.id === defaultSaveSearchId.value
        ) ?? null;
      } else {
        applyFilter();
      }
      savedSelectBoxKey.value++;
    }
  }
}

const clearStatus = () => resetFormFields(form, statusFields);
const clearLocation = () => resetFormFields(form, locationFields);
const clearDealType = () => resetFormFields(form, dealTypeFields);
const clearDom = () => resetFormFields(form, domFilterFields);
const clearMore = () => resetFormFields(form, moreFilterFields);
const clearGold = () => {
  resetFormFields(form, goldFilterFields);
  onGoldFilterTypeClick(0);
};

watch(() => form.proximity_object, (value: MileSearchAddress) => {
  let parsedValue: MileSearchAddress = value;
  try {
    parsedValue = JSON.parse(parsedValue);
  } catch (error) {
    console.error("Invalid JSON:", error);
  }
  if (parsedValue?.lat) {
    form.comps_sub_prop_id = parsedValue.lat + "|" + parsedValue.lng;
  }
});

watch(() => form.status, () => {
  if (form.status?.includes(PropertyStatus.Closed)) {
    if (!form.closed_date_min)
      form.closed_date_min = getDateDaysBefore(30 * 5);
  }
}, { deep: true })

watch(
  () => selectedSavedSearchList.value,
  async (newVal) => {
    if (newVal) {
      mapSavedSearchToForm(form, newVal);
      //await nextTick()
      applyFilter()
    }
  },
  { immediate: true }
)

onMounted(() => {
  form.defaults(props.defaultParams);
  fetchCounties();
  fetchSavedSearch(true);
  fetchMyList();
});

const selectedMobileMenu = ref<MobileMenuType | null>(null)
const onMobileMenuClick = (menuType: any) => {
  if (selectedMobileMenu.value == menuType) {
    selectedMobileMenu.value = null;
  } else {
    selectedMobileMenu.value = menuType;
  }
}
const onGoldFilterTypeClick = (filterType: GoldFilterType) => {

  if (form.gold_filter_type === filterType) {
    form.gold_filter_type = null;
    form.est_profit_min = '';
    form.accuracy_score_value = '';
    form.est_cashflow_min = '';
    form.delta_min = '';
  } else {
    form.gold_filter_type = filterType;
    form.est_profit_min = '';
    form.accuracy_score_value = '80';
    form.est_cashflow_min = '';
    form.delta_min = '';
    if (filterType === GoldFilterTypes.FixAndFlip) {
      form.est_profit_min = '30000';
    } else if (filterType === GoldFilterTypes.BuyAndHold) {
      form.est_cashflow_min = '300';
    } else if (filterType === GoldFilterTypes.BelowMarket) {
      form.delta_min = '50';
    } else if (filterType === GoldFilterTypes.None) {
      form.accuracy_score_value = '';
    }
  }
}

const propertyViewType = ref<PropertyViewType>(props.viewType);

const onViewTypeChange = (viewType: PropertyViewType) => {
  propertyViewType.value = viewType;
  emit('update-view-type', propertyViewType.value);
}
const applyFilter = () => {
  filterPopoverOpen.value = {
    status: false,
    dom: false,
    dealType: false,
    gold: false,
    location: false,
    more: false,
  };

  emit('update-filters', { filter: form.data(), initial: false });
}
const defaultSaveSearchId = ref<number | null>(null);
const myPropertyList = ref<MyPropertyList[]>([]);
const selectedMyPropertyList = ref<MyPropertyList | null>(null);
const fetchMyList = async () => {
  const res = await getMyPropertyList();
  if (res?.error) {
    showToast('My List', res.error)
    return;
  }
  myPropertyList.value = res.data;
}

interface SavedSearchDialog {
  open: boolean;
  title: string;
  saveButtonText: string;
  description: string | null | undefined;
  nameValue: string | null | undefined;
  isNew: boolean;
  loading: false;
}

const savedSearchDialogRef = ref<SavedSearchDialog>({
  open: false,
  title: '',
  isNew: true,
  saveButtonText: 'Save'
  , description: undefined, loading: false, nameValue: undefined
})


const confirmDialog = useConfirmDialog()
const handleConfirmation = async (message: string, type: string) => {
  const confirmed = await confirmDialog.openConfirm(message)
  if (confirmed) {
    if (type == 'SavedSearch') {
      try {
        if (!selectedSavedSearchList.value?.id) {
          return;
        }
        const response = await axios.post(route('property.search.save_search.delete'), { id: selectedSavedSearchList.value?.id })
        await fetchSavedSearch();
        showToast('Delete Saved Search List', response.data?.message ?? 'Your saved search list has been removed', 'success')
      } catch (err) {
        showToast('Delete Saved Search List', 'Error: ' + err?.toString(), 'error')
        console.error(err)
      } finally {

      }
    }
  } else {
    console.log("Cancelled")
  }
}


const onSaveSearchPrompt = () => {

  savedSearchDialogRef.value.description = 'Name this alert. It will be saved in your "Saved Searches" and you will receive updates when new listings match the filter. ';
  if (!selectedSavedSearchList.value || !selectedSavedSearchList.value?.id) {
    savedSearchDialogRef.value.title = 'Save your search';
    savedSearchDialogRef.value.isNew = true;
    savedSearchDialogRef.value.saveButtonText = 'Save';
  } else {
    savedSearchDialogRef.value.nameValue = selectedSavedSearchList.value.saved_search_name;
    savedSearchDialogRef.value.title = 'Save/Update your search';
    savedSearchDialogRef.value.isNew = false;
    savedSearchDialogRef.value.saveButtonText = 'Update';

  }

  savedSearchDialogRef.value.open = true;
}

const onSaveSearchSetAsDefault = async () => {
  const res = await axios.post(route('property.search.save_search.set_default'), { id: selectedSavedSearchList.value?.id });
  if (res.data) {
    if (res.data?.success) {
      showToast('Set Default', res.data?.success, 'success');
    } else if (res.data?.error) {
      showToast('Set Default', res.data?.error, 'error');
    } else {
      showToast('Set Default', 'Something went wrong! Could not set as default', 'error');
    }
    return;
  }

  return;
}

const onSaveSearchSubmit = (asNew: boolean = true) => {
  form.savedSearchName = savedSearchDialogRef.value.nameValue;
  form.savedSearchAsNew = asNew;
  savedSearchDialogRef.value.loading = true;
  const parsedValue: MileSearchAddress | string = form.proximity_object;
  if (typeof parsedValue === 'string') {
    try {
      form.proximity_object = JSON.parse(form.proximity_object);
    } catch (error) {
      console.error("Invalid JSON:", error);
    }
  }

  if (!asNew) {
    form.savedSearchId = selectedSavedSearchList.value?.id;
  }
  form.post(route('property.search.save_search'), {
    preserveScroll: true,
    onSuccess: async () => {
      showToast('Saved Search List', 'List has been saved successfully', 'success')
      savedSearchDialogRef.value.open = false;
      await fetchSavedSearch();
      savedSearchDialogRef.value.loading = false;

      const savedSearchId = page.props.responseId;
      if (savedSearchId) {
        selectedSavedSearchList.value =
          savedSearchList.value.find((value) => value.id === savedSearchId) ?? null;

        savedSelectBoxKey.value++;
      }
    },
    onError: (errors) => {
      showToast('Saved Search List', 'Invalid request, Please validate your inputs', 'error')
      console.error(errors)
      savedSearchDialogRef.value.loading = false;
    }
  })

}

const clearSavedSearch = () => {
  selectedSavedSearchList.value = null;
  form.defaults(props.defaultParams);
  form.reset();
  emit('update-filters', { filter: form.data(), initial: true });
}

const onMyListClicked = (ml: MyPropertyList | null) => {
  emit('update-my-list', ml);
  if (ml == null) {
    selectedMyPropertyList.value = null;
    form.defaults(props.defaultParams);
    form.reset();
    emit('update-filters', { filter: form.data(), initial: true });
    return;
  }
  selectedMyPropertyList.value = ml;
  resetFormToEmpty();
  form.filter_ids = selectedMyPropertyList.value.property_ids;
  emit('update-filters', { filter: form.data(), initial: true });
  return;
}

defineExpose({ fetchMyList });
</script>

<template>
  <div class="w-full md:hidden flex justify-around mb-3">
    <Button class="relative w-[65px]" :variant="selectedMobileMenu == MobileMenuTypes.Filter ? 'secondary' : 'ghost'"
      @click="onMobileMenuClick(MobileMenuTypes.Filter)">
      <Icon icon="tabler:filter-search" class="iconAttr" />
      <Icon
        v-if="isMoreValuesExists || isDomValuesExists || isGoldValuesExists || isLocationValuesExists || isDealTypeValuesExists || isStatusValuesExists"
        icon="tabler:alert-square-rounded-filled" class="text-primary absolute top-0 right-0" />
    </Button>
    <DropdownMenu>
      <DropdownMenuTrigger as-child class="w-[65px] justify-center">
        <SidebarMenuButton class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
          <Icon icon="tabler:list-details" class="iconAttr" />
        </SidebarMenuButton>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="w-[100px] rounded-lg" :side="'bottom'" align="center" :side-offset="4">
        <DropdownMenuGroup>
          <DropdownMenuItem :as-child="true">
            <Label :class="PropertyViewTypes.Map == propertyViewType ? `bg-primary text-black` : ``"
              @click="onViewTypeChange(PropertyViewTypes.Map)">
              <Icon icon="tabler:map-2"
                :class="`iconAttr ` + (PropertyViewTypes.Map == propertyViewType ? `text-black` : ``)"></Icon>
              Map
            </Label>
          </DropdownMenuItem>
          <DropdownMenuItem :as-child="true">
            <Label :class="PropertyViewTypes.Grid == propertyViewType ? `bg-primary text-black` : ``"
              @click="onViewTypeChange(PropertyViewTypes.Grid)">
              <Icon icon="tabler:layout-2"
                :class="`iconAttr ` + (PropertyViewTypes.Grid == propertyViewType ? `text-black` : ``)"></Icon>
              Grid
            </Label>
          </DropdownMenuItem>
          <DropdownMenuItem :as-child="true">
            <Label :class="PropertyViewTypes.List == propertyViewType ? `bg-primary text-black` : ``"
              @click="onViewTypeChange(PropertyViewTypes.List)">
              <Icon icon="tabler:columns"
                :class="`iconAttr ` + (PropertyViewTypes.List == propertyViewType ? `text-black` : ``)"></Icon>
              List
            </Label>
          </DropdownMenuItem>
        </DropdownMenuGroup>
      </DropdownMenuContent>
    </DropdownMenu>
    <Button class="w-[65px]" :variant="selectedMobileMenu == MobileMenuTypes.Search ? 'secondary' : 'ghost'"
      @click="onMobileMenuClick(MobileMenuTypes.Search)">
      <Icon icon="tabler:search" class="iconAttr" />
    </Button>
    <DropdownMenu>
      <DropdownMenuTrigger as-child class="w-[65px]">
        <SidebarMenuButton :class="selectedMyPropertyList ? `bg-primary` : ``"
          class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground relative">
          <Icon icon="tabler:heart-code" class="iconAttr"></Icon>
          <ChevronsUpDown class="ml-auto size-4" />
          <Icon v-if="selectedMyPropertyList" icon="tabler:alert-square-rounded-filled"
            class="text-primary absolute top-0 right-0" />
        </SidebarMenuButton>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="max-w-[300px] rounded-lg" :side="'bottom'" align="end" :side-offset="4">
        <DropdownMenuGroup v-if="selectedMyPropertyList">
          <DropdownMenuItem :as-child="true" @click="onMyListClicked(null)">
            <Label>
              <Icon icon="tabler:xbox-x" class="size-4" />
              Clear list
            </Label>
          </DropdownMenuItem>
        </DropdownMenuGroup>
        <DropdownMenuSeparator v-if="selectedMyPropertyList" />
        <DropdownMenuGroup>
          <DropdownMenuItem v-for="ml in myPropertyList" :key="ml.id" :as-child="true" @click="onMyListClicked(ml)"
            :class="ml == selectedMyPropertyList ? ` bg-primary text-black ` : ``">
            <Label>
              {{ ml.name }} <span
                :class="ml == selectedMyPropertyList ? ` text-gray-600 ` : `text-muted-foreground`">({{
                  ml.property_ids.length
                }})</span>
            </Label>
          </DropdownMenuItem>
        </DropdownMenuGroup>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
  <div class="grid gap-3 md:flex relative">
    <div :class="['grid gap-1 absolute md:flex bg-default md:bg-transparent md:dark:bg-transparent w-full md:relative z-[1] px-3 md:p-0 rounded-lg shadow-md md:shadow-none',
      (MobileMenuTypes.Filter == selectedMobileMenu || MobileMenuTypes.Search == selectedMobileMenu) ? ' py-4 ' : '']">
      <Transition enter-active-class="transition-all duration-300 ease-in-out" enter-from-class="opacity-0 max-h-0"
        enter-to-class="opacity-100 max-h-[500px]" leave-active-class="transition-all duration-300 ease-in-out"
        leave-from-class="opacity-100 max-h-[500px]" leave-to-class="opacity-0 max-h-0">
        <UnlistedPropertySearchInput class="w-full md:max-w-[250px]"
          v-show="!isMobile || selectedMobileMenu == MobileMenuTypes.Search"></UnlistedPropertySearchInput>
      </Transition>

      <div class="border-r-1 md:border-gray-300 dark:md:border-gray-700 md:mx-2" />
      <Transition enter-active-class="transition-all duration-300 ease-in-out" enter-from-class="opacity-0 max-h-0"
        enter-to-class="opacity-100 max-h-[500px]" leave-active-class="transition-all duration-300 ease-in-out"
        leave-from-class="opacity-100 max-h-[500px]" leave-to-class="opacity-0 max-h-0">
        <div class="grid gap-1 md:flex" v-show="!isMobile || selectedMobileMenu == MobileMenuTypes.Filter">
          <!---------------- [STATUS] ------------------------------------------------------------------------------------>
          <PopoverRoot :open="filterPopoverOpen.status" @update:open="filterPopoverOpen.status = $event">
            <PopoverTrigger as-child
              class="rounded-xl md:inline-flex items-center justify-center text-grass11 bg-white border hover:bg-stone-50 cursor-default focus:shadow-[0_0_0_2px]
           focus:shadow-black dark:focus:shadow-green8 focus:outline-none dark:bg-gray-900 border-gray-300 shadow-sm dark:border-gray-700"
              aria-label="Update dimensions">
              <Button variant="filterPopover" class="relative">Status
                <Icon v-show="isStatusValuesExists(form)" icon="tabler:alert-square-rounded-filled"
                  class="text-primary absolute top-[-5px] right-[-5px]" />
              </Button>

            </PopoverTrigger>
            <PopoverPortal>
              <PopoverContent side="bottom" :side-offset="5"
                class="z-10 rounded-lg p-5 w-[calc(100vw-20px)] md:w-[300px] bg-white dark:bg-gray-900 shadow-2xl border border-gray-300 dark:border-gray-700 will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade">
                <p class="text-mauve12 text-sm leading-[19px] font-semibold mb-2.5">
                  Property Status
                </p>
                <div class="max-h-[150px] md:max-h-[500px] overflow-auto mt-3 overflow-x-hidden">
                  <div class="flex flex-col gap-2.5">

                    <ul class="list-none">
                      <li v-for="item in objectToSelectArray(filterOptionValues.status)" :key="item.key"
                        class="py-1 px-2 hover:bg-gray-200 rounded-lg dark:hover:bg-gray-700">
                        <Label :for="`checkbox-status-${item.key}`" class="text-md">
                          <CheckboxLoop :id="`checkbox-status-${item.key}`" v-model="form.status" :value="item.value"
                            :tabindex="3">
                          </CheckboxLoop>
                          {{ item.value }}
                        </Label>
                      </li>
                    </ul>
                  </div>
                </div>
                <PopupApplyFilterButton :on-clear="clearStatus" :on-apply="applyFilter" />
                <PopoverClose class="h-[35px] w-[35px] inline-flex items-center justify-center text-grass11 absolute top-[8px] right-[8px]
               hover:bg-green4 outline-none border-0 cursor-default p-0" aria-label="Close">
                  <Icon icon="tabler:square-rounded-x" class="closeIcon" />
                </PopoverClose>
                <PopoverArrow class="fill-white stroke-gray-200 dark:fill-gray-900 dark:stroke-gray-900" />
              </PopoverContent>
            </PopoverPortal>
          </PopoverRoot>

          <!---------------- [DealType] ------------------------------------------------------------------------------------>
          <PopoverRoot class="z-20" :open="filterPopoverOpen.dealType"
            @update:open="filterPopoverOpen.dealType = $event">
            <PopoverTrigger as-child
              class="rounded-xl md:inline-flex items-center justify-center text-grass11 bg-white border hover:bg-stone-50 cursor-default focus:shadow-[0_0_0_2px]
           focus:shadow-black dark:focus:shadow-green8 focus:outline-none dark:bg-gray-900 border-gray-300 shadow-sm dark:border-gray-700"
              aria-label="Update dimensions">
              <Button variant="filterPopover" class="relative">Deal Type
                <Icon v-show="isDealTypeValuesExists(form)" icon="tabler:alert-square-rounded-filled"
                  class="text-primary absolute top-[-5px] right-[-5px]" />
              </Button>
            </PopoverTrigger>
            <PopoverPortal>
              <PopoverContent side="bottom" :side-offset="5"
                class="z-10 rounded-lg p-5 w-[calc(100vw-20px)] md:w-[300px] bg-white dark:bg-gray-900 shadow-2xl border border-gray-300 dark:border-gray-700 will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade">
                <div class="flex flex-col gap-2">
                  <p class="text-mauve12 text-sm leading-[19px] font-semibold mb-2.5">
                    Deal Type
                  </p>
                  <div class="">
                    <RadioGroupRoot v-model="form.deal_type" class="flex flex-col gap-1" aria-label="Deal Type">
                      <div v-for="(option) in objectToSelectArray(filterOptionValues.dealType)" :key="option.key"
                        class="flex items-center">
                        <Label
                          class="text-stone-700 w-full dark:text-white text-sm leading-none cursor-pointer py-2 px-2  hover:bg-gray-200 rounded-lg dark:hover:bg-gray-700"
                          :for="(option.key)">

                          <RadioGroupItem :id="(option.key)"
                            class="bg-white w-[1.125rem] h-[1.125rem] rounded-full border data-[active=true]:border-stone-700 data-[active=true]:bg-stone-700 dark:data-[active=true]:bg-white shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-stone-700 outline-none cursor-pointer"
                            :value="option.value.value">
                            <RadioGroupIndicator
                              class="flex items-center justify-center w-full h-full relative after:content-[''] after:block after:w-2 after:h-2 after:rounded-[50%] after:bg-primary" />
                          </RadioGroupItem>

                          {{ option.value.display }}
                        </Label>
                      </div>
                    </RadioGroupRoot>
                  </div>
                </div>
                <PopupApplyFilterButton :on-clear="clearDealType" :on-apply="applyFilter" />
                <PopoverClose class="h-[35px] w-[35px] inline-flex items-center justify-center text-grass11 absolute top-[8px] right-[8px]
               hover:bg-green4 outline-none border-0 cursor-default p-0" aria-label="Close">
                  <Icon icon="tabler:square-rounded-x" class="closeIcon" />
                </PopoverClose>
                <PopoverArrow class="fill-white stroke-gray-200 dark:fill-gray-900 dark:stroke-gray-900" />
              </PopoverContent>
            </PopoverPortal>
          </PopoverRoot>

          <!---------------- [LOCATION] ------------------------------------------------------------------------------------>
          <PopoverRoot class="z-20" :open="filterPopoverOpen.location"
            @update:open="filterPopoverOpen.location = $event">
            <PopoverTrigger as-child
              class="rounded-xl md:inline-flex items-center justify-center text-grass11 bg-white border hover:bg-stone-50 cursor-default focus:shadow-[0_0_0_2px]
           focus:shadow-black dark:focus:shadow-green8 focus:outline-none dark:bg-gray-900 border-gray-300 shadow-sm dark:border-gray-700"
              aria-label="Update dimensions">
              <Button variant="filterPopover" class="relative">Location
                <Icon v-show="isLocationValuesExists(form)" icon="tabler:alert-square-rounded-filled"
                  class="text-primary absolute top-[-5px] right-[-5px]" />
              </Button>
            </PopoverTrigger>
            <PopoverPortal>
              <PopoverContent side="bottom" :side-offset="5"
                class="z-10 rounded-lg p-5 w-[calc(100vw-20px)] md:w-[450px] bg-white dark:bg-gray-900 shadow-2xl border border-gray-300 dark:border-gray-700 will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade">
                <p class="text-sm leading-[19px] font-semibold">
                  Location
                </p>
                <div class="max-h-[150px] md:max-h-[500px] overflow-auto mt-3 overflow-x-hidden">
                  <div class="flex flex-col gap-2.5">
                    <p class="text-sm leading-[19px] font-semibold">
                      Proximity Search (Miles)
                    </p>
                    <MilePropertySearchInput :defaultValue="form.proximity_object"
                      @update:selected="form.proximity_object = $event"></MilePropertySearchInput>

                    <SelectBox v-model="form.distance_max" :options="filterOptionValues.mailRanges"
                      placeholder="Select a mail range" aria-label="Mail range" />

                  </div>
                  <Separator class="my-2" />
                  <div class="flex flex-col mt-3">
                    <Label class="text-sm font-semibold mb-1">
                      Counties
                    </Label>
                    <Combobox class="text-xs" :tag-font-size="!isMobile ? '13px' : null" v-model="form.county"
                      :optionValues="countiesList" />
                    {{ errors.counties }}
                  </div>
                  <div class="flex flex-col mt-3">
                    <Label class="text-sm font-semibold mb-1">
                      City
                    </Label>
                    <Input v-model="form.city_name_keyword" placeholder="Enter city name" />
                  </div>

                  <div class="flex flex-col mt-3">
                    <Label class="text-sm font-semibold mb-1">
                      ZipCode
                    </Label>
                    <Input v-model="form.zip" placeholder="Enter zip code" />
                  </div>
                </div>
                <PopupApplyFilterButton :on-clear="clearLocation" :on-apply="applyFilter" />
                <PopoverClose class="h-[35px] w-[35px] inline-flex items-center justify-center text-grass11 absolute top-[8px] right-[8px]
               hover:bg-green4 outline-none border-0 cursor-default p-0" aria-label="Close">
                  <Icon icon="tabler:square-rounded-x" class="closeIcon" />
                </PopoverClose>
                <PopoverArrow class="fill-white stroke-gray-200 dark:fill-gray-900 dark:stroke-gray-900" />
              </PopoverContent>
            </PopoverPortal>
          </PopoverRoot>

          <!---------------- [GOLD FILTER] ------------------------------------------------------------------------------------>
          <PopoverRoot class="z-20" :open="filterPopoverOpen.gold" @update:open="filterPopoverOpen.gold = $event">
            <PopoverTrigger as-child
              class="rounded-xl md:inline-flex items-center justify-center text-grass11 bg-white border hover:bg-stone-50 cursor-default focus:shadow-[0_0_0_2px]
           focus:shadow-black dark:focus:shadow-green8 focus:outline-none dark:bg-gray-900 border-gray-300 shadow-sm dark:border-gray-700"
              aria-label="Update dimensions">
              <Button variant="filterPopover" class="relative">Gold Filter
                <Icon v-show="isGoldValuesExists(form)" icon="tabler:alert-square-rounded-filled"
                  class="text-primary absolute top-[-5px] right-[-5px]" />
              </Button>
            </PopoverTrigger>
            <PopoverPortal>
              <PopoverContent side="bottom" :side-offset="5"
                class="z-10 rounded-lg p-5 w-[calc(100vw-20px)] md:w-[380px] bg-white dark:bg-gray-900 shadow-2xl border border-gray-300 dark:border-gray-7002xl border border-gray-300 dark:border-gray-700 will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade">
                <p class="text-mauve12 text-sm leading-[19px] font-semibold mb-2.5">
                  Gold Filters
                </p>
                <div class="max-h-[150px] md:max-h-[500px] overflow-auto mt-3 overflow-x-hidden">
                  <div class="flex flex-row justify-between gap-2.5">
                    <Button class="text-xs"
                      :variant="(form.gold_filter_type == GoldFilterTypes.FixAndFlip) ? 'default' : 'secondary'"
                      @click="onGoldFilterTypeClick(GoldFilterTypes.FixAndFlip)">Fix & Flip
                    </Button>
                    <Button class="text-xs"
                      :variant="(form.gold_filter_type == GoldFilterTypes.BuyAndHold) ? 'default' : 'secondary'"
                      @click="onGoldFilterTypeClick(GoldFilterTypes.BuyAndHold)">Buy & Hold
                    </Button>
                    <Button class="text-xs"
                      :variant="(form.gold_filter_type == GoldFilterTypes.BelowMarket) ? 'default' : 'secondary'"
                      @click="onGoldFilterTypeClick(GoldFilterTypes.BelowMarket)">Below Market
                    </Button>
                  </div>
                  <div class="flex flex-row-reverse mt-2">
                    <Button v-if="showAdvanceGoldFilter" @click="showAdvanceGoldFilter = false" variant="ghost">Hide
                      Advance Filters
                      <Icon icon="tabler:chevron-up"></Icon>
                    </Button>
                    <Button v-if="!showAdvanceGoldFilter" :disabled="user.user_type == UserTypes.FREE"
                      @click="showAdvanceGoldFilter = true" variant="ghost">Show
                      Advance Filters
                      <Icon icon="tabler:lock" class="h-4 w-4 text-muted-foreground"
                        v-if="user.user_type == UserTypes.FREE" />
                      <Icon icon="tabler:chevron-down"></Icon>
                    </Button>
                  </div>
                  <Transition enter-active-class="transition-all duration-300 ease-in-out"
                    enter-from-class="opacity-0 max-h-0" enter-to-class="opacity-100 max-h-[500px]"
                    leave-active-class="transition-all duration-300 ease-in-out"
                    leave-from-class="opacity-100 max-h-[500px]" leave-to-class="opacity-0 max-h-0">
                    <div v-show="showAdvanceGoldFilter" class="pro-filter-container">
                      <div class="grid mt-3">
                        <Label>Estimated Flip Profit</Label>
                        <div class="flex gap-2 gap-x-6">
                          <div class="">
                            <InputNumber step="100" v-model="form.est_profit_min" />
                          </div>
                          <div class="">
                            <InputNumber step="100" v-model="form.est_profit_max" />
                          </div>
                        </div>
                      </div>

                      <div class="grid  mt-3">
                        <Label>Estimated Cash Flow</Label>
                        <div class="flex gap-2 gap-x-6">
                          <div class="">
                            <InputNumber step="50" v-model="form.est_cashflow_min" />
                          </div>
                          <div class="">
                            <InputNumber step="50" v-model="form.est_cashflow_max" />
                          </div>
                        </div>
                      </div>

                      <div class="grid  mt-3">
                        <Label>Delta PSF</Label>
                        <div class="flex gap-2 gap-x-6">
                          <div class="">
                            <InputNumber step="5" v-model="form.delta_min" />
                          </div>
                          <div class="">
                            <InputNumber step="5" v-model="form.delta_max" />
                          </div>
                        </div>
                      </div>
                      <Separator class="mt-3" />
                      <div class="grid  mt-3">
                        <p class="text-mauve12 text-sm leading-[19px] font-semibold mb-2.5">
                          Accuracy Rating
                        </p>
                        <div class="flex gap-2 gap-x-6  mt-1">
                          <div class="">
                            <Label>Value(Min %)</Label>
                            <InputNumber step="10" v-model="form.accuracy_score_value" />
                          </div>
                          <div class="">
                            <Label>Rent(Min %)</Label>
                            <InputNumber step="10" v-model="form.accuracy_score_rent" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </Transition>

                </div>


                <PopupApplyFilterButton :on-clear="clearGold" :on-apply="applyFilter" />
                <PopoverClose class="h-[35px] w-[35px] inline-flex items-center justify-center text-grass11 absolute top-[8px] right-[8px]
               hover:bg-green4 outline-none border-0 cursor-default p-0" aria-label="Close">
                  <Icon icon="tabler:square-rounded-x" class="closeIcon" />
                </PopoverClose>
                <PopoverArrow class="fill-white stroke-gray-200 dark:fill-gray-900 dark:stroke-gray-900" />
              </PopoverContent>
            </PopoverPortal>
          </PopoverRoot>

          <!---------------- [DOM] ------------------------------------------------------------------------------------>
          <PopoverRoot class="z-20" :open="filterPopoverOpen.dom" @update:open="filterPopoverOpen.dom = $event">
            <PopoverTrigger as-child
              class="rounded-xl md:inline-flex items-center justify-center text-grass11 bg-white border hover:bg-stone-50 cursor-default focus:shadow-[0_0_0_2px]
           focus:shadow-black dark:focus:shadow-green8 focus:outline-none dark:bg-gray-900 border-gray-300 shadow-sm dark:border-gray-700"
              aria-label="Update dimensions">
              <Button variant="filterPopover" class="relative">DOM
                <Icon v-show="isDomValuesExists(form)" icon="tabler:alert-square-rounded-filled"
                  class="text-primary absolute top-[-5px] right-[-5px]" />
              </Button>
            </PopoverTrigger>
            <PopoverPortal>
              <PopoverContent side="bottom" :side-offset="5"
                class="z-10  rounded-lg p-5 w-[calc(100vw-20px)] md:w-[380px] bg-white dark:bg-gray-900 shadow-2xl border border-gray-300 dark:border-gray-700 will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade">
                <p class="text-mauve12 text-sm leading-[19px] font-semibold">
                  Days on Market
                </p>
                <div class="max-h-[150px] md:max-h-[500px] overflow-auto mt-3">
                  <div class="flex flex-col gap-2.5">
                    <div class="flex gap-2 gap-x-6">
                      <div class="">
                        <Label>DOM Min</Label>
                        <InputNumber v-model="form.dom_min" />
                      </div>
                      <div class="">
                        <Label>DOM Max</Label>
                        <InputNumber v-model="form.dom_max" />
                      </div>
                    </div>
                    <Separator />
                    <p class="text-sm leading-[19px] font-semibold">
                      Closed Date Filter
                    </p>
                    <div class="flex gap-2">
                      <div class="w-full">
                        <Label>Earliest Close Date</Label>
                        <Input class="text-xs" v-model="form.closed_date_min" type="date" />
                      </div>
                      <div class="w-full">
                        <Label>Latest Close Date</Label>
                        <Input class="text-xs" v-model="form.closed_date_max" type="date" />
                      </div>
                    </div>
                    <p class="text-muted-foreground text-xs">Change Status filter to "Closed" to use
                      the
                      Closed Date
                      Filter</p>
                  </div>
                </div>

                <PopupApplyFilterButton :on-clear="clearDom" :on-apply="applyFilter" />
                <PopoverClose class="h-[35px] w-[35px] inline-flex items-center justify-center text-grass11 absolute top-[8px] right-[8px]
               hover:bg-green4 outline-none border-0 cursor-default p-0" aria-label="Close">
                  <Icon icon="tabler:square-rounded-x" class="closeIcon" />
                </PopoverClose>
                <PopoverArrow class="fill-white stroke-gray-200 dark:fill-gray-900 dark:stroke-gray-900" />
              </PopoverContent>
            </PopoverPortal>
          </PopoverRoot>

          <!---------------- [MORE] ------------------------------------------------------------------------------------>
          <PopoverRoot class="z-20" :open="filterPopoverOpen.more" @update:open="filterPopoverOpen.more = $event">
            <PopoverTrigger as-child
              class="rounded-xl md:inline-flex items-center justify-center text-grass11 bg-white border hover:bg-stone-50 cursor-default focus:shadow-[0_0_0_2px]
           focus:shadow-black dark:focus:shadow-green8 focus:outline-none dark:bg-gray-900 border-gray-300 shadow-sm dark:border-gray-700"
              aria-label="Update dimensions">
              <Button variant="filterPopover" class="relative">More
                <Icon v-show="isMoreValuesExists(form)" icon="tabler:alert-square-rounded-filled"
                  class="text-primary absolute top-[-5px] right-[-5px]" />
              </Button>
            </PopoverTrigger>
            <PopoverPortal>
              <PopoverContent side="bottom" :side-offset="5"
                class="z-10 rounded-lg p-5 w-[calc(100vw-20px)] md:w-[350px] bg-white dark:bg-gray-900 shadow-2xl border border-gray-300 dark:border-gray-700 will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade">
                <p class="text-sm leading-[19px] font-semibold mb-2.5">
                  Additional Filters
                </p>
                <div class="max-h-[150px] md:min-h-[400px] md:max-h-[500px] overflow-auto">
                  <div class="flex flex-col gap-2.5">
                    <div class="flex gap-2 gap-x-6">
                      <div class="">
                        <Label>List Price Min</Label>
                        <InputNumber v-model="form.list_price_min" />
                      </div>
                      <div class="">
                        <Label>List Price Max</Label>
                        <InputNumber v-model="form.list_price_max" />
                      </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 gap-x-6">
                      <Label>Property Type</Label>
                      <Combobox :optionValues="propertyTypeList" :tag-font-size="!isMobile ? '13px' : null"
                        v-model="form.structure_type" />
                    </div>
                    <div class="flex gap-2 gap-x-6">
                      <Label>
                        <Checkbox v-model="form.fulladdress_avoid" />
                        Remove Listings With Unit Numbers
                      </Label>

                    </div>
                    <div class="flex gap-2 gap-x-6">
                      <div class="">
                        <Label>Square Feet</Label>
                        <InputNumber v-model="form.total_finished_sqft_min" />
                      </div>
                      <div class="">
                        <Label>&nbsp;</Label>
                        <InputNumber v-model="form.total_finished_sqft_max" />
                      </div>
                    </div>
                    <div class="flex gap-2 gap-x-6">
                      <div class="">
                        <Label>Lot Size</Label>
                        <InputNumber v-model="form.lot_sqf_min" />
                      </div>
                      <div class="">
                        <Label>&nbsp;</Label>
                        <InputNumber v-model="form.lot_sqf_max" />
                      </div>
                    </div>
                    <div class="flex gap-2 gap-x-6">
                      <div class="">
                        <Label>Year Built</Label>
                        <InputNumber :min="1800" :max="new Date().getFullYear()" v-model="form.year_build_min" />
                      </div>
                      <div class="">
                        <Label>&nbsp;</Label>
                        <InputNumber :min="1800" :max="new Date().getFullYear()" v-model="form.year_build_max" />
                      </div>
                    </div>
                    <div class="flex gap-2 gap-x-6">
                      <div class="">
                        <Label>Bed Rooms</Label>
                        <InputNumber v-model="form.bedrooms_min" />
                      </div>
                      <div class="">
                        <Label>&nbsp;</Label>
                        <InputNumber v-model="form.bedrooms_max" />
                      </div>
                    </div>
                    <div class="grid gap-2 gap-x-6">
                      <Label>Remark Keyword</Label>
                      <Input v-model="form.remarks_public_keywords" />
                    </div>
                    <div class="grid gap-2 gap-x-6">
                      <Label>Listing Agent</Label>
                      <ListingAgentSearchInput :defaultValue="form.list_agent_keyword"
                        v-model="form.list_agent_keyword" />
                    </div>

                    <div class="grid gap-2 gap-x-6">
                      <Label>School District Agent</Label>
                      <ComboboxServer v-model="form.school_district"
                        :route="route('get.data.combobox_school_districts')" :tag-font-size="!isMobile ? '13px' : null">
                      </ComboboxServer>
                    </div>

                    <div class="grid">
                      <Label>Rent/month Estimate</Label>
                      <div class="flex gap-2 gap-x-6">
                        <div class="">
                          <InputNumber v-model="form.medianrent_min" />
                        </div>
                        <div class="">
                          <InputNumber v-model="form.medianrent_min" />
                        </div>
                      </div>
                    </div>

                    <div class="flex gap-2 gap-x-6">
                      <div class="">
                        <Label>ARV Estimate</Label>
                        <InputNumber v-model="form.est_arv_min" />
                      </div>
                      <div class="">
                        <Label>&nbsp;</Label>
                        <InputNumber v-model="form.est_arv_max" />
                      </div>
                    </div>

                  </div>
                </div>
                <PopupApplyFilterButton :on-clear="clearMore" :on-apply="applyFilter" />
                <PopoverClose class="h-[35px] w-[35px] inline-flex items-center justify-center text-grass11 absolute top-[8px] right-[8px]
               hover:bg-green4 outline-none border-0 cursor-default p-0" aria-label="Close">
                  <Icon icon="tabler:square-rounded-x" class="closeIcon" />
                </PopoverClose>
                <PopoverArrow class="fill-white stroke-gray-200 dark:fill-gray-900 dark:stroke-gray-900" />
              </PopoverContent>
            </PopoverPortal>
          </PopoverRoot>
          <div class="flex flex-row py-2 md:py-0">
            <div v-if="user.user_type == UserTypes.FREE"
              class="order-1 md:order-1 inline-flex w-full md:min-w-[160px] items-center justify-between rounded-l-lg px-[15px] text-sm leading-none h-[38px] gap-[5px] bg-white dark:bg-gray-900 text-grass11 hover:bg-stone-50 border border-gray-300 dark:border-gray-700 shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-black data-[placeholder]:text-green9 outline-none cursor-not-allowed">
              <Icon icon="tabler:lock" class="h-4 w-4 text-muted-foreground" />
            </div>

            <SelectRoot :key="savedSelectBoxKey" v-model="selectedSavedSearchList"
              v-if="user.user_type != UserTypes.FREE">
              <SelectTrigger
                class="order-1 md:order-1 inline-flex w-full md:min-w-[160px] items-center justify-between rounded-l-lg px-[15px] text-sm leading-none h-[38px] gap-[5px] bg-white dark:bg-gray-900 text-grass11 hover:bg-stone-50 border border-gray-300 dark:border-gray-700 shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-black data-[placeholder]:text-green9 outline-none cursor-pointer"
                aria-label="Customise options">
                <SelectValue placeholder="Select a saved search" />
                <Icon icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
              </SelectTrigger>
              <SelectPortal>
                <SelectContent
                  class="min-w-[160px] bg-white dark:bg-gray-900 rounded-lg border shadow-sm will-change-[opacity,transform] data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100]"
                  :side-offset="5">
                  <SelectScrollUpButton
                    class="flex items-center justify-center h-[25px] bg-white dark:bg-gray-900 text-violet11 cursor-default">
                    <Icon icon="radix-icons:chevron-up" />
                  </SelectScrollUpButton>

                  <SelectViewport class="p-[5px]">
                    <SelectGroup>
                      <SelectItem v-for="(option, index) in savedSearchList" :key="index"
                        class="text-sm leading-none text-grass11 rounded-[3px] flex items-center h-[25px] pr-[35px] pl-[25px] relative select-none data-[disabled]:text-mauve8 data-[disabled]:pointer-events-none data-[highlighted]:outline-none data-[highlighted]:bg-primary data-[highlighted]:text-green1"
                        :value="option">
                        <SelectItemIndicator class="absolute left-0 w-[25px] inline-flex items-center justify-center">
                          <Icon icon="radix-icons:check" />
                        </SelectItemIndicator>
                        <SelectItemText>
                          {{ (!option.id) ? '-- None --' : option.saved_search_name }}
                        </SelectItemText>
                      </SelectItem>
                    </SelectGroup>
                    <!--            <SelectSeparator class="h-[1px] bg-green6 m-[5px]"/>-->

                  </SelectViewport>

                  <SelectScrollDownButton
                    class="flex items-center justify-center h-[25px] bg-white dark:bg-gray-900 text-violet11 cursor-default">
                    <Icon icon="radix-icons:chevron-down" />
                  </SelectScrollDownButton>
                </SelectContent>
              </SelectPortal>
            </SelectRoot>

            <div v-if="user.user_type == UserTypes.FREE"
              class="w-fit h-[38px] order-2 md:order-2 cursor-not-allowed flex items-center justify-center">
              <SidebarMenuButton disabled
                class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground rounded-none rounded-r-lg border border-input border-l-0 shadow-sm relative h-[38px]">
                <Icon icon="tabler:device-floppy" class="closeIcon" />
                <Icon icon="tabler:lock" class="h-3.5 w-5 absolute top-0 right-0" />
              </SidebarMenuButton>
            </div>

            <DropdownMenu v-if="user.user_type != UserTypes.FREE">
              <DropdownMenuTrigger as-child class="w-fit h-[38px] order-2 md:order-2">
                <SidebarMenuButton
                  class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground rounded-none rounded-r-lg border border-input border-l-0 shadow-sm relative">
                  <Icon icon="tabler:device-floppy" class="closeIcon" />
                  <ChevronsUpDown class="ml-auto size-4" /> &nbsp;
                  <Icon v-show="selectedSavedSearchList" icon="tabler:alert-square-rounded-filled"
                    class="text-primary absolute top-[0px] left-[0px]" />
                </SidebarMenuButton>
              </DropdownMenuTrigger>
              <DropdownMenuContent class="w-(--reka-dropdown-menu-trigger-width) rounded-lg" :side="'bottom'"
                align="end" :side-offset="4">
                <DropdownMenuGroup>
                  <DropdownMenuItem :as-child="true" v-if="selectedSavedSearchList" @click="clearSavedSearch()">
                    <Label>
                      <Icon icon="tabler:deselect" class="iconAttr"></Icon>
                      Clear
                    </Label>
                  </DropdownMenuItem>
                  <DropdownMenuItem :as-child="true" @click="onSaveSearchPrompt">
                    <Label>
                      <Icon icon="tabler:device-floppy" class="iconAttr"></Icon>
                      Save
                    </Label>
                  </DropdownMenuItem>

                  <DropdownMenuItem :disabled="defaultSaveSearchId == selectedSavedSearchList.id" :as-child="true"
                    v-if="selectedSavedSearchList" @click="onSaveSearchSetAsDefault">
                    <Label>
                      <Icon icon="tabler:filter-check" class="iconAttr"></Icon>
                      Set Default
                    </Label>
                  </DropdownMenuItem>

                  <DropdownMenuItem :as-child="true" v-if="selectedSavedSearchList"
                    @click="handleConfirmation('Do you want to remove ' + selectedSavedSearchList.name + ' from your saved list? ', 'SavedSearch')">
                    <Label>
                      <Icon icon="tabler:trash" class="iconAttr"></Icon>
                      Remove
                    </Label>
                  </DropdownMenuItem>
                </DropdownMenuGroup>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
      </Transition>
    </div>
    <div class="gap-1 hidden md:flex">
      <DropdownMenu>
        <DropdownMenuTrigger as-child class="w-full md:w-fit h-[38px] ">
          <SidebarMenuButton
            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
            <Icon icon="tabler:list-details" class="iconAttr"></Icon>
            <ChevronsUpDown class="ml-auto size-4" />
          </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-[100px] rounded-lg" :side="'bottom'" align="end" :side-offset="4">
          <DropdownMenuGroup>
            <DropdownMenuItem :as-child="true">
              <Label :class="PropertyViewTypes.Map == propertyViewType ? `bg-primary text-black` : ``"
                @click="onViewTypeChange(PropertyViewTypes.Map)">
                <Icon icon="tabler:map-2"
                  :class="`iconAttr ` + (PropertyViewTypes.Map == propertyViewType ? `text-black` : ``)"></Icon>
                Map
              </Label>
            </DropdownMenuItem>
            <DropdownMenuItem :as-child="true">
              <Label :class="PropertyViewTypes.Grid == propertyViewType ? `bg-primary text-black` : ``"
                @click="onViewTypeChange(PropertyViewTypes.Grid)">
                <Icon icon="tabler:layout-2"
                  :class="`iconAttr ` + (PropertyViewTypes.Grid == propertyViewType ? `text-black` : ``)"></Icon>
                Grid
              </Label>
            </DropdownMenuItem>
            <DropdownMenuItem :as-child="true">
              <Label :class="PropertyViewTypes.List == propertyViewType ? `bg-primary text-black` : ``"
                @click="onViewTypeChange(PropertyViewTypes.List)">
                <Icon icon="tabler:columns"
                  :class="`iconAttr ` + (PropertyViewTypes.List == propertyViewType ? `text-black` : ``)"></Icon>
                List
              </Label>
            </DropdownMenuItem>
          </DropdownMenuGroup>
        </DropdownMenuContent>
      </DropdownMenu>

      <DropdownMenu>
        <DropdownMenuTrigger as-child class="w-full md:w-fit h-[38px] ">
          <SidebarMenuButton :class="selectedMyPropertyList ? `bg-primary` : ``"
            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground relative">
            <Icon icon="tabler:heart-code" class="iconAttr"></Icon>
            <ChevronsUpDown class="ml-auto size-4" />
            <Icon v-if="selectedMyPropertyList" icon="tabler:alert-square-rounded-filled"
              class="text-primary absolute top-0 right-0" />
          </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="max-w-[300px] rounded-lg" :side="'bottom'" align="end" :side-offset="4">

          <DropdownMenuGroup v-if="selectedMyPropertyList">
            <DropdownMenuItem :as-child="true" @click="onMyListClicked(null)">
              <Label>
                <Icon icon="tabler:xbox-x" class="size-4" />
                Clear list
              </Label>
            </DropdownMenuItem>
          </DropdownMenuGroup>
          <DropdownMenuSeparator v-if="selectedMyPropertyList" />
          <DropdownMenuGroup>
            <DropdownMenuItem v-for="ml in myPropertyList" :key="ml.id" :as-child="true" @click="onMyListClicked(ml)"
              :class="ml == selectedMyPropertyList ? ` bg-primary text-black ` : ``">
              <Label>
                {{ ml.name }} <span
                  :class="ml == selectedMyPropertyList ? ` text-gray-600 ` : `text-muted-foreground`">({{
                    ml.property_ids.length
                  }})</span>
              </Label>
            </DropdownMenuItem>
          </DropdownMenuGroup>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </div>
  <ConfirmDialog :dialog="confirmDialog" />
  <Toast />
  <Dialog v-model:open="savedSearchDialogRef.open">
    <DialogContent>
      <DialogHeader class="space-y-3">
        <DialogTitle class="text-center text-2xl">{{ savedSearchDialogRef.title }}</DialogTitle>
      </DialogHeader>
      <div class="w-full p-4">

        <div class="grid mb-2">
          <Label>
            Name
            <Input v-model="savedSearchDialogRef.nameValue" />
          </Label>
        </div>
        <p class="text-xs text-muted-foreground">*{{ savedSearchDialogRef.description }}</p>
        <div v-if="form.hasErrors"
          class="flex items-start gap-3 rounded-2xl border border-red-300 bg-red-50 p-4 text-red-800 dark:bg-red-800 dark:text-red-200 shadow-md">
          <div class="text-sm font-medium">
            <ul class="list-none">
              <li v-for="error in form.errors" :key="error">
                <Icon icon="tabler:circle-dashed-x" class="text-xl inline" />
                {{ error }}
              </li>
            </ul>
          </div>
        </div>

      </div>

      <DialogFooter class="gap-2">
        <DialogClose as-child>
          <Button variant="secondary"> Cancel</Button>
        </DialogClose>
        <Button v-if="!savedSearchDialogRef.isNew"
          :disabled="!savedSearchDialogRef.nameValue || savedSearchDialogRef.loading" @click="onSaveSearchSubmit(true)">
          Create as new
        </Button>
        <Button :disabled="!savedSearchDialogRef.nameValue || savedSearchDialogRef.loading"
          @click="onSaveSearchSubmit(savedSearchDialogRef.isNew)"> {{ savedSearchDialogRef.saveButtonText }}
        </Button>
      </DialogFooter>

    </DialogContent>
  </Dialog>

</template>

<style scoped>
.closeIcon {
  width: 1.5em;
  height: 1.5em;
}

.iconAttr {
  width: 1.7em;
  height: 1.7em;
}
</style>
