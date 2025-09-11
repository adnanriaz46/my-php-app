<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import Input from '@/components/ui/input/Input.vue';
import InputError from '@/components/InputError.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import Combobox from '@/components/ui/combobox/Combobox.vue';
import Textarea from '@/components/ui/textarea/TextArea.vue';
import { Icon } from '@iconify/vue';
import InputNumber from '@/components/ui/input-number/InputNumber.vue';
import { ProgressIndicator, ProgressRoot } from 'reka-ui';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

interface Props {
    assessment?: any;
    investmentStrategies: string[];
    counties: string[];
    isAuthenticated: boolean;
    user?: {
        first_name: string;
        last_name: string;
        email: string;
        phone_number: string;
    };
    nextStep?: number | null;
    error?: string | null;
    success?: string | null;
}

const props = defineProps<Props>();

const currentStep = ref(1); // TODO: Change to 1 if not - testing
const totalSteps = 19;
const isLoading = ref(false);
const showLoadingStep = ref(false);

// Form data for all steps
const form = useForm({
    // Step 1: User Information
    first_name: props.user?.first_name || props.assessment?.first_name || '',
    last_name: props.user?.last_name || props.assessment?.last_name || '',
    email: props.user?.email || props.assessment?.email || '',
    phone: props.user?.phone_number || props.assessment?.phone || '',

    // Step 2: Investment Strategy
    investment_strategy: props.assessment?.investment_strategy || [],

    // Step 3: Counties
    counties_invest: props.assessment?.counties_invest || [],

    // Step 4: Experience
    how_long_in_rei: props.assessment?.how_long_in_rei || '',

    // Step 5: Deal Counts
    how_many_agent_deals: props.assessment?.how_many_agent_deals || 0,
    how_many_flips: props.assessment?.how_many_flips || 0,
    how_many_land: props.assessment?.how_many_land || 0,
    how_many_new_construction: props.assessment?.how_many_new_construction || 0,
    how_many_private_lending: props.assessment?.how_many_private_lending || 0,
    how_many_rentals: props.assessment?.how_many_rentals || 0,
    how_many_wholesale: props.assessment?.how_many_wholesale || 0,

    // Step 6: Other Investments
    other_investments_yn: props.assessment?.other_investments_yn || false,
    other_investments_detail: props.assessment?.other_investments_detail || '',

    // Step 7-9: Goals and Plans
    primary_goal: props.assessment?.primary_goal || '',
    investment_biz_plan: props.assessment?.investment_biz_plan || '',
    smart_goals: props.assessment?.smart_goals || '',

    // Step 10-11: Obstacles and Strategy
    main_obstacle: props.assessment?.main_obstacle || '',
    acq_strategy: props.assessment?.acq_strategy || [],

    // Step 12-13: Time Management
    time_per_week_actual: props.assessment?.time_per_week_actual || '',
    time_per_week_goal: props.assessment?.time_per_week_goal || '',

    // Step 14: Pillar Rankings
    pillar_ranking_finance: props.assessment?.pillar_ranking_finance || 0,
    pillar_ranking_leadership: props.assessment?.pillar_ranking_leadership || 0,
    pillar_ranking_marketing: props.assessment?.pillar_ranking_marketing || 0,
    pillar_ranking_operations: props.assessment?.pillar_ranking_operations || 0,
    pillar_ranking_sales: props.assessment?.pillar_ranking_sales || 0,

    // Step 15-16: Financial Information
    average_deal_profit: props.assessment?.average_deal_profit || '',
    what_would_you_pay: props.assessment?.what_would_you_pay || '',

    // Step 17: Preferences
    want_auto_offers: props.assessment?.want_auto_offers || false,

    // Step 18: Additional Comments
    additional_comments: props.assessment?.additional_comments || '',

    // Additional
    step: props.nextStep || null,
});

// Experience options
const experienceOptions = [
    'Don\'t Have',
    '<1 Year',
    '1-2 Years',
    '3-5 Years',
    '5-10 Years',
    '10+ Years'
];

// Time options
const timeOptions = [
    '<1 Hour',
    '1-2 Hours',
    '2-5 Hours',
    '5-10 Hours',
    '10+ Hours'
];

// Deal types for step 5
const dealTypes = [
    { key: 'how_many_rentals', label: 'Rentals' },
    { key: 'how_many_flips', label: 'Flips' },
    { key: 'how_many_wholesale', label: 'Wholesale' },
    { key: 'how_many_land', label: 'Land' },
    { key: 'how_many_new_construction', label: 'New Construction' },
    { key: 'how_many_agent_deals', label: 'Agent Deals' },
    { key: 'how_many_private_lending', label: 'Private Lending' },
];

