<script setup lang="ts">
import {Head, router, useForm} from '@inertiajs/vue3';

import {Button} from '@/components/ui/button';
import {Input} from '@/components/ui/input';
import {Label} from '@/components/ui/label';
import {EmailCategory,type User} from '@/types';
import Checkbox from "@/components/ui/checkbox/Checkbox.vue";
import CheckboxLoop from "@/components/ui/checkbox-loop/CheckboxLoop.vue";
import {useToast} from '@/composables/useToast'
import Toast from "@/components/ui/toast/Toast.vue";
import PageHeading from "@/components/ui/page-heading/PageHeading.vue";

const {showToast} = useToast()

interface Props {
  user: User;
  emailCategories: Record<string, EmailCategory>;
  status?: string;
}

const props = defineProps<Props>();

const user = props.user;

const form = useForm({
  user_id: user.uuid,
  emailVerified: user.email_verified,
  emailVerifiedAt: user.email_verified_at,
  global: user.email_unsubscribed_global,
  emailUnsubscribedListPreference: user.email_unsubscribed_list_preference || [],
});

const submit = () => {
  form.patch(route('unsubscribe.submit'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      showToast('Email Settings', 'Your changes have been updated', 'success')
      router.visit(route('unsubscribe.page', user.uuid), {
        replace: true,
        preserveScroll: true,
      })
    },
  });
};

</script>

<template>
  <Toast/>
  <Head title="Unsubscribe"/>
  <div class="w-full flex justify-center">
    <div class="flex flex-col space-y-6 max-w-xl">
      <PageHeading title="Email Settings" sub-title="Update your email list preference information"/>

      <div class="grid gap-2 px-3 ">
        <Label for="email">Email address</Label>
        <Input disabled class="mt-1 block w-full"
               :default-value="props.user.email"/>
      </div>
      <form @submit.prevent="submit" class="space-y-6 px-3 mb-3">
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
              <p class="text-sm text-muted-foreground">Unsubscribe from all emails (this does not include important
                emails such as account or security messages).</p>
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
  </div>
</template>
