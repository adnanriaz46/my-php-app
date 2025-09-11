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
import {useToast} from '@/composables/useToast'
import {DBApiPropertyFull} from "@/types/DBApi";
import {User} from "@/types";
import {getMainImage} from "@/types/property";
import TextArea from "@/components/ui/textarea/TextArea.vue";
import Toast from "@/components/ui/toast/Toast.vue";

const {showToast} = useToast()

const page = usePage();

watch(() => page.props?.success, (msg) => {
  if (msg) {
    showToast('Request Showing', msg, 'success')
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


const form = useForm({
  propertyId: propertyId, // required
  propertyUrl: propertyUrl,
  propertyAddress: propertyAddress, // required
  propertyStreet: props.propertyData?.full_street_address, // required
  propertyCity: props.propertyData?.city_name, // required
  propertyState: props.propertyData?.state_or_province, // required
  propertyZip: props.propertyData?.zip_code, // required
  propertyImage: getMainImage(props.propertyData?.full_location?.split(',')[0]), // required
  preferredDateTime: null, // required
  userName: user.name, // required
  userPhone: user.phone_number, // required
  userEmail: user.email, // required
  message: ''
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
  form.post(route('property.request.showing'), {
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
        <DialogTitle>Schedule a Showing</DialogTitle>
        <DialogDescription>
          {{ propertyAddress }}
        </DialogDescription>
      </DialogHeader>
      <div class="w-full grid grid-cols-1 justify-center gap-4 max-h-[calc(100dvh-200px)] overflow-y-auto">
        <img :src="propertyImage" :alt="propertyAddress" class="rounded-lg max-w-[280px] md:max-w-[350px]  mx-auto"/>
        <InputError :message="form.errors.propertyId" class="mt-2"/>
        <InputError :message="form.errors.propertyAddress" class="mt-2"/>
        <div class="flex flex-col gap-1.5">
          <Label>Select a preferred time</Label>
          <Input type="datetime-local" class="flex-2 custom-date-input" v-model="form.preferredDateTime"/>
          <InputError :message="form.errors.preferredDateTime" class="mt-2"/>
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
          <Label>Message (Optional)</Label>
          <TextArea type="text" v-model="form.message"/>
          <InputError :message="form.errors.message" class="mt-2"/>
        </div>
      </div>
      <DialogFooter class="gap-2">
        <DialogClose as-child>
          <Button variant="secondary"> Cancel</Button>
        </DialogClose>
        <Button @click="submit" variant="default" :disabled="form.processing">
          Submit Request
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
  <Toast/>
</template>

<style scoped>

</style>