// Pillar options for step 14
const pillarOptions = [
    { key: 'pillar_ranking_marketing', label: 'Marketing' },
    { key: 'pillar_ranking_sales', label: 'Sales' },
    { key: 'pillar_ranking_operations', label: 'Operations' },
    { key: 'pillar_ranking_finance', label: 'Finance' },
    { key: 'pillar_ranking_leadership', label: 'Leadership' },
];

// Computed properties
const progressPercentage = computed(() => {
    return Math.round((currentStep.value / totalSteps) * 100);
});

const canGoNext = computed(() => {
    return isStepValid(currentStep.value);
});

const canGoBack = computed(() => {
    return currentStep.value > 1;
});

// Validation functions
function isStepValid(step: number): boolean {
    switch (step) {
        case 1:
            return form.first_name && form.last_name && form.email && form.phone;
        case 2:
            return form.investment_strategy.length > 0;
        case 3:
            return form.counties_invest.length > 0;
        case 4:
            return form.how_long_in_rei !== '';
        case 5:
            return true; // All fields have defaults
        case 6:
            return form.other_investments_yn !== null;
        case 7:
            return form.primary_goal.trim() !== '';
        case 8:
            return form.investment_biz_plan.trim() !== '';
        case 9:
            return form.smart_goals.trim() !== '';
        case 10:
            return form.main_obstacle.trim() !== '';
        case 11:
            return form.acq_strategy.length > 0;
        case 12:
            return form.time_per_week_actual !== '';
        case 13:
            return form.time_per_week_goal !== '';
        case 14:
            return form.pillar_ranking_finance >= 0 && form.pillar_ranking_leadership >= 0 &&
                form.pillar_ranking_marketing >= 0 && form.pillar_ranking_operations >= 0 &&
                form.pillar_ranking_sales >= 0;
        case 15:
            return form.average_deal_profit !== '';
        case 16:
            return form.what_would_you_pay !== '';
        case 17:
            return form.want_auto_offers !== null;
        case 18:
            return true; // Optional field
        default:
            return true;
    }
}
const isAiDripGenerationLoading = ref(false);
// Navigation functions
function onNextStep() {
    if (!canGoNext.value) return;

    if (currentStep.value === 19) {
        return;
    } else {
        isLoading.value = true;
        // Save current step data
        form.step = currentStep.value;
        form.post(route('assessment.save_step'), {
            onSuccess: (data) => {
                if (data?.props?.nextStep) {
                    currentStep.value = data.props.nextStep as number;
                }
                if (currentStep.value === 19) {
                    completeAssessment();
                }
            },
            onFinish: () => {
                // The redirect will handle the step progression
                isLoading.value = false;
            },
            preserveScroll: true,
            preserveState: true
        });
    }
}

function previousStep() {
    if (canGoBack.value) {
        currentStep.value--;
    }
}

function completeAssessment() {
    isAiDripGenerationLoading.value = true;
    setTimeout(() => {
        form.post(route('assessment.complete'), {
            onFinish: () => {
                // The redirect will handle the completion
                // isLoading.value = false;
                isAiDripGenerationLoading.value = false;
            }
        });
    }, 1000)

    // isLoading.value = true;

}
const isSmartGoalsLoading = ref(false);
const smartGoalsMessages = ref<{ role: string, content: string, hidden?: boolean }[]>([]);
const resetSmartGoals = () => {
    smartGoalsMessages.value = [
        {
            role: 'system',
            content: 'You are a helpful assistant that can help with the assessment.',
            hidden: true
        }
    ]
}
const useAiForSmartGoals = async () => {
    resetSmartGoals();
    isSmartGoalsLoading.value = true;
    try {
        const prompt = `I am going to give you text in the second prompt. The text was written as investment goals. I want you to apply the "S.M.A.R.T." 
    framework to revise and re-write the goals to be better and within the boundaries of the S.M.A.R.T. goals. Your response should not include a 
    lot of explanation. The response should simply be the revised version, according to the SMART framework in an easy to read format.
    The response should be in the same language as the text you are given.
`;

        smartGoalsMessages.value.push({
            role: 'user',
            content: prompt.trim(),
            hidden: true
        })
        smartGoalsMessages.value.push({
            role: 'user',
            content: form.investment_biz_plan.trim()
        });

        const response = await axios.post(route('assessment.use_ai_for_smart_goals'), {
            messages: smartGoalsMessages.value
        });

        console.log(response.data.choices[0].message.content);
        smartGoalsMessages.value.push({
            role: 'assistant',
            content: response.data.choices[0].message.content
        });

        isSmartGoalsLoading.value = false;
    } catch (error) {
        console.error(error);
        isSmartGoalsLoading.value = false;
    }

}

