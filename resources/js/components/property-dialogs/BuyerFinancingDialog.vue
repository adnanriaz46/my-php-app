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
import {computed, onMounted, ref, watch} from "vue";

import {useToast} from '@/composables/useToast'
import {DBApiPropertyFull} from "@/types/DBApi";
import {User} from "@/types";
import {BuyerFinancingFormData} from "@/types/property";
import {getBuyerFinancingFormData} from "@/lib/DataUtil";
import {Icon} from '@iconify/vue';
import {
    StepperIndicator,
    StepperItem,
    StepperRoot,
    StepperSeparator,
    StepperTitle,
} from "reka-ui";
import Toast from "@/components/ui/toast/Toast.vue";

const {showToast} = useToast()

const page = usePage();

watch(() => page.props?.success, (msg) => {
    if (msg) {
        showToast('Buyer Financing', msg, 'success')
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

const currentStep = ref<number>(1);

const form = useForm({
    propertyId: propertyId,// required
    propertyAddress: propertyAddress,// required
    step1: '',// required
    step2: '', // required
    step3: '',// required
    email: user.email,// required
    firstName: user.first_name,// required
    lastName: user.last_name,// required
    phone: user.phone_number,// required
});

// Sync child state back to parent
watch(dialogOpen, (newVal) => {
    emit('update:open', newVal);
});

// Sync prop changes to local state
watch(() => props.open, (newVal) => {
    dialogOpen.value = newVal;
});

const totalSteps = [
    {
        step: 1,
        title: 'Investment Stage',
        icon: 'tabler:map-route',
    },
    {
        step: 2,
        title: 'Close Period',
        icon: 'tabler:contract',
    },
    {
        step: 3,
        title: 'Loan Type',
        icon: 'tabler:building-bank',
    },
    {
        step: 4,
        title: 'Your Info',
        icon: 'tabler:user-square-rounded',
    },

];

onMounted(() => {
    fetchInitialData();
})
const formLabelData = ref<BuyerFinancingFormData>();

const fetchInitialData = async () => {
    const res = await getBuyerFinancingFormData();
    if (res.data) {
        formLabelData.value = res.data;
    }
}
const onStepClicked = async (value: string, step: number) => {
    if (step == 1) {
        form.step1 = value
    } else if (step == 2) {
        form.step2 = value
    } else if (step == 3) {
        form.step3 = value
    }
}

const isNextButtonDisabled = computed(() => {
    if (currentStep.value === 1) {
        return form.step1 === '';
    } else if (currentStep.value === 2) {
        return form.step2 === '';
    } else if (currentStep.value === 3) {
        return form.step3 === '';
    } else if (currentStep.value === 5) {
        return (
            form.firstName === '' ||
            form.lastName === '' ||
            form.email === '' ||
            form.phone === ''
        );
    }
    return false;
});

const submit = () => {
    form.post(route('property.request.buyer_financing'), {
        preserveScroll: true,
        onSuccess: () => {
            currentStep.value++;
        },
        onError: (errors: Record<string, string[]>) => {
            const messages = Object.values(errors).flat().join(', ');
            showToast('Buyer Financing', messages, 'error')
        }
    })
};
</script>
<template>
    <Dialog v-model:open="dialogOpen">
        <DialogContent>
            <Toast/>
            <DialogHeader class="space-y-3">
                <DialogTitle>
                    Get Financing
                </DialogTitle>
                <DialogDescription>
                    {{ propertyAddress }}
                </DialogDescription>
            </DialogHeader>
            <div class="w-full grid grid-cols-1 justify-center gap-4 max-h-[calc(100dvh-220px)] overflow-y-auto">
                <div class="mb-2 h-[50px]" v-if="currentStep < 5">
                    <StepperRoot
                        :default-value="1"
                        class="flex gap-2 w-full max-w-[32rem]"
                    >
                        <StepperItem
                            v-for="item in totalSteps"
                            :key="item.step"
                            class="w-full flex justify-center gap-2 cursor-pointer group relative px-4"
                            :step="item.step"
                        >
                            <StepperIndicator>
                                <Icon
                                    :icon="item.icon"
                                    class="size-7"
                                    :class="item.step <= currentStep ? ` text-primary-strong ` : `text-stone-700/50 dark:text-white/50`"
                                />
                            </StepperIndicator>

                            <StepperSeparator
                                v-if="item.step < 4"
                                class="absolute block top-5 left-[calc(50%+30px)] right-[calc(-50%+20px)] h-0.5 rounded-full  shrink-0"
                                :class="item.step < currentStep ? ` bg-primary-strong ` : `bg-stone-300/50`"
                            />
                            <div
                                class="absolute text-center top-full left-0 w-full mt-2 "
                                :class="item.step <= currentStep ? ` text-primary-strong ` : `text-stone-700/50 dark:text-white/50`"
                            >
                                <StepperTitle class="font-normal text-sm">
                                    {{ item.title }}
                                </StepperTitle>
                            </div>
                        </StepperItem>
                    </StepperRoot>
                </div>

                <div class="mt-4 mb-2 text-lg text-center w-full font-medium">
                    <span v-if="currentStep == 1">Where are you in your investment purchase journey?</span>
                    <span v-else-if="currentStep == 2">When are you looking to close?</span>
                    <span v-else-if="currentStep == 3">What type of loan are you interested in?</span>
                    <span v-else-if="currentStep == 4">Contact Info</span>
                    <span
                        v-else-if="currentStep == 5">Our lending partner will be in touch soon via email and/or phone. </span>
                </div>

                <div class="w-full flex flex-col gap-3 mb-3" v-if="currentStep == 1">
                    <div class="flex gap-1 cursor-pointer font-normal" @click.stop="onStepClicked(fm, 1)"
                         v-for="(fm, index) in formLabelData?.step1"
                         :key="index">
                        <Icon class="size-6 text-primary-strong" v-if="fm == form.step1"
                              icon="tabler:circle-check-filled"/>
                        <Icon class="size-6" v-else icon="tabler:circle"/>
                        {{ fm }}
                    </div>
                </div>
                <div class="w-full flex flex-col gap-3 my-6" v-if="currentStep == 2">
                    <div class="flex gap-1 cursor-pointer font-normal" @click.stop="onStepClicked(fm, 2)"
                         v-for="(fm, index) in formLabelData?.step2"
                         :key="index">
                        <Icon class="size-6 text-primary-strong" v-if="fm == form.step2"
                              icon="tabler:circle-check-filled"/>
                        <Icon class="size-6" v-else icon="tabler:circle"/>
                        {{ fm }}
                    </div>
                </div>
                <div class="w-full flex flex-col gap-3 my-6" v-if="currentStep == 3">
                    <div class="flex gap-1 cursor-pointer font-normal" @click.stop="onStepClicked(fm, 3)"
                         v-for="(fm, index) in formLabelData?.step3"
                         :key="index">
                        <Icon class="size-6 text-primary-strong" v-if="fm == form.step3"
                              icon="tabler:circle-check-filled"/>
                        <Icon class="size-6" v-else icon="tabler:circle"/>
                        {{ fm }}
                    </div>
                </div>
                <div class="w-full flex flex-col gap-3 my-6" v-if="currentStep == 4">
                    <div class="mb-1">
                        {{ formLabelData?.step4[0] }}
                    </div>
                    <div class="grid gap-2">
                        <Label :line-break="true">
                            First Name
                            <Input placeholder="Your first name here" v-model="form.firstName"/>
                            <InputError :message="form.errors.firstName"/>
                        </Label>
                        <Label :line-break="true">
                            Last Name
                            <Input placeholder="Your last name here" v-model="form.lastName"/>
                            <InputError :message="form.errors.lastName"/>
                        </Label>
                        <Label :line-break="true">
                            Email
                            <Input type="email" placeholder="Your email here" v-model="form.email"/>
                            <InputError :message="form.errors.email"/>
                        </Label>
                        <Label :line-break="true">
                            Phone
                            <Input type="tel" placeholder="Your phone number here" v-model="form.phone"/>
                            <InputError :message="form.errors.phone"/>
                        </Label>
                    </div>
                </div>

                <div class="w-full flex flex-col gap-3 my-6" v-if="currentStep == 5">

                    <img alt="done"
                         src="//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1723749791049x586808121993557500/mission-complete-spongebob.gif"
                         class="rounded-lg m-3">

                    <DialogClose>
                        <Button variant="default">Back to Deal Hunting</Button>
                    </DialogClose>

                </div>

            </div>
            <DialogFooter class="gap-2">
                <Button @click.stop="currentStep--" variant="secondary" v-if="currentStep > 1">Back</Button>
                <Button @click.stop="currentStep++" :disabled="isNextButtonDisabled" variant="default"
                        v-if="currentStep < 4">
                    Next
                </Button>
                <Button v-if="currentStep == 4" @click="submit" variant="default" :disabled="form.processing">
                    Submit
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

</template>

<style scoped>

</style>
