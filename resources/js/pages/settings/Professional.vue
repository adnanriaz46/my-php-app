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
import { Icon } from '@iconify/vue';

import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader, DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import { useToast } from '@/composables/useToast'
import Toast from "@/components/ui/toast/Toast.vue";

const { showToast } = useToast()

defineProps<{
  mustVerifyCompanyEmail: boolean;
  status?: string;
  success?: string;
  error?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Professional settings',
    href: '/settings/profile/professional',
  },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
  companyName: user.company_name,
  companyEmail: user.company_email,
  brokerageName: user.brokerage_name,
  agentLicenseNumber: user.agent_license_number
});

const submit = () => {
  form.patch(route('profile.professional.update'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      showToast('Professional', 'Your changes have been updated', 'success')
      router.visit(route('profile.professional.edit'), {
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
</script>

<script lang="ts">
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import axios from 'axios';

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
      console.log(coordinates, canvas);
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
      });
    },
    emitCroppedImage() {
      if (!this.canvas) return;
      this.canvas.toBlob((blob) => {
        const formData = new FormData();
        formData.append('image', blob, 'cropped.jpg');
        this.avatar_error = '';
        this.avatar_loading = true;
        axios.post(route('profile.professional.upload'), formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
          .then((response) => {
            console.log('Upload success:', response.data);
            if (response.data!.error) {
              this.avatar_error = response.data.error;
            } else {
              this.dialogOpen = false;
              page.props.auth.user.company_logo = response.data.url;

            }
            this.avatar_loading = false;
          })
          .catch((error) => {
            this.avatar_error = error.response!.data!.error;
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
        <HeadingSmall title="Professional Profile" :user="user" image-key="company_logo"
          description="Update your professional information" />

        <Dialog v-model:open="dialogOpen">
          <DialogTrigger as-child>
            <Button variant="outline">Change Company Logo</Button>
          </DialogTrigger>
          <DialogContent>

            <DialogHeader class="space-y-3">
              <DialogTitle>Upload a picture</DialogTitle>
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

        <form @submit.prevent="submit" class="space-y-6">

          <div class="grid gap-2">
            <Label for="companyName">Company Name</Label>
            <Input id="companyName" class="mt-1 block w-full" v-model="form.companyName" required
              autocomplete="companyName" placeholder="Company name" />
            <InputError class="mt-2" :message="form.errors.companyName" />
          </div>
          <div class="grid gap-2">
            <Label for="companyEmail">Company Email</Label>
            <Input id="companyEmail" class="mt-1 block w-full" v-model="form.companyEmail" type="email" required
              autocomplete="companyEmail" placeholder="Company email" />
            <InputError class="mt-2" :message="form.errors.companyEmail" />
          </div>
          <!-- Company Email Verification Section -->
          <div v-if="mustVerifyCompanyEmail && user.company_email">
            <div
              class="rounded-lg border border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20 p-4">
              <div class="flex items-start gap-3">
                <Icon icon="tabler:alert-triangle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                <div class="flex-1">
                  <h3 class="font-medium text-yellow-800 dark:text-yellow-200">
                    Company Email Verification Required
                  </h3>
                  <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                    Your company email address <strong>{{ user.company_email }}</strong> needs to be verified.
                  </p>
                  <div class="mt-3">
                    <Link :href="route('profile.company-email.verification.send')" method="post" as="button"
                      class="inline-flex items-center gap-2 rounded-md bg-yellow-600 px-3 py-2 text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-yellow-900">
                    <Icon icon="tabler:mail" class="h-4 w-4" />
                    Send Verification Email
                    </Link>
                  </div>
                  <div v-if="status === 'company-email-verification-sent'"
                    class="mt-2 text-sm font-medium text-green-600">
                    A verification link has been sent to your company email address.
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="grid gap-2">
            <Label for="brokerageName">Brokerage Name</Label>
            <Input id="brokerageName" class="mt-1 block w-full" v-model="form.brokerageName"
              autocomplete="brokerageName" placeholder="Brokerage name" />
            <InputError class="mt-2" :message="form.errors.brokerageName" />
          </div>

          <div class="grid gap-2">
            <Label for="agentLicenseNumber">Agent License Number</Label>
            <Input id="agentLicenseNumber" class="mt-1 block w-full" v-model="form.agentLicenseNumber"
              autocomplete="agentLicenseNumber" placeholder="Agent license number" />
            <InputError class="mt-2" :message="form.errors.agentLicenseNumber" />
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
