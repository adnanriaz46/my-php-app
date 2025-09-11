<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { router, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { SwitchRoot, SwitchThumb } from "reka-ui";
import { Icon } from '@iconify/vue';
import axios from "axios";
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import Label from "@/components/ui/label/Label.vue";
import Combobox from "@/components/ui/combobox/Combobox.vue";
import { getUser, isPremiumUser } from "@/composables/useUser";
import { UpgradeFeature } from "@/types";
import { UserTypes } from "@/types/userTypes";
import { useToast } from "@/composables/useToast";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";

interface DetailDialogAttr {
    open: boolean;
    title: string;
    description: string;
}

const processing = ref(false);
const detailDialogData = ref<DetailDialogAttr>({
    open: false,
    title: '',
    description: ''
});

const featureValues = ref<UpgradeFeature[]>([]);
const counties = ref([]);
const error = ref('');
const { showToast } = useToast();

const stripeMonthly = import.meta.env.VITE_STRIPE_MONTHLY;
const stripeYearly = import.meta.env.VITE_STRIPE_YEARLY;

const price = ref(0);

const fetchCounties = async () => {
    processing.value = true;
    try {
        const response = await axios.get(route('get.data.combobox_counties'));
        counties.value = response.data;
    } catch (err) {
        error.value = 'Failed to load counties';
        console.error(err);
    } finally {
        processing.value = false;
    }
};

const fetchFeatures = async () => {
    processing.value = true;
    try {
        const response = await axios.get(route('get.data.upgrade_features'));
        featureValues.value = response.data;
    } catch (err) {
        error.value = 'Failed to load features';
        console.error(err);
    } finally {
        processing.value = false;
    }
};

onMounted(async () => {
    await Promise.all([fetchCounties(), fetchFeatures()]);
    if (form.counties.length === 0) {
        form.counties = ['Delaware, PA'];
    }
    getPrice();
});

const form = useForm({
    counties: getUser()?.subscribed_counties as string[] ?? [],
    isMonthly: getUser()?.subscription_period_monthly as boolean ?? false
});

const isUnlocked = computed(() => {
    return form.counties?.length >= 10;
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
};

watch([() => form.counties, () => form.isMonthly], () => {
    getPrice();
});

const isUpgradeDisabled = computed(() => {
    return !form.counties?.length || processing.value;
});

const isDowngradeDisabled = computed(() => {
    return getUser()?.user_type == UserTypes.FREE || processing.value;
});

const showDetailDialog = (title: string, description: string) => {
    detailDialogData.value.title = title;
    detailDialogData.value.description = description;
    detailDialogData.value.open = true;
};

const submit = async () => {
    if (!getUser()) {
        router.visit(route('login'));
        showToast('Login Required', 'You should logged in to proceed!', 'warning');
        return;
    }

    // if (getUser()?.user_type == UserTypes.ADMIN) {
    //     alert('Admin could not subscribe to a plan, Please create non-admin account');
    //     return false;
    // }

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
    } catch (e: any) {
        console.error(e);
        if (e?.response?.data?.message) {
            alert('ERROR: ' + e?.response?.data?.message);
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
        showToast('Login Required', 'You should logged in to proceed!', 'warning');
        return;
    }

    // if (getUser()?.user_type == UserTypes.ADMIN) {
    //     alert('Admin could not subscribe to a plan, Please create non-admin account');
    //     return false;
    // }

    processing.value = true;
    try {
        const res = await axios.post(route('subscription.cancel'));
        if (res.data?.success) {
            alert(res.data.success);
            window.location.href = route('profile.edit');
        } else {
            alert("ERROR: " + res.data?.error);
        }
    } catch (e: any) {
        console.error(e);
        alert(e?.response?.data?.error || e?.message || 'Cancellation failed');
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <GuestAppLayout>
        <!-- Hero Section -->
        <div class="relative bg-white dark:bg-gray-900 py-8 px-4">
            <div class="max-w-7xl mx-auto text-center">
                <p
                    class="text-2xl md:text-4xl font-bold text-gray-600 dark:text-gray-300 mb-2 max-w-4xl mx-auto leading-relaxed">
                    🚀Rocket fuel for your investment portfolio. 🚀
                </p>
            </div>
        </div>

        <!-- Pricing Section -->
        <div class="pb-10 pt-5 bg-white dark:bg-gray-900 p-4">
            <div class="max-w-6xl mx-auto">
                <!-- Billing Toggle -->
                <div class="flex justify-center mb-2">
                    <div class="flex gap-2 items-center">
                        <label
                            class="text-stone-700 dark:text-white leading-none select-none cursor-pointer hover:bg-primary/50 px-4 py-2 rounded-lg text-center"
                            :class="{ 'bg-primary ': !form.isMonthly }" for="billing-toggle">
                            <span class="font-semibold relative block dark:text-white">
                                <span
                                    class="inline-flex px-2 py-0.5 text-xs font-normal rounded-full shadow-sm text-white bg-green-800 absolute bottom-5 right-[-5px] w-[110px] text-center">
                                    Save up to 25%
                                </span>
                                Yearly
                            </span>
                        </label>
                        <SwitchRoot id="billing-toggle" v-model="form.isMonthly"
                            class="w-[32px] h-[20px] cursor-pointer shadow-sm flex data-[state=unchecked]:bg-stone-300 data-[state=checked]:bg-stone-800 dark:data-[state=unchecked]:bg-stone-800 dark:data-[state=checked]:bg-stone-700 border border-stone-300 data-[state=checked]:border-stone-700 dark:border-stone-700 rounded-full relative transition-[background] focus-within:outline-none focus-within:shadow-[0_0_0_1px] focus-within:border-stone-800 focus-within:shadow-stone-800">
                            <SwitchThumb
                                class="w-3.5 h-3.5 my-auto bg-white text-xs flex items-center justify-center shadow-xl rounded-full transition-transform translate-x-0.5 will-change-transform data-[state=checked]:translate-x-full" />
                        </SwitchRoot>

                        <label
                            class="text-stone-700 dark:text-white leading-none select-none cursor-pointer hover:bg-primary/50 px-4 py-2 rounded-lg text-center"
                            :class="{ 'bg-primary ': form.isMonthly }" for="billing-toggle">
                            <span class="font-semibold">Monthly</span>
                        </label>
                    </div>
                </div>

                <!-- County Selection -->
                <div class="max-w-2xl mx-auto mb-5">
                    <Label for="preferred-counties" class="text-lg font-semibold">
                        Counties ({{ (isUnlocked) ? '10+' : form.counties?.length }})
                        <span v-if="isUnlocked"
                            class="inline-flex px-2 py-0.5 text-xs font-normal rounded-full shadow-sm text-white bg-green-800 ml-2">
                            <Icon icon="tabler:lock-open" :height="16" />&nbsp;All Counties
                        </span>
                    </Label>
                    <Combobox id="preferred-counties" :option-values="counties" v-model="form.counties" class="mt-2" />
                    <p class="text-muted-foreground text-sm mt-2">
                        * After 10 counties, you automatically unlock ALL data at no additional cost
                    </p>
                </div>

                <!-- Pricing Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-3xl mx-auto">
                    <!-- Basic Plan -->
                    <Card class="gap-1 p-8 border rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="text-center mb-3">
                            <h2 class="text-3xl font-bold mb-2">Basic
                                <span class="text-sm text-muted-foreground block mt-1">
                                    (0 Counties)
                                </span>
                            </h2>
                            <p class="text-4xl font-semibold mb-2">$0<span class="text-lg font-normal">/month</span></p>
                            <p class="text-gray-600 dark:text-gray-400">Perfect for getting started</p>
                        </div>

                        <div class="mb-3">
                            <Button variant="outline" class="w-full mb-1" :disabled="isDowngradeDisabled"
                                @click="downgrade" v-if="isPremiumUser(getUser()?.user_type)">
                                <Icon v-if="processing" icon="tabler:loader-2" class="loading-icon text-lg mr-2" />
                                Downgrade Your Plan
                            </Button>
                            <Button variant="outline" class="w-full mb-1" :disabled="true"
                                v-if="!isPremiumUser(getUser()?.user_type)">
                                Your current plan
                            </Button>
                        </div>

                        <ul class="space-y-0 text-base max-h-[400px] overflow-y-auto">
                            <li class="cursor-pointer flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800"
                                @click="showDetailDialog(item.feature, item.description)"
                                v-for="item in featureValues.filter(f => f.group === UserTypes.FREE)" :key="item.id">
                                <Icon class="text-lg" :style="{ 'color': 'red' }" v-if="item.no_access"
                                    icon="tabler:ban" />
                                <Icon class="text-lg text-green-600 dark:text-[yellowgreen]"
                                    v-if="item.no_access === false" icon="tabler:rosette-discount-check" />
                                <span>{{ item.feature }}</span>
                            </li>
                        </ul>
                    </Card>

                    <!-- Premium Plan -->
                    <Card
                        class="p-8 gap-1 border rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-gradient-to-bl from-yellow-600 to-yellow-700 text-white relative overflow-hidden">
                        <div class="absolute top-4 right-4">
                            <span
                                class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-white text-yellow-700">
                                Most Popular
                            </span>
                        </div>

                        <div class="text-center mb-2.5">
                            <h2 class="text-3xl font-bold mb-2">
                                Premium
                                <span class="text-sm block mt-1">
                                    ({{ (isUnlocked) ? 'All' : (form.counties?.length ?? '0') }} Counties)
                                </span>
                            </h2>
                            <p class="text-4xl font-semibold mb-2">
                                ${{ price }}<span class="text-lg font-normal">/month</span>
                                <span class="text-lg font-normal block"
                                    :style="{ visibility: form.isMonthly ? 'hidden' : 'visible' }">Billed
                                    annually</span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <Button variant="default" class="w-full mb-4 text-black bg-white hover:bg-gray-100"
                                @click="submit" :disabled="isUpgradeDisabled">
                                <Icon v-if="processing" icon="tabler:loader-2" class="loading-icon text-lg mr-2" />
                                {{ (getUser()?.user_type == UserTypes.FREE) ? 'Upgrade Your Plan' : 'Update Your Plan'
                                }}
                            </Button>
                        </div> 

                        <ul class="space-y-0 text-base max-h-[400px] overflow-y-auto">
                            <li class="cursor-pointer flex items-center gap-2 p-2 rounded-lg hover:bg-yellow-600/20"
                                @click="showDetailDialog(item.feature, item.description)"
                                v-for="item in featureValues.filter(f => f.group === UserTypes.PREMIUM)" :key="item.id">
                                <Icon class="text-lg text-greenyellow" v-if="item.no_access === false"
                                    icon="tabler:rosette-discount-check" />
                                <span>{{ item.feature }}</span>
                            </li>
                        </ul>
                    </Card>
                </div>

                <!-- FAQ Section -->
                <div class="mt-20 text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Frequently Asked Questions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                        <div class="text-left">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Can I change my plan anytime?
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Yes, you can upgrade or downgrade your
                                plan at any time. Changes take effect immediately.</p>
                        </div>
                        <div class="text-left">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">What happens after 10 counties?
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">You automatically unlock access to ALL
                                counties at no additional cost. No need to upgrade further.</p>
                        </div>
                        <div class="text-left">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Is there a free trial?</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Yes, you can start with our Basic plan
                                for free and upgrade when you're ready for more features.</p>
                        </div>
                        <div class="text-left">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Can I cancel anytime?</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Absolutely. You can cancel your
                                subscription at any time and continue using the service until the end of your billing
                                period.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Detail Dialog -->
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
    </GuestAppLayout>
</template>

<style scoped>
.loading-icon {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>