// function getStepData(step: number) {
//     switch (step) {
//         case 1:
//             return {
//                 first_name: form.first_name,
//                 last_name: form.last_name,
//                 email: form.email,
//                 phone: form.phone,
//             };
//         case 2:
//             return { investment_strategy: form.investment_strategy };
//         case 3:
//             return { counties_invest: form.counties_invest };
//         case 4:
//             return { how_long_in_rei: form.how_long_in_rei };
//         case 5:
//             return {
//                 how_many_agent_deals: form.how_many_agent_deals,
//                 how_many_flips: form.how_many_flips,
//                 how_many_land: form.how_many_land,
//                 how_many_new_construction: form.how_many_new_construction,
//                 how_many_private_lending: form.how_many_private_lending,
//                 how_many_rentals: form.how_many_rentals,
//                 how_many_wholesale: form.how_many_wholesale,
//             };
//         case 6:
//             return {
//                 other_investments_yn: form.other_investments_yn,
//                 other_investments_detail: form.other_investments_detail,
//             };
//         case 7:
//             return { primary_goal: form.primary_goal };
//         case 8:
//             return { investment_biz_plan: form.investment_biz_plan };
//         case 9:
//             return { smart_goals: form.smart_goals };
//         case 10:
//             return { main_obstacle: form.main_obstacle };
//         case 11:
//             return { acq_strategy: form.acq_strategy };
//         case 12:
//             return { time_per_week_actual: form.time_per_week_actual };
//         case 13:
//             return { time_per_week_goal: form.time_per_week_goal };
//         case 14:
//             return {
//                 pillar_ranking_finance: form.pillar_ranking_finance,
//                 pillar_ranking_leadership: form.pillar_ranking_leadership,
//                 pillar_ranking_marketing: form.pillar_ranking_marketing,
//                 pillar_ranking_operations: form.pillar_ranking_operations,
//                 pillar_ranking_sales: form.pillar_ranking_sales,
//             };
//         case 15:
//             return { average_deal_profit: form.average_deal_profit };
//         case 16:
//             return { what_would_you_pay: form.what_would_you_pay };
//         case 17:
//             return { want_auto_offers: form.want_auto_offers };
//         case 18:
//             return { additional_comments: form.additional_comments };
//         default:
//             return {};
//     }
// }

// Watch for form changes to clear errors
watch(() => form.errors, (errors) => {
    if (Object.keys(errors).length > 0) {
        // Clear errors after a delay
        setTimeout(() => {
            form.clearErrors();
        }, 3000);
    }
});
</script>

