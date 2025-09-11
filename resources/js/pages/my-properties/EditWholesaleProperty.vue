<script setup lang="ts">


import { Head, router, useForm } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { BreadcrumbItem } from "@/types";
import { Button } from "@/components/ui/button";

import { computed, onMounted, ref } from "vue";
import { Label } from "@/components/ui/label";
import { Icon } from "@iconify/vue";

import Input from "@/components/ui/input/Input.vue";
import Separator from "@/components/ui/separator/Separator.vue";
import SelectBox from "@/components/ui/select-box/SelectBox.vue";
import {
  objectToSelectArraySame,
  PropertyCoolTypesArray,
  PropertyHeatTypesArray,
  useFilterOptions
} from "@/composables/useFilterOptions";
import { InputNumber } from "@/components/ui/input-number";
import InputError from "@/components/InputError.vue";
import { PropertyStatus } from "@/types/property";
import Combobox from "@/components/ui/combobox/Combobox.vue";
import { SwitchRoot, SwitchThumb } from "reka-ui";
import TextArea from "@/components/ui/textarea/TextArea.vue";
import Toast from "@/components/ui/toast/Toast.vue";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

import { useToast } from "@/composables/useToast";
import DialogFooter from "@/components/ui/dialog/DialogFooter.vue";
import DialogClose from "@/components/ui/dialog/DialogClose.vue";
import DialogDescription from "@/components/ui/dialog/DialogDescription.vue";
import WholesaleImagesEditor from "@/pages/my-properties/subs/WholesaleImagesEditor.vue";
import { useRoute } from "ziggy-js";
import { WholesaleProperty } from "@/types/wholesale";
import { comSepToArray } from "@/lib/propertyUtil";
import { useDateFormat, useNumber } from "@/composables/useFormat";

const route = useRoute();
const { showToast } = useToast()
const { formatPrice, formatNumber } = useNumber()
const { formatDateTime, formatGetDays } = useDateFormat()

const filterOptionValues = useFilterOptions();

const props = defineProps<{
  id: number | string,
  data: WholesaleProperty,
  agreed: boolean,
  success?: string | null,
  error?: string | null
}>();

const successUploadedDialogOpen = ref<boolean>();


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My Properties',
    href: route('my_properties').toString(),
  },
  {
    title: 'Edit Property',
  },
];


const propertyTypeList = ref<string[]>(objectToSelectArraySame(filterOptionValues.structureType));
const propertyStatusTypeList = ref<string[]>(objectToSelectArraySame(filterOptionValues.status));

const propertyHeatTypeList = PropertyHeatTypesArray;
const propertyCoolTypeList = PropertyCoolTypesArray;

const defaultCoolType = comSepToArray(props.data.cool_type);
const defaultHeatType = comSepToArray(props.data.heat_type);
const defaultImages = props.data.images;

const form = useForm({
  wholesaleId: props.data.id, // number always available
  // geoObject: address.value, // ignore
  geoAddress: props.data.geo_address, // nullable
  manualAddress: props.data.manual_address, // boolean required true / false
  fullStreetAddress: props.data.full_street_address, // string required
  city: props.data.city_name, // string required
  state: props.data.state_or_province, // string required
  zip: props.data.zip_code, // string required
  county: props.data.county, // string required
  lat: props.data.lat, // number nullable
  lng: props.data.lng,  // number nullable

  status: props.data.status, // string required
  tin: props.data.tax_id_number, // string nullable
  zoning: props.data.zoning,  // string nullable
  structureType: props.data.structure_type,  // string required
  finishedSqft: props.data.total_finished_sqft, // number required
  lotSize: props.data.lot_sqft, // number required
  bedrooms: props.data.beds,// number required
  bathrooms: props.data.baths,// number required
  yearBuilt: props.data.year_built,// number required
  hoa: props.data.hoa,// boolean nullable
  heatType: defaultHeatType, // string[] nullable
  coolType: defaultCoolType, // string[] nullable
  annualTax: props.data.tax_annual_amount, // number nullable
  taxAssessment: props.data.tax_assessed_value, // number nullable
  listPrice: props.data.list_price, // number required
  avm: props.data.seller_avm, // number nullable
  arv: props.data.seller_arv, // number nullable
  avgRent: props.data.seller_avg_rent, // number nullable
  estRentRehab: props.data.seller_est_rental_rehab, // number nullable
  estFlipRehab: props.data.seller_est_flip_rehab, // number nullable
  estCashflow: props.data.seller_est_cashflow, // number nullable
  estFlipProfit: props.data.seller_est_flip_profit, // number nullable
  municipality: props.data.municipality, // string nullable
  schoolDistrict: props.data.school_district, // string nullable
  listingOffice: props.data.listing_office, // string required
  listingAgent: props.data.listing_agent, // string required
  listingAgentEmail: props.data.listing_agent_email, // string required
  remark: props.data.remarks_public, // string required
  images: defaultImages, // string[] nullable
  showAddress: props.data.show_full_address, //  boolean true/false
  closedPrice: props.data.closed_price,// number required if status = 'Closed' else nullable
  closedDate: props.data.closed_date, // date required if status = 'Closed' else nullable
});

