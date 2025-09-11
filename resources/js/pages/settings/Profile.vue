<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import TextArea from "@/components/ui/textarea/TextArea.vue";
import { Icon } from '@iconify/vue'


import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader, DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import UserTypeBadge from "@/components/ui/user-type-badge/UserTypeBadge.vue";
import { useToast } from '@/composables/useToast'
import Toast from "@/components/ui/toast/Toast.vue";

const { showToast } = useToast()

interface Props {
  mustVerifyEmail: boolean;
  status?: string;
  error?: string;
  success?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Profile settings',
    href: '/settings/profile',
  },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
  email: user.email,
  firstName: user.first_name,
  lastName: user.last_name,
  streetAddress: user.street_address,
  city: user.city,
  state: user.state,
  zip: user.zip,
  phoneNumber: user.phone_number,
  profilePicture: user.profile_picture,
  aboutMe: user.about_me,
});

// const currentRoute = route().current();

const submit = () => {
  form.patch(route('profile.update'), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      showToast('Profile', 'Your changes have been updated', 'success')
      router.visit(route('profile.update'), {
        replace: true,
        preserveScroll: true,
      })
    },
  });
};

const closeModal = () => {
  form.clearErrors();
  form.reset();
};
const subscribedCounties = ref<string[]>([]);
onMounted(() => {
  subscribedCounties.value = user.subscribed_counties;
});

</script>

<script lang="ts">
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { UserTypes } from '@/types/userTypes';
import Badge from '@/components/ui/badge/Badge.vue';

const page = usePage<SharedData>();