<template>
    <GuestAppLayout class="bg-gray-100 dark:bg-gray-900">
        <div class="min-h-[calc(100dvh-100px)] py-8">
            <div class="max-w-2xl mx-auto px-4">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Revamp REI <span
                            class="text-primary">Blueprint</span></h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300">Take the assessment now to get your custom
                        tailored blueprint.</p>
                </div>
                <div v-if="props.error" class="flex items-center justify-center bg-red-500/20 rounded-lg p-4">
                    <Icon icon="tabler:alert-circle" class="size-5 my-auto animate-pulse" />
                    {{ props.error }}

                </div>
                <div v-if="props.success" class="flex items-center justify-center bg-green-500/20 rounded-lg p-4">
                    <Icon icon="tabler:check" class="size-5 my-auto animate-pulse" />
                    {{ props.success }}
                </div>
                <!-- Assessment Form -->
                <div v-if="!showLoadingStep" class="bg-white dark:bg-gray-800 rounded-2xl border shadow-md p-8"
                    :class="isAiDripGenerationLoading ? 'bg-[url(/assets/ai-sparkle-loader.svg)] bg-cover bg-no-repeat' : ''">
                    <!-- Step 1: User Information -->
                    <InputError :message="form.errors?.step" />
                    <div v-if="currentStep === 1" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Take the assessment
                                now to get your custom tailored blueprint.</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="firstName">First Name</Label>
                                <Input id="firstName" v-model="form.first_name" type="text" required
                                    autocomplete="given-name" placeholder="First name" />
                                <InputError :message="form.errors.first_name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="lastName">Last Name</Label>
                                <Input id="lastName" v-model="form.last_name" type="text" required
                                    autocomplete="family-name" placeholder="Last name" />
                                <InputError :message="form.errors.last_name" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input id="email" v-model="form.email" type="email" required autocomplete="email"
                                    placeholder="Email address" />
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="space-y-2">
                                <Label for="phone">Phone</Label>
                                <Input id="phone" v-model="form.phone" type="tel" required autocomplete="tel"
                                    placeholder="Phone number" />
                                <InputError :message="form.errors.phone" />
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Investment Strategy -->
                    <div v-if="currentStep === 2" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">What is your
                                investment strategy?</h2>
                        </div>

                        <Combobox v-model="form.investment_strategy" :option-values="investmentStrategies"
                            class="mt-2" />
                        <InputError :message="form.errors.investment_strategy" />
                    </div>

                    <!-- Step 3: Counties -->
                    <div v-if="currentStep === 3" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">What Counties do you
                                purchase in?</h2>
                        </div>

                        <div>
                            <Label>Select your county, city, and/or zip</Label>
                            <Combobox v-model="form.counties_invest" :option-values="counties" class="mt-2"
                                allow-custom />
                            <InputError :message="form.errors.counties_invest" />
                        </div>
                    </div>

                    <!-- Step 4: Experience -->
                    <div v-if="currentStep === 4" class="space-y-6 mx-auto">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">How long have you been
                                investing in real estate?</h2>
                        </div>

                        <div class="space-y-3">
                            <div v-for="option in experienceOptions" :key="option" class="flex items-center">
                                <Label :for="option" class="ml-3 cursor-pointer dark:text-gray-200 text-base"
                                    @click="form.how_long_in_rei = option">
                                    <Icon
                                        :icon="option == form.how_long_in_rei ? 'tabler:square-rounded-check-filled' : 'tabler:square-rounded'"
                                        class="size-6 text-primary" />
                                    {{ option }}
                                </Label>
                            </div>
                        </div>
                        <InputError :message="form.errors.how_long_in_rei" />
                    </div>

                    <!-- Step 5: Deal Counts -->
                    <div v-if="currentStep === 5" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">How many deals have
                                you done? (Estimate is ok)</h2>
                        </div>

                        <div class="space-y-4">
                            <div v-for="dealType in dealTypes" :key="dealType.key"
                                class="flex items-center justify-between">
                                <Label class="flex-1 dark:text-gray-200">{{ dealType.label }}</Label>
                                <div class="flex items-center space-x-4">
                                    <input type="range" :min="0" :max="100" v-model="form[dealType.key]"
                                        class="w-32 h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer slider" />
                                    <Input v-model="form[dealType.key]" type="number" :min="0" :max="100"
                                        class="w-20" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: Other Investments -->
                    <div v-if="currentStep === 6" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Do you have other
                                investments?</h2>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 justify-center">


                                <div class="flex items-center">
                                    <input id="other_investments_yes" v-model="form.other_investments_yn" type="radio"
                                        :value="true" name="other_investments"
                                        class="h-4 w-4 accent-primary text-primary border-gray-300 dark:border-gray-600 focus:ring-primary dark:bg-gray-700" />
                                    <Label for="other_investments_yes"
                                        class="ml-3 cursor-pointer dark:text-gray-200 text-base">Yes</Label>
                                </div>
                                <div class="flex items-center">
                                    <input id="other_investments_no" v-model="form.other_investments_yn" type="radio"
                                        :value="false" name="other_investments"
                                        class="h-4 w-4 accent-primary text-primary border-gray-300 dark:border-gray-600 focus:ring-primary dark:bg-gray-700" />
                                    <Label for="other_investments_no"
                                        class="ml-3 cursor-pointer dark:text-gray-200 text-base">No</Label>
                                </div>
                            </div>

                            <div v-if="form.other_investments_yn" class="mt-4 space-y-2">
                                <Label for="other_investments_detail">What other types of investments do you have / have
                                    you done?</Label>
                                <Textarea id="other_investments_detail" v-model="form.other_investments_detail" rows="4"
                                    placeholder="Describe your other investments..." />
                                <InputError :message="form.errors.other_investments_detail" />
                            </div>
                        </div>
                        <InputError :message="form.errors.other_investments_yn" />
                    </div>

                    <!-- Step 7: Primary Goal -->
                    <div v-if="currentStep === 7" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">What is your primary
                                goal for investing in real estate?</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm text-center">
                            *Example - I want to replace my w2 income, retire my spouse, have time freedom, and leave
                            legacy wealth. *
                        </p>
                        <div>
                            <Textarea v-model="form.primary_goal" rows="6"
                                placeholder="Describe your primary goal..." />
                            <p
                                class="text-sm text-gray-700 dark:text-gray-200 mt-2 px-4 py-2 bg-yellow-500/20 rounded-lg">
                                NOTE: Be thoughtful and creative here. The more thorough your answer, the better our AI
                                model can craft your Blueprint.
                            </p>
                            <InputError :message="form.errors.primary_goal" />
                        </div>
                    </div>

                    <!-- Step 8: Investment Business Plan -->
                    <div v-if="currentStep === 8" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">In a few sentences,
                                summarize your investment strategy.</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm text-center">
                            Include asset type, purchase price range, rehab range, deals per year, exit strategies or
                            anything else notable.
                        </p>
                        <div>
                            <Textarea v-model="form.investment_biz_plan" rows="6"
                                placeholder="Summarize your investment strategy..." />
                            <p
                                class="text-sm text-gray-700 dark:text-gray-200 mt-2 px-4 py-2 bg-yellow-500/20 rounded-lg">
                                NOTE: Be thoughtful and creative here. The more thorough your answer, the better our AI
                                model can craft your Blueprint.
                            </p>
                            <InputError :message="form.errors.investment_biz_plan" />
                        </div>
                    </div>

                    <!-- Step 9: SMART Goals -->
                    <div v-if="currentStep === 9" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Let's make sure your
                                goals are SMART goals.</h2>
                            <p class="text-gray-600 dark:text-gray-300">Specific, Measurable, Achievable, Relevant,
                                Time-bound</p>
                        </div>
                        <div class="flex flex-col items-center space-y-6 mb-6" v-if="smartGoalsMessages.length <= 1">
                            <div class="text-left">
                                <div class="font-bold text-lg mb-2">S <span class="font-normal">- Specific</span></div>
                                <div class="font-bold text-lg mb-2">M <span class="font-normal">- Measurable</span>
                                </div>
                                <div class="font-bold text-lg mb-2">A <span class="font-normal">- Achievable</span>
                                </div>
                                <div class="font-bold text-lg mb-2">R <span class="font-normal">- Relevant</span></div>
                                <div class="font-bold text-lg">T <span class="font-normal">- Time-bound</span></div>
                            </div>
                            <div class="text-gray-700 dark:text-gray-200 text-base text-center max-w-xl">
                                <p>
                                    "SMART" goals is a framework designed to guide the setting of objectives for maximum
                                    success.<br>
                                    Each component of the acronym plays a crucial role in creating effective and
                                    actionable goals.
                                </p>
                            </div>
                            <Button type="button" variant="default" @click="useAiForSmartGoals">
                                Use AI to help !
                            </Button>
                        </div>
                        <template v-if="!isSmartGoalsLoading">
                            <div class="flex items-center space-x-2"
                                v-for="message in smartGoalsMessages.filter(message => !message.hidden)"
                                :key="message.content">
                                <Icon :icon="message.role === 'assistant' ? 'tabler:robot' : 'tabler:user'"
                                    class="size-4 min-w-[25px] min-h-[25px] my-auto" />
                                <span
                                    class="text-gray-700 dark:text-gray-200 text-sm px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-800 flex flex-col border border-gray-200 dark:border-gray-600">{{
                                        message.content }}
                                    <Button variant="filter" size="sm" @click="form.smart_goals = message.content"
                                        v-if="message.role === 'assistant'">Use this</Button>
                                    <Button variant="filter" size="sm" @click="useAiForSmartGoals"
                                        v-if="message.role === 'user'">Try Again</Button>
                                </span>
                            </div>
                        </template>
                        <div v-else class="flex items-center justify-center gap-2">
                            <Icon icon="tabler:loader" class="size-5 my-auto animate-spin" />
                            Loading...
                        </div>

                        <div>
                            <Textarea v-model="form.smart_goals" rows="6" placeholder="Write your SMART goals..." />
                            <InputError :message="form.errors.smart_goals" />
                        </div>
                    </div>

                    <!-- Step 10: Main Obstacle -->
                    <div v-if="currentStep === 10" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">What's stopping you?
                            </h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-lg text-center">
                            What do you think is the main obstacle holding you back from your REI goals?
                        </p>
                        <div>
                            <Textarea v-model="form.main_obstacle" rows="6"
                                placeholder="Describe the main obstacle holding you back..." />
                            <InputError :message="form.errors.main_obstacle" />
                        </div>
                    </div>

                    <!-- Step 11: Acquisition Strategy -->
                    <div v-if="currentStep === 11" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Describe your current
                                acquisition strategy.</h2>
                        </div>

                        <div>
                            <Label>How do you currently acquire properties?</Label>
                            <Combobox v-model="form.acq_strategy"
                                :option-values="['Revamp365.ai', 'MLS searches', 'Wholesalers', 'Agent Relations', 'Auctions', 'Sheriff Sales', 'Tax Sales', 'Direct to seller', 'Other']"
                                class="mt-2" allow-custom />
                            <InputError :message="form.errors.acq_strategy" />
                        </div>
                    </div>

                    <!-- Step 12: Time Per Week Actual -->
                    <div v-if="currentStep === 12" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">How much time per week
                                do you currently spend deal hunting?</h2>
                        </div>

                        <div class="space-y-3">
                            <div v-for="option in timeOptions" :key="option" class="flex items-center">
                                <Label :for="option" class="ml-3 cursor-pointer dark:text-gray-200 text-base"
                                    @click="form.time_per_week_actual = option">
                                    <Icon
                                        :icon="option == form.time_per_week_actual ? 'tabler:square-rounded-check-filled' : 'tabler:square-rounded'"
                                        class="size-6 text-primary" />
                                    {{ option }}
                                </Label>
                            </div>
                        </div>
                        <InputError :message="form.errors.time_per_week_actual" />
                    </div>

                    <!-- Step 13: Time Per Week Goal -->
                    <div v-if="currentStep === 13" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">How much time per week
                                do you THINK you need to spend to hit your goals?</h2>
                        </div>

                        <div class="space-y-3">


                            <div v-for="option in [...timeOptions, ...['20+ hours', 'I need a team']]" :key="option"
                                class="flex items-center">
                                <Label :for="option" class="ml-3 cursor-pointer dark:text-gray-200 text-base"
                                    @click="form.time_per_week_goal = option">
                                    <Icon
                                        :icon="option == form.time_per_week_goal ? 'tabler:square-rounded-check-filled' : 'tabler:square-rounded'"
                                        class="size-6 text-primary" />
                                    {{ option }}
                                </Label>
                            </div>
                        </div>
                        <InputError :message="form.errors.time_per_week_goal" />
                    </div>

                    <!-- Step 14: The 5 Pillars -->
                    <div v-if="currentStep === 14" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">The 5 Pillars</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-2 font-semibold text-base">We break down the
                                skills required to excel into 5 pillars.</p>
                            <p class="text-gray-600 dark:text-gray-300 text-sm font-light">How would you rate your
                                current business
                                on each pillar?</p>
                        </div>

                        <div class="space-y-4">
                            <div v-for="pillar in pillarOptions" :key="pillar.key"
                                class="flex items-center justify-between">
                                <Label class="flex-1 dark:text-gray-200">{{ pillar.label }}</Label>
                                <div class="flex items-center space-x-4">
                                    <input type="range" :min="0" :max="10" v-model="form[pillar.key]"
                                        class="w-32 h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer slider" />
                                    <Input v-model="form[pillar.key]" type="number" :min="0" :max="10" class="w-16" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 15: Average Deal Profit -->
                    <div v-if="currentStep === 15" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">What is your average
                                deal worth in terms of profit?</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-base font-light">Example 1 - your average flip
                            nets 40k,
                            that
                            deal is worth 40k to you. </p>
                        <p class="text-gray-600 dark:text-gray-300 text-base font-light">Example 2 - your average rental
                            yields
                            6k/yr
                            in cash flow and 6k/yr in appreciation, and 3k/yr in debt paydown, hold time of 10 years,
                            that deal is worth 150k to you.</p>
                        <div>
                            <Label for="average_deal_profit">Average Deal Profit ($)</Label>
                            <InputNumber type="price" id="average_deal_profit" v-model="form.average_deal_profit"
                                :min="0" :step="100" placeholder="40000" />
                            <InputError :message="form.errors.average_deal_profit" />
                        </div>
                    </div>

                    <!-- Step 16: What's That Worth -->
                    <div v-if="currentStep === 16" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">What's that worth?
                            </h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-base font-light">If you could add just ONE deal
                            per month
                            to your current operation, how much would you pay for that opportunity?</p>

                        <div>
                            <Label for="what_would_you_pay">Value ($)</Label>
                            <InputNumber type="price" id="what_would_you_pay" v-model="form.what_would_you_pay" :min="0"
                                :step="10" placeholder="10000" />
                            <InputError :message="form.errors.what_would_you_pay" />
                        </div>
                    </div>

                    <!-- Step 17: Automated Offers -->
                    <div v-if="currentStep === 17" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Automated Offers</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-base font-light">We're in the final testing
                            stages with our "automated offers" system that allows you to send fully executed agreements
                            in bulk with a click of a button. </p>
                        <p class="text-gray-600 dark:text-gray-300 text-base font-light">Through testing, we have
                            "accidentally"
                            closed over $100k across 3 deals. </p>
                        <p class="text-gray-600 dark:text-gray-300 text-base font-light">Are you interested in joining
                            the initial
                            public release?</p>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-4 justify-center">
                                <div class="flex items-center">
                                    <input id="auto_offers_yes" v-model="form.want_auto_offers" type="radio"
                                        :value="true" name="auto_offers"
                                        class="h-4 w-4 text-primary border-gray-300 dark:border-gray-600 focus:ring-primary dark:bg-gray-700 accent-primary" />
                                    <Label for="auto_offers_yes"
                                        class="ml-3 cursor-pointer dark:text-gray-200 text-base">Yes</Label>
                                </div>
                                <div class="flex items-center">
                                    <input id="auto_offers_no" v-model="form.want_auto_offers" type="radio"
                                        :value="false" name="auto_offers"
                                        class="h-4 w-4 text-primary border-gray-300 dark:border-gray-600 focus:ring-primary dark:bg-gray-700 accent-primary" />
                                    <Label for="auto_offers_no"
                                        class="ml-3 cursor-pointer dark:text-gray-200 text-base">No
                                        Thanks</Label>
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.want_auto_offers" />
                    </div>

                    <!-- Step 18: Additional Comments -->
                    <div v-if="currentStep === 18" class="space-y-6">
                        <div class="text-center">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">You are AWESOME!</h2>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-base font-light">You're all done the assessment.
                            Congrats! You'll get your results via email shortly, keep an eye out! Unrelated to the
                            assessment, is there anything else you'd like to share with the Revamp team? </p>
                        <div>
                            <Label for="additional_comments">Additional Comments</Label>
                            <Textarea id="additional_comments" v-model="form.additional_comments" rows="4"
                                placeholder="Any additional comments or thoughts..." />
                            <InputError :message="form.errors.additional_comments" />
                        </div>
                    </div>

                    <!-- Step 19: Complete -->
                    <div v-if="currentStep === 19" class="space-y-6">
                        <div class="text-center" v-if="isAiDripGenerationLoading">

                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">We are working on your
                                assessment.</h2>
                            <p class="text-gray-600 text-lg dark:text-gray-300 font-light">Please don't close this page
                                just yet... </p>

                            <img :src="`/assets/ai-dots-loader.svg`" alt="AI loading" class="inline-block mx-auto"
                                :width="232" :height="332" />
                        </div>
                        <div class="text-center" v-else>
                            <!-- INSERT_YOUR_CODE -->
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Congratulations!</h2>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Most people never finish anything...
                                <span class="underline font-bold">You are <span class="italic">not</span> most
                                    people.</span>
                            </p>
                            <p class="text-gray-700 dark:text-gray-300 text-base mb-4">
                                Keep an eye on your email inbox. You'll receive your custom Blueprint results usually in
                                less than 5
                                minutes.
                                Check your "promotions" or spam folders if you don't see it.
                            </p>
                            <p class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">
                                This is just the beginning, and we are excited to be a part of your REI journey.
                            </p>
                            <p class="text-lg font-bold text-primary mb-4">LETS GO!</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                *PS* This is a good time to set your email permissions to "white-label" our sending
                                address so you never
                                miss a killer cheap property, or an important update.
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                                The stakes are SO high in our industry, and one deal could be worth 6 figures in profit.
                                Don’t miss out over
                                a stupid email setting you never set.
                            </p>
                            <div class="flex justify-center gap-8 mt-6">
                                <Link :href="route('profile.edit')"
                                    class="text-primary font-medium underline hover:text-primary/80 cursor-pointer">My
                                Profile</Link>
                                <Link :href="route('property.search')"
                                    class="text-primary font-medium underline hover:text-primary/80 cursor-pointer">
                                Search</Link>
                                <Link :href="route('learn.index')"
                                    class="text-primary font-medium underline hover:text-primary/80 cursor-pointer">
                                Learn</Link>
                            </div>
                        </div>
                    </div>
                    <!-- Navigation Buttons -->
                    <div v-if="currentStep <= 19" class="flex justify-between items-center mt-8">
                        <Button v-if="canGoBack" @click="previousStep" variant="outline"
                            :disabled="isLoading || isAiDripGenerationLoading" class="cursor-pointer">
                            <Icon icon="tabler:arrow-left" class="size-4" />
                            Back
                        </Button>
                        <div v-else></div>

                        <Button @click="onNextStep" :disabled="!canGoNext || isLoading || isAiDripGenerationLoading"
                            :class="currentStep === 18 ? ' bg-green-600 hover:bg-green-700 text-white' : ''"
                            class="cursor-pointer">
                            <span class="flex gap-2 my-auto" v-if="isAiDripGenerationLoading">
                                <Icon icon="tabler:loader" class="size-4 my-auto animate-spin" />
                                Generating...
                            </span>
                            <span class="flex gap-2 my-auto" v-else-if="isLoading">
                                <Icon icon="tabler:loader" class="size-4 my-auto animate-spin" />
                                Saving...
                            </span>
                            <span class="flex gap-2 my-auto" @click="router.visit(route('guest.home'))"
                                v-else-if="currentStep === 19 && !isAiDripGenerationLoading">
                                <Icon icon="tabler:home-2" class="size-4 my-auto" />
                                Go to Home
                            </span>
                            <span class="flex gap-2 my-auto" v-else-if="currentStep === 18">
                                <Icon icon="tabler:circle-check" class="size-4 my-auto" />
                                Complete
                            </span>
                            <span class="flex gap-2 my-auto" v-else>
                                Next
                                <Icon icon="tabler:arrow-right" class="size-4 my-auto" />
                            </span>
                        </Button>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                            <span class="text-sm  text-gray-600 dark:text-gray-400">{{ progressPercentage }}%</span>
                        </div>
                        <!-- 
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full transition-all duration-300"
                                :style="{ width: progressPercentage + '%' }"></div>
                        </div> -->

                        <ProgressRoot v-model="progressPercentage"
                            class="rounded-full relative h-4 w-full overflow-hidden border border-muted bg-white dark:bg-gray-800">
                            <ProgressIndicator
                                class="indicator rounded-full block relative w-full h-full bg-primary transition-transform overflow-hidden duration-[660ms] ease-[cubic-bezier(0.65, 0, 0.35, 1)] after:animate-progress after:content-[''] after:absolute after:inset-0  after:bg-[linear-gradient(-45deg,_rgba(255,255,255,0.2)_25%,_transparent_25%,_transparent_50%,_rgba(255,255,255,0.2)_50%,_rgba(255,255,255,0.2)_75%,_transparent_75%,_transparent)] after:bg-[length:30px_30px]"
                                :style="`transform: translateX(-${100 - progressPercentage}%)`" />
                        </ProgressRoot>

                    </div>
                </div>

                <!-- Loading Step -->
                <div v-if="showLoadingStep" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">We're working...</h2>
                    <p class="text-gray-600 dark:text-gray-300">Please don't close this page just yet...</p>
                </div>
            </div>
        </div>
    </GuestAppLayout>