onMounted(() => {
  if (!props.agreed) {
    router.visit(route('my_properties'), {
      replace: true,
      preserveScroll: false,
    });
  }
})

form.geoAddress = computed(() => {
  return form.fullStreetAddress + ', ' + form.city + ", " + form.state + " " + form.zip
});

const onSubmit = async () => {
  form.post(route('my_properties.edit_property_submit'), {
    preserveScroll: true,
    onSuccess: () => {
      successUploadedDialogOpen.value = true;
    },
    onError: (errors: any) => {
      showToast('Form Validation', 'There are some invalid inputs. Please check and resolve them.', 'error')
      console.error('Validation Errors:', errors)
    }
  });
}

const detailContainer = ref<HTMLInputElement | null>(null);
const onScrollToDetail = () => {
  detailContainer.value?.scrollIntoView({
    behavior: 'smooth', // for a nice animated scroll
    block: 'start',     // aligns the top of the element to the top of the scroll area
  });
}

const backToList = () => {
  router.visit(route('my_properties'), {
    replace: true,
    preserveScroll: false,
  })
}

</script>

<template>
  <Toast />
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="Edit Property - My Properties" />
    <div
      class="flex flex-col items-center md:items-start md:flex-row md:justify-center px-4 py-2 h-[calc(100dvh-65px)] overflow-auto gap-4 relative">

      <div class="flex-1 flex flex-col max-w-lg">
        <WholesaleImagesEditor :id="props.data.id" v-model:bindImages="form.images" />
      </div>
      <div ref="detailContainer" class="flex-1 flex flex-col w-full max-w-lg">
        <div class="">
          <div class="mt-2">
            <label class="text-3xl font-semibold">
              {{ formatPrice(form.listPrice) }}
            </label>
            <Label class="text-lg">
              <Icon icon="tabler:map-pin" class="size-5 text-primary-strong" />
              {{ form.geoAddress }}
            </Label>
          </div>
          <div class="grid grid-cols-1 gap-2 mt-3 mb-3">
            <Label> <span>Public Address Visibility&nbsp;<span class="text-muted-foreground text-xs">({{
              form.showAddress ?
                'on' : 'off' }})</span>
                <br>
              </span></Label>
            <div class="flex items-center gap-2">
              <div class="flex rounded-xl overflow-hidden shadow-sm border mx-auto w-fit">
                <!-- Address Visible Button -->
                <button type="button"
                  class="flex items-center gap-2 px-5 py-3 font-semibold text-base transition-colors duration-150 cursor-pointer focus:outline-none"
                  :class="form.showAddress
                    ? 'bg-primary text-black'
                    : 'bg-transparent hover:bg-primary/80'" @click="form.showAddress = true">
                  <Icon icon="tabler:map-pin" class="size-7" />
                  <span class="flex flex-col items-start leading-tight">
                    <span>Address visible</span>
                  </span>
                </button>
                <!-- Address Hidden Button -->
                <button type="button"
                  class="flex items-center gap-2 px-5 py-3 font-semibold text-base transition-colors duration-150 cursor-pointer focus:outline-none"
                  :class="!form.showAddress
                    ? 'bg-primary text-black'
                    : 'bg-transparent hover:bg-primary/80'" @click="form.showAddress = false">
                     <Icon icon="tabler:map-pin-off" class="size-7" />
                  <span class="flex flex-col items-start leading-tight">
                    <span>Address hidden</span>
                  </span>
                </button>
              </div>
            </div>

          </div>
          <Separator class="my-2" />
          <div class="grid grid-cols-1 gap-2 mt-3">
            <div class="text-lg font-semibold my-2">Property Description</div>
            <Label :line-break="true">
              <TextArea :aria-invalid="(form.errors.remark != null)" v-model="form.remark" :rows="4" />
              <InputError :message="form.errors.remark" />
            </Label>
          </div>
          <div class="grid grid-cols-1 xl:grid-cols-2 gap-2 mt-5">
            <div class="text-lg font-semibold my-2 col-span-1 xl:col-span-2">Wholesale Details</div>
            <Label :line-break="true">
              <span>List Price</span>
              <InputNumber :aria-invalid="(form.errors.listPrice != null)" v-model="form.listPrice" type="price" />
              <InputError :message="form.errors.listPrice" />
            </Label>
            <Label :line-break="true">
              <span>Listing Office</span>
              <Input :aria-invalid="(form.errors.listingOffice != null)" v-model="form.listingOffice" />
              <InputError :message="form.errors.listingOffice" />
            </Label>
            <Label :line-break="true">
              <span>Listing Agent</span>
              <Input :aria-invalid="(form.errors.listingAgent != null)" v-model="form.listingAgent" />
              <InputError :message="form.errors.listingAgent" />
            </Label>
            <Label :line-break="true">
              <span>Listing Agent Email</span>
              <Input :disabled="true" :aria-invalid="(form.errors.listingAgentEmail != null)"
                v-model="form.listingAgentEmail" />
              <InputError :message="form.errors.listingAgentEmail" />
            </Label>
            <Label :line-break="true">
              <span>AVM (Median Value Estimate)</span>
              <InputNumber :aria-invalid="(form.errors.avm != null)" v-model="form.avm" type="price" />
              <InputError :message="form.errors.avm" />
            </Label>
            <Label :line-break="true">
              <span>ARV (After-Repair Value Estimate)</span>
              <InputNumber :aria-invalid="(form.errors.arv != null)" v-model="form.arv" type="price" />
              <InputError :message="form.errors.arv" />
            </Label>
            <Label :line-break="true">
              <span>Average Rent</span>
              <InputNumber :aria-invalid="(form.errors.avgRent != null)" v-model="form.avgRent" type="price" />
              <InputError :message="form.errors.avgRent" />
            </Label>
            <Label :line-break="true">
              <span>Estimate Rental Rehab</span>
              <InputNumber :aria-invalid="(form.errors.estRentRehab != null)" v-model="form.estRentRehab"
                type="price" />
              <InputError :message="form.errors.estRentRehab" />
            </Label>
            <Label :line-break="true">
              <span>Estimate Flip Rehab</span>
              <InputNumber :aria-invalid="(form.errors.estFlipRehab != null)" v-model="form.estFlipRehab"
                type="price" />
              <InputError :message="form.errors.estFlipRehab" />
            </Label>
            <Label :line-break="true">
              <span>Estimate Cashflow</span>
              <InputNumber :aria-invalid="(form.errors.estCashflow != null)" v-model="form.estCashflow" type="price" />
              <InputError :message="form.errors.estCashflow" />
            </Label>
            <Label :line-break="true">
              <span>Estimate Flip Profit</span>
              <InputNumber :aria-invalid="(form.errors.estFlipProfit != null)" v-model="form.estFlipProfit"
                type="price" />
              <InputError :message="form.errors.estFlipProfit" />
            </Label>
          </div>

          <div class="grid grid-cols-1 xl:grid-cols-2 gap-2 mt-5">
            <div class="text-lg font-semibold my-2 col-span-1 xl:col-span-2">Property Details</div>

            <Label :line-break="true">
              <span>Status</span>
              <SelectBox :not-allow-empty-in-list="true" :aria-invalid="(form.errors.status != null)"
                :options="propertyStatusTypeList" v-model="form.status" />
              <InputError :message="form.errors.status" />
            </Label>
            <Label :line-break="true">
              <span>Structure Type</span>
              <SelectBox :not-allow-empty-in-list="true" :aria-invalid="(form.errors.structureType != null)"
                :options="propertyTypeList" v-model="form.structureType" />
              <InputError :message="form.errors.structureType" />
            </Label>
            <Label :line-break="true" v-if="form.status == PropertyStatus.Closed">
              <span>Closed Price</span>
              <InputNumber :aria-invalid="(form.errors.closedPrice != null)" v-model="form.closedPrice" type="price" />
              <InputError :message="form.errors.closedPrice" />
            </Label>

            <Label :line-break="true" v-if="form.status == PropertyStatus.Closed">
              <span>Closed Date</span>
              <Input type="date" class="w-full custom-date-input" :aria-invalid="(form.errors.closedDate != null)"
                v-model="form.closedDate" />
              <InputError :message="form.errors.closedDate" />
            </Label>

            <Label :line-break="true">
              <span>Tax ID Number(APN)</span>
              <Input :aria-invalid="(form.errors.tin != null)" v-model="form.tin" />
              <InputError :message="form.errors.tin" />
            </Label>
            <Label :line-break="true">
              <span>Zoning</span>
              <Input :aria-invalid="(form.errors.zoning != null)" v-model="form.zoning" />
              <InputError :message="form.errors.zoning" />
            </Label>


            <Label :line-break="true">
              <span>Finished Sqft</span>
              <InputNumber :aria-invalid="(form.errors.finishedSqft != null)" v-model="form.finishedSqft" :step="10" />
              <InputError :message="form.errors.finishedSqft" />
            </Label>
            <Label :line-break="true">
              <span>Lot Size (sqft)</span>
              <InputNumber :aria-invalid="(form.errors.lotSize != null)" v-model="form.lotSize" :step="10" />
              <InputError :message="form.errors.lotSize" />
            </Label>
            <Label :line-break="true">
              <span>Bedrooms Count</span>
              <InputNumber :aria-invalid="(form.errors.bedrooms != null)" v-model="form.bedrooms" />
              <InputError :message="form.errors.bedrooms" />
            </Label>
            <Label :line-break="true">
              <span>Bathrooms Total Count</span>
              <InputNumber :aria-invalid="(form.errors.bathrooms != null)" v-model="form.bathrooms" :step="0.5" />
              <InputError :message="form.errors.bathrooms" />
            </Label>
            <Label :line-break="true">
              <span>Year Built</span>
              <Input :aria-invalid="(form.errors.yearBuilt != null)" type="number" v-model="form.yearBuilt" min="1900"
                max="2100" />
              <InputError :message="form.errors.yearBuilt" />
            </Label>

            <Label :line-break="true" class="h-[60px]">
              <div>HOA&nbsp;<span class="text-muted-foreground text-xs">({{ form.hoa ? 'on' : 'off' }})</span></div>
              <SwitchRoot id="airplane-mode" v-model="form.hoa"
                class="w-[32px] h-[20px] shadow-sm flex data-[state=unchecked]:bg-stone-300 data-[state=checked]:bg-stone-800 dark:data-[state=unchecked]:bg-stone-800 dark:data-[state=checked]:bg-stone-700 border border-stone-300 data-[state=checked]:border-stone-700  dark:border-stone-700 rounded-full relative transition-[background] focus-within:outline-none focus-within:shadow-[0_0_0_1px] focus-within:border-stone-800 focus-within:shadow-stone-800">
                <SwitchThumb
                  class="w-3.5 h-3.5 my-auto bg-white text-xs flex items-center justify-center shadow-xl rounded-full transition-transform translate-x-0.5 will-change-transform data-[state=checked]:translate-x-full" />
              </SwitchRoot>

              <InputError :message="form.errors.hoa" />
            </Label>
            <Label class="col-span-1 xl:col-span-2" :line-break="true">
              <span>Heat Type</span>
              <Combobox :tag-font-size="13" :option-values="propertyHeatTypeList" v-model="form.heatType"
                :aria-invalid="(form.errors.heatType != null)" />
              <InputError :message="form.errors.heatType" />
            </Label>
            <Label class="col-span-1 xl:col-span-2" :line-break="true">
              <span>Cool Type</span>
              <Combobox :tag-font-size="13" :option-values="propertyCoolTypeList" v-model="form.coolType"
                :aria-invalid="(form.errors.coolType != null)" />
              <InputError :message="form.errors.coolType" />
            </Label>
            <Label :line-break="true">
              <span>Full Street Address</span>
              <Input :aria-invalid="(form.errors.fullStreetAddress != null)" v-model="form.fullStreetAddress" />
              <InputError :message="form.errors.fullStreetAddress" />
            </Label>
            <Label :line-break="true">
              <span>City Name</span>
              <Input :aria-invalid="(form.errors.city != null)" v-model="form.city" />
              <InputError :message="form.errors.city" />
            </Label>
            <Label :line-break="true">
              <span>State or Province</span>
              <Input :aria-invalid="(form.errors.state != null)" v-model="form.state" />
              <InputError :message="form.errors.state" />
            </Label>
            <Label :line-break="true">
              <span>Zip Code</span>
              <Input :aria-invalid="(form.errors.zip != null)" v-model="form.zip" />
              <InputError :message="form.errors.zip" />
            </Label>
            <Label :line-break="true">
              <span>County</span>
              <Input :aria-invalid="(form.errors.county != null)" v-model="form.county" />
              <InputError :message="form.errors.county" />
            </Label>
            <Label :line-break="true">
              <span>Latitude</span>
              <Input :aria-invalid="(form.errors.lat != null)" v-model="form.lat" />
              <InputError :message="form.errors.lat" />
            </Label>
            <Label :line-break="true">
              <span>Longitude</span>
              <Input :aria-invalid="(form.errors.lng != null)" v-model="form.lng" />
              <InputError :message="form.errors.lng" />
            </Label>
          </div>
          <div class="grid grid-cols-1 xl:grid-cols-2 gap-2 mt-5">
            <div class="text-lg font-semibold my-2 col-span-1 xl:col-span-2">Tax Details</div>
            <Label :line-break="true">
              <span>Annual Taxes</span>
              <InputNumber :aria-invalid="(form.errors.fullStreetAddress != null)" v-model="form.annualTax"
                type="price" />
              <InputError :message="form.errors.fullStreetAddress" />
            </Label>
            <Label :line-break="true">
              <span>Tax Assessment</span>
              <InputNumber :aria-invalid="(form.errors.taxAssessment != null)" v-model="form.taxAssessment"
                type="price" />
              <InputError :message="form.errors.taxAssessment" />
            </Label>
          </div>
          <div class="grid grid-cols-1 xl:grid-cols-2 gap-2 mt-5">
            <div class="text-lg font-semibold my-2 col-span-1 xl:col-span-2">Municipality & Schools</div>
            <Label :line-break="true">
              <span>Municipality</span>
              <Input :aria-invalid="(form.errors.municipality != null)" v-model="form.municipality" />
              <InputError :message="form.errors.municipality" />
            </Label>
            <Label :line-break="true">
              <span>School District</span>
              <Input :aria-invalid="(form.errors.schoolDistrict != null)" v-model="form.schoolDistrict" />
              <InputError :message="form.errors.schoolDistrict" />
            </Label>
          </div>
          <div class="grid grid-cols-1 xl:grid-cols-2 gap-2 mt-5">
            <div class="text-lg font-semibold my-2 col-span-1 xl:col-span-2 flex gap-2">General Details
              <div class="text-xs bg-red-600 px-2 py-1 text-white rounded-lg my-auto" v-if="!props.data.database_id">
                Sync pending
              </div>
            </div>
            <Label :line-break="true">
              <span>Listing Created</span>
              <Input :disabled="true" :default-value="formatDateTime(props.data.created_at)" />
            </Label>
            <Label :line-break="true">
              <span>Revamp ID</span>
              <Input :disabled="true" :default-value="props.data.database_id" />
            </Label>
            <Label :line-break="true">
              <span>Listing Updated</span>
              <Input :disabled="true" :default-value="formatDateTime(props.data.updated_at)" />
            </Label>
            <Label :line-break="true">
              <span class="text-xs text-muted-foreground">DOM</span>
              <Input :disabled="true" :default-value="formatNumber(formatGetDays(props.data.created_at))" />
            </Label>
          </div>

          <Button variant="default" :disabled="form.processing" class="my-3 w-full" @click="onSubmit()">Update</Button>
        </div>
      </div>

      <div class="fixed bg-input px-3 py-2 md:hidden right-0 rounded-l-lg shadow-md" @click="onScrollToDetail">Go to
        Details
      </div>
    </div>
  </AppLayout>
  <Dialog v-if="successUploadedDialogOpen" v-model:open="successUploadedDialogOpen">
    <DialogContent>
      <DialogHeader class="space-y-3">
        <DialogTitle class="text-center text-2xl">Property Submitted</DialogTitle>
        <DialogDescription>
          <p class="text-base">Your property has been submitted and your property upload is in process
            and should be live
            with 30 minutes.</p>
          <br />
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <Button @click="backToList()" variant="default">Back to List</Button>
        <DialogClose>
          <Button variant="secondary">Close</Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped></style>
