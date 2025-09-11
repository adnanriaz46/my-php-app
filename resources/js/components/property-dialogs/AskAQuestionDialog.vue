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

const {showToast} = useToast()

const page = usePage();

watch(() => page.props?.success, (msg) => {
  if (msg) {
    showToast('Ask a Question', msg, 'success')
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
  {key: "Email", value: "Email"},
  {key: "Text Message", value: "Text Message"},
  {key: "Call", value: "Call"},
];
const form = useForm({
  propertyId: propertyId,// required
  propertyUrl: propertyUrl,
  propertyAddress: propertyAddress,// required
  propertyStreet: props.propertyData?.full_street_address, // required
  propertyCity: props.propertyData?.city_name, // required
  propertyState: props.propertyData?.state_or_province, // required
  propertyZip: props.propertyData?.zip_code, // required
  question: '',// required
  userName: user.name,// required
  userPhone: user.phone_number,// required
  userEmail: user.email,// required
  contactBy: selectOptions[0].key// required
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
  form.post(route('property.request.ask_question'), {
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
        <DialogTitle>Got a Question?</DialogTitle>
        <DialogDescription>
          {{ propertyAddress }}
        </DialogDescription>
      </DialogHeader>
      <div class="w-full grid grid-cols-1 justify-center gap-4 max-h-[calc(100dvh-160px)] overflow-y-auto">
        <img :src="propertyImage" :alt="propertyAddress" class="rounded-lg max-w-[280px] md:max-w-[350px]  mx-auto"/>
        <InputError :message="form.errors.propertyId" class="mt-2"/>
        <InputError :message="form.errors.propertyAddress" class="mt-2"/>
        <div class="flex flex-col gap-1.5">
          <Label>What is your question?*</Label>
          <TextArea rows="4" type="text" v-model="form.question"/>
          <InputError :message="form.errors.question" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Name</Label>
          <Input type="text" v-model="form.userName"/>
          <InputError :message="form.errors.userName" class="mt-2"/>
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
          <Label>How do you prefer us to reach you? *</Label>
          <SelectBox :not-allow-empty-in-list="true" type="text" v-model="form.contactBy" :options="selectOptions"/>
          <InputError :message="form.errors.contactBy" class="mt-2"/>
        </div>
      </div>
      <DialogFooter class="gap-2">
        <DialogClose as-child>
          <Button variant="secondary"> Cancel</Button>
        </DialogClose>
        <Button @click="submit" variant="default" :disabled="form.processing">
          Submit Question
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped>

</style>