</template>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

/* Custom range slider styles */
input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    cursor: pointer;
}

input[type="range"]::-webkit-slider-track {
    background: #e5e7eb;
    height: 8px;
    border-radius: 4px;
}

.dark input[type="range"]::-webkit-slider-track {
    background: #4b5563;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    background: #f59e0b;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    cursor: pointer;
}

input[type="range"]::-moz-range-track {
    background: #e5e7eb;
    height: 8px;
    border-radius: 4px;
    border: none;
}

.dark input[type="range"]::-moz-range-track {
    background: #4b5563;
}

input[type="range"]::-moz-range-thumb {
    background: #f59e0b;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    cursor: pointer;
    border: none;
}



/* Chrome, Safari, Edge */
.slider::-webkit-slider-runnable-track {
    background: #e5e7eb;
    height: 12px;
    border-radius: 9999px;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 20px;
    width: 20px;
    background: #f59e0b;
    border-radius: 50%;
    cursor: pointer;
    margin-top: -4px;
}

/* Firefox */
.slider::-moz-range-track {
    background: #e5e7eb;
    height: 12px;
    border-radius: 9999px;
}

.slider::-moz-range-thumb {
    height: 20px;
    width: 20px;
    background: #f59e0b;
    border-radius: 50%;
    border: none;
    cursor: pointer;
}
</style>