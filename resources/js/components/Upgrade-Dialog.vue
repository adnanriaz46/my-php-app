<script setup lang="ts">

import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogClose,
  DialogTrigger,
  DialogFooter
} from "@/components/ui/dialog";

import { upgradeDialog } from '@/stores/DialogStore';
import Label from "@/components/ui/label/Label.vue";
import Combobox from "@/components/ui/combobox/Combobox.vue";
import axios from "axios";
import { ref, onMounted, computed, watch } from 'vue'
import { router, useForm } from "@inertiajs/vue3";
import { Card } from "@/components/ui/card";
import { SwitchRoot, SwitchThumb } from "reka-ui";
// import {SharedData} from "@/types";
import { getUser, isPremiumUser } from "@/composables/useUser"
import { Icon } from '@iconify/vue'
import { UpgradeFeature } from "@/types";
import { UserTypes } from "@/types/userTypes";
import { useToast } from "@/composables/useToast";

interface DetailDialogAttr {
  open: boolean;
  title: string;
  description: string;
}

// interface Props {
//
// }

// const props = defineProps<Props>();
const processing = ref(false);
const detailDialogData = ref<DetailDialogAttr>({
  open: false,
  title: '',
  description: ''
});
// const page = usePage<SharedData>();
const downgradeDialog = ref(false);
const featureValues = ref<UpgradeFeature[]>([]);
const counties = ref([])
const error = ref('')
const { showToast } = useToast();

const stripeMonthly = import.meta.env.VITE_STRIPE_MONTHLY;
const stripeYearly = import.meta.env.VITE_STRIPE_YEARLY;

const price = ref(0);

const fetchCounties = async () => {
  processing.value = true;
  try {
    const response = await axios.get(route('get.data.combobox_counties'))
    counties.value = response.data
  } catch (err) {
    error.value = 'Failed to load counties'
    console.error(err)
  } finally {
    processing.value = false;
  }
}
const fetchFeatures = async () => {
  processing.value = true;
  try {
    const response = await axios.get(route('get.data.upgrade_features'))
    featureValues.value = response.data
  } catch (err) {
    error.value = 'Failed to load features'
    console.error(err)
  } finally {
    processing.value = false;
  }
}
onMounted(async () => {
  await Promise.all([fetchCounties(), fetchFeatures()])
  if (!upgradeDialog.user) {
    form.counties = [];
  } else {
    form.counties = [...upgradeDialog.user?.subscribed_counties]
  }

  if (form.counties.length === 0) {
    form.counties = ['Delaware, PA'];
  }
  getPrice();

})

const form = useForm({
  counties: upgradeDialog.user?.subscribed_counties as string[] ?? [],
  isMonthly: upgradeDialog.user?.subscription_period_monthly as boolean ?? false
});

const isUnlocked = computed(() => {
  return (
    form.counties?.length >= 10
  );
});

const getPrice = () => {
  processing.value = true;
  const count = form.counties?.length;
  if (form.isMonthly) {
    price.value = Math.min(count * 20, 200);
  } else {
    price.value = Math.min(count * 15, 150);
  }
  processing.value = false;
}


watch(
  () => upgradeDialog.user?.subscribed_counties,
  (newVal) => {
    if (newVal) {
      form.counties = [...newVal]
    }
  },
  { immediate: true }
)

watch(
  () => upgradeDialog.user?.subscription_period_monthly,
  (newVal) => {
    form.isMonthly = newVal || false
  },
  { immediate: true }
)

watch([() => form.counties, () => form.isMonthly], () => {
  getPrice();
});

const isUpgradeDisabled = computed(() => {
  return (
    !form.counties?.length ||
    processing.value
  );
});

const isDowngradeDisabled = computed(() => {
  return (
    getUser()?.user_type == UserTypes.FREE ||
    processing.value
  );
});

const showDetailDialog = (title: string, description: string) => {
  detailDialogData.value.title = title;
  detailDialogData.value.description = description;
  detailDialogData.value.open = true;
}