export default {
  components: {
    Cropper,
  },
  data() {
    return {
      dialogOpen: false,
      img: null,       // Image preview source
      canvas: null,    // Cropped canvas result
      avatar_error: '',
      avatar_loading: false
    }
  },
  methods: {
    change({ coordinates, canvas }) {
      console.log(coordinates, canvas)
      this.canvas = canvas;
    },
    onFileChange(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
          this.img = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    refreshPage() {
      this.$inertia.reload({
        preserveState: false,
        new_s: 'yes',
      });
    },
    emitCroppedImage() {
      if (!this.canvas) return;
      this.canvas.toBlob((blob) => {
        const formData = new FormData();
        formData.append('image', blob, 'cropped.jpg');
        this.avatar_error = '';
        this.avatar_loading = true;
        axios.post(route('profile.upload'), formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
          .then((response) => {
            if (response.data!.error) {
              this.avatar_error = response.data.error;
            } else {
              this.dialogOpen = false;
              page.props.auth.user.profile_picture = response.data.url;
              console.log('Upload', response.data.url);
            }
            this.avatar_loading = false;
          })
          .catch((error) => {
            console.log('ERROR:', error);
            this.avatar_error = error.response?.data?.message;
            if (!this.avatar_error) {
              this.avatar_error = error;
            }
            this.avatar_loading = false;
          });
      }, 'image/jpeg');
    },
  },
}

</script>

<template>
  <Toast />
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="Profile settings" />

    <SettingsLayout class="h-[calc(100dvh-65px)]  overflow-auto">
      <div class="flex flex-col space-y-6">
        <div v-if="success"
          class="flex items-start gap-3 rounded-2xl border border-green-300 bg-green-50 dark:bg-green-800 p-4 text-green-600 dark:text-green-200 shadow-md">
          <Icon icon="tabler:circle-dashed-check" class="text-xl" />
          <div class="text-sm font-medium">
            {{ success }}
          </div>
        </div>

        <div v-if="error"
          class="flex items-start gap-3 rounded-2xl border border-red-300 bg-red-50 p-4 text-red-800 dark:bg-red-800 dark:text-red-200 shadow-md">
          <Icon icon="tabler:circle-dashed-x" class="text-xl" />
          <div class="text-sm font-medium">
            {{ error }}
          </div>
        </div>

        <HeadingSmall title="Basic Profile" :user="user" description="Update your basic information" />

        <Dialog v-model:open="dialogOpen">
          <DialogTrigger as-child>
            <Button variant="outline">Change Profile Picture</Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader class="space-y-3">
              <DialogTitle>Upload Profile Picture</DialogTitle>
            </DialogHeader>

            <div class="flex justify-center">
              <Input type="file" @change="onFileChange" class="" accept="image/*" />
            </div>
            <div class="flex justify-center">
              <cropper :hidden="!img" style="max-width: 320px; height: 320px" class="cropper rounded-xl content-center"
                :src="img" :auto-zoom="true" :stencil-props="{ aspectRatio: 1 }" @change="change" />
            </div>
            <div class="flex justify-center">
              <InputError class="mt-2" :message="avatar_error" />
            </div>
            <DialogFooter class="gap-2">
              <DialogClose as-child>
                <Button variant="secondary" @click="closeModal"> Cancel</Button>
              </DialogClose>

              <Button v-if="img" @click="emitCroppedImage" :disabled="avatar_loading">Upload
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>


        <div class="grid gap-2">
          <Label for="email">Email address
            <UserTypeBadge :user-type="user.user_type"></UserTypeBadge>
          </Label>
          <Input disabled class="mt-1 block w-full" v-model="form.email" />
        </div>
        <div v-if="mustVerifyEmail && !user.email_verified_at">
          <p class="-mt-4 text-sm text-muted-foreground">
            Your email address is unverified.
            <Link :href="route('verification.send')" method="post" as="button"
              class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500">
            Click here to resend the verification email.
            </Link>
          </p>

          <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
            A new verification link has been sent to your email address.
          </div>
        </div>
        <div class="grid gap-2" v-if="user.user_type == UserTypes.PREMIUM">
          <Label for="email">
            Subscribed Counties ({{ subscribedCounties.length > 10 ? '10+' : subscribedCounties.length }})
            <Badge variant="secondary" class="text-sm ml-2" v-if="subscribedCounties.length >= 10">
              <Icon icon="tabler:lock-open" :height="16" />&nbsp;All Counties
            </Badge>
          </Label>
          <div class="mt-1 w-full flex flex-wrap gap-2 overflow-y-auto max-h-[100px]">
            <Badge variant="default" v-for="county in subscribedCounties" :key="county" class="text-sm cursor-default">
              {{ county }}
            </Badge>
          </div>
        </div>


        <form @submit.prevent="submit" class="space-y-6">

          <div class="grid gap-2">
            <Label for="firstName">First Name</Label>
            <Input id="firstName" class="mt-1 block w-full" v-model="form.firstName" required autocomplete="firstName"
              placeholder="First name" />
            <InputError class="mt-2" :message="form.errors.firstName" />
          </div>
          <div class="grid gap-2">
            <Label for="lastName">Last Name</Label>
            <Input id="lastName" class="mt-1 block w-full" v-model="form.lastName" required autocomplete="lastName"
              placeholder="Last name" />
            <InputError class="mt-2" :message="form.errors.lastName" />
          </div>


          <div class="grid gap-2">
            <Label for="streetAddress">Phone Number</Label>
            <Input id="streetAddress" class="mt-1 block w-full" v-model="form.phoneNumber" type="tel"
              autocomplete="phoneNumber" placeholder="Street Address" />
            <InputError class="mt-2" :message="form.errors.phoneNumber" />
          </div>

          <div class="grid gap-2">
            <Label for="streetAddress">Street Address</Label>
            <Input id="streetAddress" class="mt-1 block w-full" v-model="form.streetAddress"
              autocomplete="streetAddress" placeholder="Street Address" />
            <InputError class="mt-2" :message="form.errors.streetAddress" />
          </div>

          <div class="grid gap-2">
            <Label for="city">City</Label>
            <Input id="city" class="mt-1 block w-full" v-model="form.city" autocomplete="city" placeholder="City" />
            <InputError class="mt-2" :message="form.errors.city" />
          </div>


          <div class="grid gap-2">
            <Label for="state">State(Short)</Label>
            <Input id="state" class="mt-1 block w-full" v-model="form.state" autocomplete="state"
              placeholder="State (example: PA)" />
            <InputError class="mt-2" :message="form.errors.state" />
          </div>


          <div class="grid gap-2">
            <Label for="zip">Zip</Label>
            <Input id="zip" class="mt-1 block w-full" v-model="form.zip" autocomplete="zip" placeholder="zip" />
            <InputError class="mt-2" :message="form.errors.zip" />
          </div>


          <div class="grid gap-2">
            <Label for="aboutMe">About Me</Label>
            <TextArea id="aboutMe" class="mt-1 block w-full" v-model="form.aboutMe"
              placeholder="Write about you"></TextArea>
            <InputError class="mt-2" :message="form.errors.aboutMe" />
          </div>


          <div class="flex items-center">
            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
              <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Your changes have been
                saved successfully!</p>
            </Transition>
            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
              <p v-show="form.hasErrors" class="text-sm text-destructive">Check the form for possible
                input errors.</p>
            </Transition>
          </div>
          <div class="flex items-center gap-4">
            <Button :disabled="form.processing" class="w-full">Save</Button>
          </div>
        </form>
      </div>

      <!--            <DeleteUser/>-->

    </SettingsLayout>
  </AppLayout>
</template>
