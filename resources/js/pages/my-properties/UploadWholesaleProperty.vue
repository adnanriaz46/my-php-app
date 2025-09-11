<script setup lang="ts">


import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { SharedData } from "@/types";
import { Button } from "@/components/ui/button";
import { onMounted, ref, watch } from "vue";
import { Label } from "@/components/ui/label";
import AddressSearchInput from "@/components/ui/address-search/AddressSearchInput.vue";
import { GeoAddressObject } from "@/lib/zilowAndlocationUtil";
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
import { getDatatreePropertyInfo } from "@/lib/DBApiUtil";
import { DBApi, DBApiDatatreePropertyInfo } from "@/types/DBApi";
import { Icon } from "@iconify/vue";

const { showToast } = useToast()

const filterOptionValues = useFilterOptions();

const page = usePage<SharedData>();
const props = defineProps<{ agreed: boolean, success?: string | null, error?: string | null }>();
const user = page.props.auth.user;

const successUploadedDialogOpen = ref<boolean>();
const recordId = ref<number>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My Properties',
    href: route('my_properties').toString(),
  },
  {
    title: 'Upload Property',
    href: route('my_properties.upload_property').toString(),
  },
];
const address = ref<GeoAddressObject>()

const propertyTypeList = ref<string[]>(objectToSelectArraySame(filterOptionValues.structureType));
const propertyStatusTypeList = ref<string[]>(objectToSelectArraySame(filterOptionValues.status));
const propertyHeatTypeList = PropertyHeatTypesArray;
const propertyCoolTypeList = PropertyCoolTypesArray;

const form = useForm({
  geoObject: address.value, // ignore
  geoAddress: '', // nullable
  manualAddress: false, // boolean required true / false
  fullStreetAddress: '', // string required
  city: '', // string required
  state: '', // string required
  zip: '', // string required
  county: '', // string required
  lat: '', // number nullable
  lng: '',  // number nullable
  tin: '', // string nullable
  zoning: '',  // string nullable
  structureType: '',  // string required
  status: '', // string required
  finishedSqft: '', // number required
  lotSize: '', // number required
  bedrooms: '',// number required
  bathrooms: '',// number required
  yearBuilt: '',// number required
  hoa: false,// boolean nullable
  heatType: [], // string[] nullable
  coolType: [], // string[] nullable
  annualTax: '', // number nullable
  taxAssessment: '', // number nullable
  listPrice: '', // number required
  avm: '', // number nullable
  arv: '', // number nullable
  avgRent: '', // number nullable
  estRentRehab: '', // number nullable
  estFlipRehab: '', // number nullable
  estCashflow: '', // number nullable
  estFlipProfit: '', // number nullable
  municipality: '', // string nullable
  schoolDistrict: '', // string nullable
  listingOffice: '', // string required
  listingAgent: '', // string required
  listingAgentEmail: '', // string required
  remark: '', // string required
});


onMounted(() => {
  if (!props.agreed) {
    router.visit(route('my_properties'), {
      replace: true,
      preserveScroll: false,
    });
  }

  form.status = PropertyStatus.Active;
  form.listingOffice = user.company_name;
  form.listingAgent = user.name;
  form.listingAgentEmail = user.email;
})

const loadingPropertyInfo = ref<boolean>(false);

watch(address, async (newValue) => {
  if (!newValue) {
    return;
  }
  form.geoObject = newValue;
  form.fullStreetAddress = newValue?.street;
  form.city = newValue?.city;
  form.state = newValue?.state;
  form.zip = newValue?.zip;
  form.county = newValue?.county;
  form.geoAddress = newValue?.geoAddress;
  form.lat = newValue?.lat;
  form.lng = newValue?.lng;


  loadingPropertyInfo.value = true;
  const propertyInfo: DBApi<DBApiDatatreePropertyInfo[]> = await getDatatreePropertyInfo({
    full_street_address: newValue?.street,
    city: newValue?.city,
    state: newValue?.state,
    zip: newValue?.zip,
    county: newValue?.county,
  })

  if (propertyInfo.status && propertyInfo.data && propertyInfo.data.length > 0) {
    form.tin = propertyInfo.data[0].TaxAccountNumber;
    form.zoning = propertyInfo.data[0].Zoning;
    form.finishedSqft = propertyInfo.data[0].SumLivingAreaSqFt;
    form.lotSize = propertyInfo.data[0].LotSizeSqFt;
    form.bedrooms = propertyInfo.data[0].Bedrooms;
    form.bathrooms = propertyInfo.data[0].BathTotalCalc;
    form.yearBuilt = propertyInfo.data[0].YearBuilt;
    form.hoa = propertyInfo.data[0].HOA1Name !== '';
    form.annualTax = propertyInfo.data[0].TaxAmt;
    form.taxAssessment = propertyInfo.data[0].AssdTotalValue;
    form.listPrice = propertyInfo.data[0].CurrentAVMValue;
    form.avm = propertyInfo.data[0].CurrentAVMValue;
    form.municipality = propertyInfo.data[0].Municipality;
    form.schoolDistrict = propertyInfo.data[0].SchoolDistrictName;
  }


  loadingPropertyInfo.value = false;
}, { deep: true })