const submit = async () => {
  if (!getUser()) {
    router.visit(route('login'));
    showToast('Login Required', 'You should logged in to proceed!', 'warning')
    return;
  }

  if (getUser()?.user_type == UserTypes.ADMIN) {
    alert('Admin could not subscribe to a plan, Please create non-admin account')
    return false;
  }

  processing.value = true;
  try {
    const priceId = form.isMonthly ? stripeMonthly : stripeYearly;
    const response = await axios.post(route('subscription.create'), {
      price_id: priceId,
      quantity: form.counties.length,
      counties: form.counties,
      is_monthly: form.isMonthly,
    });
    if (response.data?.url) {
      window.location.href = response.data.url;
    } else if (response.data?.error) {
      alert('ERROR: ' + response.data?.error);
    } else if (response.data?.success) {
      alert('Success: ' + response.data?.success);
      window.location.href = route('profile.edit');
    }
    upgradeDialog.upgradeDialogOpen = false
  } catch (e) {
    console.error(e);
    if (e?.response?.data?.message) {
      alert('ERROR: ' + e?.response?.data?.message)
    } else {
      alert('ERROR: ' + e?.message);
    }
  } finally {
    processing.value = false;
  }
};
const downgrade = async () => {
  if (!getUser()) {
    router.visit(route('login'));
    showToast('Login Required', 'You should logged in to proceed!', 'warning')
    return;
  }
  if (getUser()?.user_type == UserTypes.ADMIN) {
    alert('Admin could not subscribe to a plan, Please create non-admin account')
    return false;
  }
  processing.value = true;
  try {
    try {
      const res = await axios.post(route('subscription.cancel'));
      if (res.data?.success) {
        alert(res.data.success);
        window.location.href = route('profile.edit');
      } else {
        alert("ERROR: " + res.data?.error);
      }
    } catch (err) {
      alert(err?.response?.data?.error || 'Cancellation failed');
    }
  } catch (e) {
    console.error(e);
    alert(e?.response?.data?.error || e?.message || 'Cancellation failed');
  } finally {
    processing.value = false;
    upgradeDialog.upgradeDialogOpen = false
  }
};

/* 
 @Usage: Copy this code and use it to open the upgrade dialog
...
import { upgradeDialog } from '@/stores/DialogStore';
...
 const openUpgrade = () => {
    upgradeDialog.user = getUser();
    upgradeDialog.upgradeDialogOpen = true;
 }
...
<UpgradeDialog />

Use openUpgrade() to open the upgrade dialog
 */


</script>

