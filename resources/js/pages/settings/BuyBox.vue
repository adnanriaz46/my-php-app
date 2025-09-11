<script setup lang="ts">
import {Head, router, useForm, usePage} from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import {Button} from '@/components/ui/button';

import {Label} from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import {type BreadcrumbItem, type BuyBox, type SharedData} from '@/types';
import Combobox from "@/components/ui/combobox/Combobox.vue";
import InputNumber from "@/components/ui/input-number/InputNumber.vue";


interface Props {
  mustVerifyEmail: boolean;
  status?: string;
  buybox?: BuyBox;
  propertyTypes: [];
  invStgy: [];
  counties: [];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Buy Box settings',
    href: '/settings/profile/buybox',
  },
];

const page = usePage<SharedData>();
// const user = page.props.auth.user as User;
const buyBox = page.props.buybox as BuyBox;

const form = useForm({
  investment_strategy: buyBox?.investment_strategy || [],
  counties_invest: buyBox?.counties_invest || [],
  property_types: buyBox?.property_types || [],
  arv_min: Number(buyBox?.arv_min) || null,
  arv_max: Number(buyBox?.arv_max) || null,
  bath_min: Number(buyBox?.bath_min) || null,
  bath_max: Number(buyBox?.bath_max) || null,
  bed_min: Number(buyBox?.bed_min) || null,
  bed_max: Number(buyBox?.bed_max) || null,
  cashflow_min: Number(buyBox?.cashflow_min) || null,
  cashflow_max: Number(buyBox?.cashflow_max) || null,
  delta_psf_min: Number(buyBox?.delta_psf_min) || null,
  delta_psf_max: Number(buyBox?.delta_psf_max) || null,
  flip_profit_min: Number(buyBox?.flip_profit_min) || null,
  flip_profit_max: Number(buyBox?.flip_profit_max) || null,
  price_min: Number(buyBox?.price_min) || null,
  price_max: Number(buyBox?.price_max) || null,
  sqft_min: Number(buyBox?.sqft_min) || null,
  sqft_max: Number(buyBox?.sqft_max) || null,
  year_build_min: Number(buyBox?.year_build_min) || null,
  year_build_max: Number(buyBox?.year_build_max) || null,
});
import {useToast} from '@/composables/useToast'
import Toast from "@/components/ui/toast/Toast.vue";

const {showToast} = useToast()
const submit = () => {
  form.patch(route('profile.buy_box.update'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      showToast('Buy Box', 'Your changes have been updated', 'success')
      router.visit(route('profile.buy_box.edit'), {
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

    <Head title="Profile settings" />

    <SettingsLayout class="h-[calc(100dvh-65px)]  overflow-auto">
      <div class="flex flex-col space-y-6">
        <HeadingSmall title="Buy Box Information" description="Update your buy box information"/>

        <form @submit.prevent="submit" class="space-y-6">

          <div class="grid gap-2">
            <Label for="counties_invest">Counties You Purchase In</Label>
            <Combobox
                id="counties_invest"
                v-model="form.counties_invest"
                :optionValues="counties"
            />
            <InputError class="mt-2" :message="form.errors.counties_invest"/>
          </div>

          <div class="grid gap-2">
            <Label for="investment_strategy">Investment Strategy</Label>
            <Combobox id="investment_strategy"
                      v-model="form.investment_strategy"
                      :optionValues="invStgy"
            />
            <InputError class="mt-2" :message="form.errors.investment_strategy"/>
          </div>

          <div class="grid gap-2">
            <Label for="investment_strategy">Property Types</Label>
            <Combobox
                id="investment_strategy"
                v-model="form.property_types"
                :optionValues="propertyTypes"
            />
            <InputError class="mt-2" :message="form.errors.property_types"/>
          </div>


          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="arv_min">ARV Min</Label>
              <InputNumber v-model="form.arv_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.arv_min"/>
            </div>
            <div class="">
              <Label for="arv_max">ARV Max</Label>
              <InputNumber v-model="form.arv_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.arv_max"/>
            </div>
          </div>

          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="bath_min">Bath Min</Label>
              <InputNumber v-model="form.bath_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.bath_min"/>
            </div>
            <div class="">
              <Label for="bath_max">Bath Max</Label>
              <InputNumber v-model="form.bath_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.bath_max"/>
            </div>
          </div>

          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="bed_min">Bed Min</Label>
              <InputNumber v-model="form.bed_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.bed_min"/>
            </div>
            <div class="">
              <Label for="bed_max">Bed Max</Label>
              <InputNumber v-model="form.bed_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.bed_max"/>
            </div>
          </div>

          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="cashflow_min">Cashflow Min</Label>
              <InputNumber v-model="form.cashflow_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.cashflow_min"/>
            </div>
            <div class="">
              <Label for="cashflow_max">Cashflow Max</Label>
              <InputNumber v-model="form.cashflow_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.cashflow_max"/>
            </div>
          </div>

          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="delta_psf_min">Delta Min</Label>
              <InputNumber v-model="form.delta_psf_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.delta_psf_min"/>
            </div>
            <div class="">
              <Label for="delta_psf_max">Delta Max</Label>
              <InputNumber v-model="form.delta_psf_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.delta_psf_max"/>
            </div>

          </div>
          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="flip_profit_min">Flip Profit Min</Label>
              <InputNumber v-model="form.flip_profit_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.flip_profit_min"/>
            </div>
            <div class="">
              <Label for="flip_profit_max">Flip Profit Max</Label>
              <InputNumber v-model="form.flip_profit_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.flip_profit_max"/>
            </div>

          </div>
          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="price_min">Price Min</Label>
              <InputNumber v-model="form.price_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.price_min"/>
            </div>
            <div class="">
              <Label for="price_max">Price Max</Label>
              <InputNumber v-model="form.price_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.price_max"/>
            </div>

          </div>
          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="sqft_min">SQFT Min</Label>
              <InputNumber v-model="form.sqft_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.sqft_min"/>
            </div>
            <div class="">
              <Label for="sqft_max">SQFT Max</Label>
              <InputNumber v-model="form.sqft_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.sqft_max"/>
            </div>

          </div>
          <div class="flex gap-2 gap-x-6">
            <div class="">
              <Label for="year_build_min">Year Build Min</Label>
              <InputNumber v-model="form.year_build_min"></InputNumber>
              <InputError class="mt-2" :message="form.errors.year_build_min"/>
            </div>
            <div class="">
              <Label for="year_build_max">Year Build Max</Label>
              <InputNumber v-model="form.year_build_max"></InputNumber>
              <InputError class="mt-2" :message="form.errors.year_build_max"/>
            </div>
          </div>
          <div class="flex items-center">
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
              <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Your changes have been
                saved successfully!</p>
            </Transition>
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
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
