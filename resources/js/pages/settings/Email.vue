<script setup lang="ts">
import {Head, Link, router, useForm, usePage} from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import {Button} from '@/components/ui/button';
import {Input} from '@/components/ui/input';
import {Label} from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import {type BreadcrumbItem, EmailCategory, type SharedData, type User} from '@/types';
import Checkbox from "@/components/ui/checkbox/Checkbox.vue";
import CheckboxLoop from "@/components/ui/checkbox-loop/CheckboxLoop.vue";

interface Props {
  emailCategories: Record<string, EmailCategory>;
  mustVerifyEmail: boolean;
  status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Email settings',
    href: '/settings/profile/email',
  },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
  email: user.email,
  emailVerified: user.email_verified,
  emailVerifiedAt: user.email_verified_at,
  global: user.email_unsubscribed_global,
  emailUnsubscribedListPreference: user.email_unsubscribed_list_preference || [],
});
import {useToast} from '@/composables/useToast'
import Toast from "@/components/ui/toast/Toast.vue";

const {showToast} = useToast()
const submit = () => {
  form.patch(route('profile.email.update'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      showToast('Email Settings', 'Your changes have been updated', 'success')
      router.visit(route('profile.email.edit'), {
        replace: true,
        preserveScroll: true,
      })
    },
  });
};

</script>

<template>
  <Toast/>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Profile settings"/>

    <SettingsLayout class="h-[calc(100dvh-65px)]  overflow-auto">
      <div class="flex flex-col space-y-6">
        <HeadingSmall title="Email Settings" description="Update your email list preference information"/>

        <div class="grid gap-2">
          <Label for="email">Email address</Label>
          <Input disabled class="mt-1 block w-full"
                 v-model="form.email"/>
        </div>
        <div v-if="mustVerifyEmail && !user.email_verified_at">
          <p class="-mt-4 text-sm text-muted-foreground">
            Your email address is unverified.
            <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
            >
              Click here to resend the verification email.
            </Link>
          </p>

          <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
            A new verification link has been sent to your email address.
          </div>
        </div>
        <div class="grid gap-2">
          <div class="text-base font-medium text-muted-foreground">
            Select the categories you wish to <span class="text-red-500">unsubscribe</span> from:
          </div>
        </div>
        <form @submit.prevent="submit" class="space-y-6">
          <table class="table-auto ">
            <tbody>
            <tr :for="`checkbox-${key}`" v-for="(item, key) in emailCategories" :key="key">
              <td width="35px" class="align-top pt-1">
                <CheckboxLoop :id="`checkbox-${key}`"
                              v-model="form.emailUnsubscribedListPreference"
                              :value="key"
                              :tabindex="3"></CheckboxLoop>
              </td>
              <td>
                <div class="font-medium">{{ item.name }}</div>
                <p class="text-sm text-muted-foreground">{{ item.description }}</p>
              </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr :for="`checkbox-global`">
              <td width="35px" class="align-top pt-4 border-t-1 border-t-gray-500 mt-2">
                <Checkbox :id="`checkbox-global`"
                          v-model="form.global"
                          :tabindex="3"></Checkbox>
              </td>
              <td class="pt-3 align-top border-t-1 border-t-gray-500 mt-2">
                <div class="font-medium">Global Unsubscribe</div>
                <p class="text-sm text-muted-foreground">Unsubscribe from all emails (this does not include important emails such as account or security-related messages).</p>
              </td>
            </tr>
            </tbody>
          </table>

          <div class="flex items-center">
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
              <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Your changes have been saved
                successfully!</p>
            </Transition>
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
              <p v-show="form.hasErrors" class="text-sm text-destructive">Check the form for possible input errors.</p>
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
