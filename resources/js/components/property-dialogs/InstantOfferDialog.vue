<script setup lang="ts">

import InputError from "@/components/InputError.vue";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter, DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Input} from "@/components/ui/input";
import {Label} from "@/components/ui/label";
import {useForm, usePage} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";
import SelectBox from "@/components/ui/select-box/SelectBox.vue";
import {useToast} from '@/composables/useToast'
import {DBApiPropertyFull} from "@/types/DBApi";
import {User} from "@/types";
import {getMainImage} from "@/types/property";
import TextArea from "@/components/ui/textarea/TextArea.vue";
import InputNumber from "@/components/ui/input-number/InputNumber.vue";

const {showToast} = useToast()

const page = usePage();

watch(() => page.props?.success, (msg) => {
  if (msg) {
    showToast('Instant Offer', msg, 'success')
  }
}, {deep: true});

const props = defineProps<{
  propertyData: DBApiPropertyFull;
  open: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const dialogOpen = ref<boolean>(props.open);


const user = page.props.auth.user as User;
const propertyId = props.propertyData?.id;
const propertyAddress = props.propertyData?.geo_address;
const propertyImage = getMainImage(props.propertyData?.full_location);
const propertyUrl = page.url;

const selectOptions = [
  {key: "Yes", value: "Yes"},
  {key: "No", value: "No"}
];
const form = useForm({
  propertyId: propertyId,
  propertyUrl: propertyUrl,
  propertyAddress: propertyAddress,
  propertyStreet: props.propertyData?.full_street_address, // required
  propertyCity: props.propertyData?.city_name, // required
  propertyState: props.propertyData?.state_or_province, // required
  propertyZip: props.propertyData?.zip_code, // required
  propertyTin: props.propertyData?.tax_id_number, // required
  propertyOffice: props.propertyData?.list_office_name, // required
  userName: user.name,
  userContractualName: null,
  userPhone: user.phone_number,
  userEmail: user.email,
  offerPrice: 0,
  depositAmount: 0,
  preferredClosingDate: null,
  agentInvolved: 'No',
  agentName: '',
  agentEmail: '',
  agentCommission: 0,
  note: '',
});

// Sync child state back to parent
watch(dialogOpen, (newVal) => {
  emit('update:open', newVal);
});

// Sync prop changes to local state
watch(() => props.open, (newVal) => {
  dialogOpen.value = newVal;
});


onMounted(() => {
  fetchInitialData();
})
const fetchInitialData = async () => {

}

const submit = () => {
  form.post(route('property.request.instant_offer'), {
    preserveScroll: true,
    onSuccess: () => {
      dialogOpen.value = false;
      form.reset()
    },
    onError: (errors) => {
      console.error('Validation Errors:', errors)
    }
  })
};
</script>
<template>
  <Dialog v-model:open="dialogOpen">
    <DialogContent>
      <DialogHeader class="space-y-3">
        <DialogTitle>Submit an Offer</DialogTitle>
        <DialogDescription>&nbsp;</DialogDescription>
      </DialogHeader>
      <div class="w-full grid grid-cols-1 justify-center gap-4 max-h-[calc(100dvh-200px)] overflow-y-auto">
        <img :src="propertyImage" :alt="propertyAddress" class="rounded-lg max-w-[280px] md:max-w-[350px]  mx-auto"/>
        <InputError :message="form.errors.propertyId" class="mt-2"/>
        <InputError :message="form.errors.propertyAddress" class="mt-2"/>
        <div class="flex flex-col gap-1.5">
          <Label>Address</Label>
          <Input type="text" v-model="form.propertyAddress"/>
          <InputError :message="form.errors.propertyAddress" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Your Name</Label>
          <Input type="text" v-model="form.userName"/>
          <InputError :message="form.errors.userName" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Contractual Name *</Label>
          <Input type="text" v-model="form.userContractualName"/>
          <InputError :message="form.errors.userContractualName" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Email</Label>
          <Input type="email" v-model="form.userEmail"/>
          <InputError :message="form.errors.userEmail" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Phone</Label>
          <Input type="tel" v-model="form.userPhone"/>
          <InputError :message="form.errors.userPhone" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Offer Price *</Label>
          <InputNumber align="left" type="price" v-model="form.offerPrice"/>
          <InputError :message="form.errors.offerPrice" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Deposit Amount *</Label>
          <InputNumber align="left" type="price" v-model="form.depositAmount"/>
          <InputError :message="form.errors.depositAmount" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Preferred Closing Date *</Label>
          <Input type="datetime-local" class="flex-2 custom-date-input" v-model="form.preferredClosingDate"/>
          <InputError :message="form.errors.preferredClosingDate" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Agent Involved? *</Label>
          <SelectBox :not-allow-empty-in-list="true" type="text" v-model="form.agentInvolved" :options="selectOptions"/>
          <InputError :message="form.errors.agentInvolved" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5" v-if="form.agentInvolved == 'Yes'">
          <div class="flex flex-col gap-1.5">
            <Label>Agent Name</Label>
            <Input type="text" v-model="form.agentName"/>
            <InputError :message="form.errors.agentName" class="mt-2"/>
          </div>
          <div class="flex flex-col gap-1.5">
            <Label>Agent Email</Label>
            <Input type="text" v-model="form.agentEmail"/>
            <InputError :message="form.errors.agentEmail" class="mt-2"/>
          </div>
          <div class="flex flex-col gap-1.5">
            <Label>Agent Requested Commission</Label>
            <InputNumber align="left" type="price" v-model="form.agentCommission"/>
            <InputError :message="form.errors.agentCommission" class="mt-2"/>
          </div>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Comments / Notes</Label>
          <TextArea rows="4" type="text" v-model="form.note"/>
          <InputError :message="form.errors.note" class="mt-2"/>
        </div>
      </div>
      <DialogFooter class="gap-2">
        <DialogClose as-child>
          <Button variant="secondary"> Cancel</Button>
        </DialogClose>
        <Button @click="submit" variant="default" :disabled="form.processing">
          Submit Quick Offer
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped>

</style>
