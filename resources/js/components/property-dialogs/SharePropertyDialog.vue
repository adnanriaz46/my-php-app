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
// import {DBApiPropertyFull} from "@/types/DBApi";
import {User} from "@/types";
// import {getMainImage} from "@/types/property";
import TextArea from "@/components/ui/textarea/TextArea.vue";
// import {useClipboard} from '@vueuse/core'
import {SFacebook, STwitter, SLinkedIn} from 'vue-socials'
import {Icon} from '@iconify/vue';
import {useClipboardCopy} from "@/composables/useCopy";

const {showToast} = useToast()
const page = usePage();


watch(() => page.props?.success, (msg) => {
  if (msg) {
    showToast('Share Property', msg.toString(), 'success')
  }
}, {deep: true});

watch(() => page.props?.error, (msg) => {
  if (msg) {
    showToast('Share Property', msg.toString(), 'error')
  }
}, {deep: true});


const props = defineProps<{
  propertyUrl: string,
  propertyAddress: string,
  propertyImage?: string,
  listingType: 'Unlisted' | 'Listed',
  propertyId?: number;
  open: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const dialogOpen = ref<boolean>(props.open);


const user = page.props.auth.user as User;
const userEmail = user.email;
const propertyId = props.propertyId;
const propertyAddress = props.propertyAddress;
// const propertyImage = props.propertyImage;
const propertyUrl = props.propertyUrl;


const form = useForm({
  propertyId: propertyId,
  propertyUrl: propertyUrl,
  propertyAddress: propertyAddress,
  listingType: props.listingType,
  toEmail: '',
  fromEmail: userEmail,
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
const {handleCopy, copied} = useClipboardCopy()

const submit = () => {
  form.post(route('property.request.share_property'), {
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
        <DialogTitle>Share This Property</DialogTitle>
        <DialogDescription>
          {{ propertyAddress }}
        </DialogDescription>
      </DialogHeader>
      <div class="w-full grid grid-cols-1 justify-center gap-4 max-h-[calc(100dvh-160px)] overflow-y-auto">
        <div class="w-full flex" v-if="propertyUrl">
          <SFacebook :shareOptions=" {
          url: propertyUrl,
          quote: 'Quote',
          hashtag: '#Github',
        }">
            <Icon icon="entypo-social:facebook-with-circle" class="size-6"/>
          </SFacebook>
          <STwitter :shareOptions=" {
          url: propertyUrl,
          text: 'Hello world',
          hashtags: ['hash', 'tag'],
          via: 'twitterdev',
        }">
            <Icon icon="entypo-social:twitter-with-circle" class="size-6"/>
          </STwitter>
          <SLinkedIn :shareOptions=" {
          url: propertyUrl,
          quote: 'Quote',
          hashtag: '#Github',
        }">
            <Icon icon="entypo-social:linkedin-with-circle" class="size-6"/>
          </SLinkedIn>

        </div>
        <div class="w-full flex justify-center">
          <Button @click="handleCopy(propertyUrl)" variant="link">Click Here To Copy URL</Button>
          <span class="text-xs text-muted-foreground my-auto pl-2" v-if="copied">Copied</span>
        </div>
        <InputError :message="form.errors.propertyId" class="mt-2"/>
        <InputError :message="form.errors.propertyAddress" class="mt-2"/>
        <InputError :message="form.errors.propertyUrl" class="mt-2"/>
        <InputError :message="form.errors.listingType" class="mt-2"/>
        <div class="flex flex-col gap-1.5">
          <Label>To Email</Label>
          <Input type="email" v-model="form.toEmail"/>
          <InputError :message="form.errors.toEmail" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Your Email</Label>
          <Input type="email" v-model="form.fromEmail"/>
          <InputError :message="form.errors.fromEmail" class="mt-2"/>
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Message (optional)</Label>
          <TextArea v-model="form.message" rows="3" placeholder="Enter your message here"/>
          <InputError :message="form.errors.message" class="mt-2"/>
        </div>
      </div>
      <DialogFooter class="gap-2">
        <DialogClose as-child>
          <Button variant="secondary"> Cancel</Button>
        </DialogClose>
        <Button @click="submit" variant="default" :disabled="form.processing">
          Send Email
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped>

</style>