const onSubmit = async () => {
  form.post(route('my_properties.upload_property_submit'), {
    preserveScroll: true,
    onSuccess: () => {
      successUploadedDialogOpen.value = true;
      recordId.value = page.props.recordId;
      form.reset()
    },
    onError: (errors) => {
      showToast('Form Validation', 'There are some invalid inputs. Please check and resolve them.', 'error')
      console.error('Validation Errors:', errors)
    }
  });
}

const onGoToEditPage = () => {
  router.visit(route('my_properties.edit_property', { id: recordId.value }), {
    replace: false,
    preserveScroll: false,
  })
}
</script>

<template>
  <Toast />
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="Upload Property - My Properties" />
    <div class="flex flex-col px-4 py-2 h-[calc(100dvh-65px)] overflow-auto">
      <div class="top-section flex-1 flex flex-col justify-start items-center">
        <div class="w-full max-w-lg">
          <AddressSearchInput @update:address="val => address = val" v-if="!form.manualAddress" />
          <div class="mt-2" v-if="!form.manualAddress">
            <Label :line-break="true">
              <span>Geo Address <span class="text-muted-foreground text-xs">(Read-only)</span></span>
              <Input :aria-invalid="(form.errors.geoAddress != null)" :disabled="true" v-model="form.geoAddress" />
              <InputError :message="form.errors.geoAddress" />
            </Label>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3">
            <Label class="h-[60px]">
              <SwitchRoot id="airplane-mode" v-model="form.manualAddress"
                class="w-[32px] h-[20px] shadow-sm flex data-[state=unchecked]:bg-stone-300 data-[state=checked]:bg-stone-800 dark:data-[state=unchecked]:bg-stone-800 dark:data-[state=checked]:bg-stone-700 border border-stone-300 data-[state=checked]:border-stone-700  dark:border-stone-700 rounded-full relative transition-[background] focus-within:outline-none focus-within:shadow-[0_0_0_1px] focus-within:border-stone-800 focus-within:shadow-stone-800">
                <SwitchThumb
                  class="w-3.5 h-3.5 my-auto bg-white text-xs flex items-center justify-center shadow-xl rounded-full transition-transform translate-x-0.5 will-change-transform data-[state=checked]:translate-x-full" />
              </SwitchRoot>
              <span>Manual Address&nbsp;<span class="text-muted-foreground text-xs">({{ form.manualAddress ? 'on' :
                'off' }})</span></span>
            </Label>
            <Label :line-break="true">
              <span>Full Street Address<span v-if="!form.manualAddress"
                  class="text-muted-foreground text-xs">(Read-only)</span></span>
              <Input :aria-invalid="(form.errors.fullStreetAddress != null)" :disabled="!form.manualAddress"
                v-model="form.fullStreetAddress" />
              <InputError :message="form.errors.fullStreetAddress" />
            </Label>
            <Label :line-break="true">
              <span>City Name<span v-if="!form.manualAddress"
                  class="text-muted-foreground text-xs">(Read-only)</span></span>
              <Input :aria-invalid="(form.errors.city != null)" :disabled="!form.manualAddress" v-model="form.city" />
              <InputError :message="form.errors.city" />
            </Label>
            <Label :line-break="true">
              <span>State or Province<span v-if="!form.manualAddress"
                  class="text-muted-foreground text-xs">(Read-only)</span></span>
              <Input :aria-invalid="(form.errors.state != null)" :disabled="!form.manualAddress" v-model="form.state" />
              <InputError :message="form.errors.state" />
            </Label>
            <Label :line-break="true">
              <span>Zip Code<span v-if="!form.manualAddress"
                  class="text-muted-foreground text-xs">(Read-only)</span></span>
              <Input :aria-invalid="(form.errors.zip != null)" :disabled="!form.manualAddress" v-model="form.zip" />
              <InputError :message="form.errors.zip" />
            </Label>
            <Label :line-break="true">
              <span>County<span v-if="!form.manualAddress"
                  class="text-muted-foreground text-xs">(Read-only)</span></span>
              <Input :aria-invalid="(form.errors.county != null)" :disabled="!form.manualAddress"
                v-model="form.county" />
              <InputError :message="form.errors.county" />
            </Label>
            <Label :line-break="true">
              <span>Latitude<span v-if="!form.manualAddress"
                  class="text-muted-foreground text-xs">(Read-only)</span></span>
              <Input :aria-invalid="(form.errors.lat != null)" :disabled="!form.manualAddress" v-model="form.lat" />
              <InputError :message="form.errors.lat" />
            </Label>
            <Label :line-break="true">
              <span>Longitude<span v-if="!form.manualAddress"
                  class="text-muted-foreground text-xs">(Read-only)</span></span>
              <Input :aria-invalid="(form.errors.lng != null)" :disabled="!form.manualAddress" v-model="form.lng" />
              <InputError :message="form.errors.lng" />
            </Label>
          </div>
          <Separator class="my-2" />

          <div v-if="loadingPropertyInfo" class="grid grid-cols-1 text-center justify-center items-center h-fit">
            <div class="flex flex-col justify-center items-center h-full">
              <Icon icon="tabler:loader-2" class="size-10 animate-spin text-primary" />
              <span class="text-sm">Loading property information...</span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3">
            <Label :line-break="true">
              <span>Tax ID Number</span>
              <Input :aria-invalid="(form.errors.tin != null)" v-model="form.tin" />
              <InputError :message="form.errors.tin" />
            </Label>
            <Label :line-break="true">
              <span>Zoning</span>
              <Input :aria-invalid="(form.errors.zoning != null)" v-model="form.zoning" />
              <InputError :message="form.errors.zoning" />
            </Label>
            <Label :line-break="true">
              <span>Structure Type</span>
              <SelectBox :not-allow-empty-in-list="true" :aria-invalid="(form.errors.structureType != null)"
                :options="propertyTypeList" v-model="form.structureType" />
              <InputError :message="form.errors.structureType" />
            </Label>
            <Label :line-break="true">
              <span>Status</span>
              <SelectBox :not-allow-empty-in-list="true" :aria-invalid="(form.errors.status != null)"
                :options="propertyStatusTypeList" v-model="form.status" />
              <InputError :message="form.errors.status" />
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
            <Label class="col-span-1 md:col-span-2" :line-break="true">
              <span>Heat Type</span>
              <Combobox :tag-font-size="13" :option-values="propertyHeatTypeList" v-model="form.heatType"
                :aria-invalid="(form.errors.heatType != null)" />
              <InputError :message="form.errors.heatType" />
            </Label>
            <Label class="col-span-1 md:col-span-2" :line-break="true">
              <span>Cool Type</span>
              <Combobox :tag-font-size="13" :option-values="propertyCoolTypeList" v-model="form.coolType"
                :aria-invalid="(form.errors.coolType != null)" />
              <InputError :message="form.errors.coolType" />
            </Label>
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
            <Label :line-break="true">
              <span>List Price</span>
              <InputNumber :aria-invalid="(form.errors.listPrice != null)" v-model="form.listPrice" type="price" />
              <InputError :message="form.errors.listPrice" />
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
            <Label class="md:col-span-2" :line-break="true">
              <span>Public Description</span>
              <TextArea :aria-invalid="(form.errors.remark != null)" v-model="form.remark" :rows="4" />
              <InputError :message="form.errors.remark" />
            </Label>
          </div>
          <Button variant="default" :disabled="form.processing" class="my-3 w-full" @click="onSubmit()">Update</Button>
        </div>
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
      <div class="w-full">
        <p class="text-sm">Add more information, images, and other details using the 'Add Additional Info' button.</p>
      </div>
      <DialogFooter>
        <DialogClose>
          <Button variant="secondary">Close</Button>
        </DialogClose>
        <Button @click="onGoToEditPage()">Add Additional Info</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped></style>