<template>
  <Dialog v-if="upgradeDialog.upgradeDialogOpen" v-model:open="upgradeDialog.upgradeDialogOpen">
    <DialogContent class="md:min-w-[650px] sm:min-w-full h-full ">
      <DialogHeader class="space-y-3">
        <DialogTitle class="text-center text-3xl">Upgrade to add counties, and add profits</DialogTitle>
      </DialogHeader>


      <div class="w-full h-fit max-h-full overflow-y-auto scroll-auto pt-1">
        <div class=" mx-auto md:mt-5">
          <div class="flex justify-center mb-8">

            <div class="flex gap-2 items-center mt-3">
              <label class="text-stone-700 dark:text-whiteleading-none pr-2 select-none cursor-pointer"
                for="airplane-mode">
                <span class="ml-2 font-semibold relative block dark:text-white"><span
                    class="inline-flex px-2 py-0.5 text-xs font-normal rounded-full shadow-sm text-white bg-green-800 absolute bottom-5 right-[-5px] w-[110px] text-center">Save
                    up to 25%</span> Yearly</span>
              </label>

              <SwitchRoot id="airplane-mode" v-model="form.isMonthly"
                class="w-[32px] h-[20px] cursor-pointer shadow-sm flex data-[state=unchecked]:bg-stone-300 data-[state=checked]:bg-stone-800 dark:data-[state=unchecked]:bg-stone-800 dark:data-[state=checked]:bg-stone-700 border border-stone-300 data-[state=checked]:border-stone-700  dark:border-stone-700 rounded-full relative transition-[background] focus-within:outline-none focus-within:shadow-[0_0_0_1px] focus-within:border-stone-800 focus-within:shadow-stone-800">
                <SwitchThumb
                  class="w-3.5 h-3.5 my-auto bg-white text-xs flex items-center justify-center shadow-xl rounded-full transition-transform translate-x-0.5 will-change-transform data-[state=checked]:translate-x-full" />
              </SwitchRoot>
              <label class="text-stone-700 dark:text-white leading-none pr-2 select-none cursor-pointer"
                for="airplane-mode">
                <span class="mr-2 font-semibold">Monthly</span>
              </label>
            </div>
          </div>
        </div>
        <Label for="preferred-counties">
          Counties ({{ (isUnlocked) ? '10+' : form.counties?.length }})
          <span v-if="isUnlocked"
            class="inline-flex px-2 py-0.5 text-xs font-normal rounded-full shadow-sm text-white bg-green-800">
            <Icon icon="tabler:lock-open" :height="16" />&nbsp;All Counties
          </span>
        </Label>
        <Combobox id="preferred-counties" :option-values="counties" v-model="form.counties"></Combobox>
        <span class="text-muted-foreground text-sm">* After 10 counties, you automatically unlock ALL data at no
          additional cost</span>
        <div class=" mx-auto md:mt-5">

          <!-- Pricing Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Free Plan -->
            <Card class="p-6 border rounded-2xl shadow-sm m-1">
              <h2 class="text-2xl font-bold mb-0">Basic</h2>
              <p class="text-3xl font-semibold mb-0 text-right">$0<span class="text-base font-normal">/month</span>
              </p>
              <Dialog v-model:open="downgradeDialog">
                <DialogTrigger as-child>
                  <Button variant="outline" class="mb-2 w-full" :disabled="isDowngradeDisabled"
                    v-if="isPremiumUser(getUser()?.user_type)">
                    <Icon v-if="processing" icon="tabler:loader-2" class="loading-icon text-lg" />
                    Downgrade Your Plan
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader class="space-y-3">
                    <DialogTitle class="text-center text-xl">Confirm Your Plan Downgrade
                    </DialogTitle>
                  </DialogHeader>
                  <div class="w-full p-4">
                    <p class="text">Are you sure you want to downgrade your plan?</p>
                  </div>
                  <DialogFooter class="gap-2">
                    <DialogClose as-child>
                      <Button variant="secondary">
                        Cancel
                      </Button>
                    </DialogClose>
                    <Button variant="default" :disabled="isDowngradeDisabled" @click="downgrade"
                      v-if="isPremiumUser(getUser()?.user_type)">
                      <Icon v-if="processing" icon="tabler:loader-2" class="loading-icon text-lg" />
                      Downgrade Your Plan (Immediate)
                    </Button>
                  </DialogFooter>
                </DialogContent>
              </Dialog>

              <Button variant="outline" class="mb-2 w-full" :disabled="true"
                v-if="!isPremiumUser(getUser()?.user_type)">Your current plan
              </Button>
              <ul class="space-y-2 text-sm max-h-[250px] overflow-y-auto">
                <li class="cursor-pointer" @click="showDetailDialog(item.feature, item.description)"
                  v-for="item in featureValues.filter(f => f.group === UserTypes.FREE)" :key="item.id">
                  <Icon class="inline-flex text-lg" :style="{ 'color': 'red' }" v-if="item.no_access"
                    icon="tabler:ban" />
                  <Icon class="inline-flex text-lg text-green-600 dark:text-[yellowgreen] "
                    v-if="item.no_access === false" icon="tabler:rosette-discount-check" />&nbsp;{{ item.feature }}
                </li>
              </ul>
            </Card>

            <!-- Premium Plan -->
            <Card
              class="p-6 border rounded-2xl shadow-sm bg-gradient-to-bl from-yellow-600 to-yellow-700 text-white m-1">
              <h2 class="text-2xl font-bold mb-0">Premium <span class="text-xs">({{ (isUnlocked) ? 'All' :
                (form.counties?.length ?? '0') }} Counties)</span>
              </h2>
              <p class="text-3xl font-semibold mb-0 text-right">
                ${{ price }}<span class="text-base font-normal">/month</span>
                <span class="text-base font-normal block" v-show="!form.isMonthly">Billed annually</span>
              </p>
              <Button variant="default" class="mb-2 w-full text-black bg-white hover:bg-gray-100" @click="submit"
                :disabled="isUpgradeDisabled">
                <Icon v-if="processing" icon="tabler:loader-2" class="loading-icon text-lg" />
                {{
                  (getUser()?.user_type == UserTypes.FREE) ? 'Upgrade Your Plan' : 'Update Your Plan'
                }}
              </Button>
              <ul class="space-y-2 text-sm max-h-[250px] overflow-y-auto">
                <li class="cursor-pointer" @click="showDetailDialog(item.feature, item.description)"
                  v-for="item in featureValues.filter(f => f.group === UserTypes.PREMIUM)" :key="item.id">
                  <Icon class="inline-flex text-lg" :style="{ 'color': 'greenyellow' }" v-if="item.no_access === false"
                    icon="tabler:rosette-discount-check" />&nbsp;{{ item.feature }}
                </li>
              </ul>
            </Card>
          </div>
        </div>
      </div>
      <!--      <DialogFooter class="gap-2">-->
      <!--        <DialogClose as-child>-->
      <!--          <Button variant="secondary">Cancel</Button>-->
      <!--        </DialogClose>-->
      <!--      </DialogFooter>-->
    </DialogContent>
  </Dialog>
  <Dialog v-if="detailDialogData.open" v-model:open="detailDialogData.open">
    <DialogContent>
      <DialogHeader class="space-y-3">
        <DialogTitle class="text-center text-2xl">{{ detailDialogData.title }}</DialogTitle>
      </DialogHeader>
      <div class="w-full p-4">
        <p class="text-sm">{{ detailDialogData.description }}</p>
      </div>
    </DialogContent>
  </Dialog>

</template>
