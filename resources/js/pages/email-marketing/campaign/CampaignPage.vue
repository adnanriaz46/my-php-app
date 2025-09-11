<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import PageHeading from "@/components/ui/page-heading/PageHeading.vue";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import Combobox from '@/components/ui/combobox/Combobox.vue';
import { CheckboxLoop } from '@/components/ui/checkbox-loop';

import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

import { useToast } from '@/composables/useToast';
import { Icon } from '@iconify/vue';
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import type { Contact } from '@/types/emailMarketing';
import CampaignList from '@/components/email-marketing/CampaignList.vue';
import IframeView from '@/components/email-marketing/IframeView.vue';
import type { Campaign } from '@/types/emailMarketing';
import axios from 'axios';
import { cn } from '@/lib/utils';
import { DBApiPropertyFull, DBApiPropertyMinimal } from '@/types/DBApi';
import { getProperty, getPropertyMinimalListByText } from '@/lib/DBApiUtil';
import { useDebounceFn } from '@vueuse/core';
import { useNumber } from '@/composables/useFormat';

import { EditorContent, useEditor } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import TextStyle from '@tiptap/extension-text-style'
import Color from '@tiptap/extension-color'
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PropertyStatusBadge from '@/components/ui/proprety-status-badge/PropertyStatusBadge.vue';

const { formatPrice, formatNumber } = useNumber()

const editor = useEditor({
    extensions: [
        StarterKit.configure({
            bulletList: false,
            orderedList: false,
            listItem: false,
        }),
        Underline,
        TextStyle,
        Color
    ],
    content: '',
});

const applyColor = (color: any) => {
    editor.value?.chain().focus().setColor(color).run()
}

const { showToast } = useToast();
const page = usePage();

interface Props {
    campaigns: {
        data: Campaign[];
        links: { url: string | null, label: string, active: boolean }[],
        from: number | null,
        to: number | null,
        current_page: number | null,
        last_page: number | null,
        per_page: number | null,
        total: number | null,
    };
    mustVerifyEmail: boolean;
    mustVerifyCompanyEmail: boolean;
    status?: string;
    success?: string;
    error?: string;
    user: User;
    property_id: number | null;
}



const props = defineProps<Props>();
const activeTab = ref('campaigns');




// Campaign form data
const campaignForm = ref({
    name: '',
    subject: '',
    description: '',
    headerText: '',
    contactSelection: 'all', // 'all', 'filtered', 'selected'
    contactFilters: {
        counties: [] as string[],
        dealTypes: [] as string[],
        tags: [] as string[],
        zipCodes: [] as string[],
    },
    selectedContacts: [] as number[],
    propertyData: null as DBApiPropertyFull | null,
    schedule: {
        sendImmediately: true,
        scheduledDate: '',
        scheduledTime: '',
    }
});

const searchContactQuery = ref('');

// UI state
const currentStep = ref(1);
const isLoading = ref(false);
const contactsDialogOpen = ref(false);
const previewDialogOpen = ref(false);
const previewEmailContent = ref('');

// Contact data
const contacts = ref<Contact[]>([]);
const filteredContacts = ref<Contact[]>([]);
const selectedContacts = ref<Contact[]>([]);

// Property data
const propretySearchList = ref<DBApiPropertyMinimal[]>([]);


// Available filter options (these would come from your existing data)
const availableCounties = ref([]);
const availableDealTypes = ref([]);
const availableTags = ref([]);
const availableZipCodes = ref([]);

const loadAvailableOptions = async () => {
    const { data } = await axios.get(route('email-marketing.contacts.get_available_options'));
    availableCounties.value = data.counties;
    availableDealTypes.value = data.deal_types;
    availableTags.value = data.tags;
    availableZipCodes.value = data.zip_codes;
};

// Computed properties
const totalRecipients = computed(() => {
    if (campaignForm.value.contactSelection === 'all') {
        return contacts.value.length;
    } else if (campaignForm.value.contactSelection === 'filtered') {
        return filteredContacts.value.length;
    } else {
        return selectedContacts.value.length;
    }
});



const canProceedToNext = computed(() => {
    switch (currentStep.value) {
        case 1:
            return campaignForm.value.name && campaignForm.value.subject;
        case 2:
            return totalRecipients.value > 0;
        case 3:
            return campaignForm.value.propertyData?.id != null;
        case 4:
            return campaignForm.value.subject && campaignForm.value.description && campaignForm.value.headerText;
        default:
            return true;
    }
});

const selectRecipientsOptions = [
    { value: 'all', label: 'All Contacts', icon: 'tabler:checkbox' },
    { value: 'filtered', label: 'Filtered Contacts', icon: 'tabler:filter' },
    { value: 'selected', label: 'Selected Contacts', icon: 'tabler:user-plus' },
];

// Methods
const loadContacts = async () => {
    try {
        const { data } = await axios.get(route('email-marketing.contacts.get_contacts'));
        contacts.value = data || [];
        filteredContacts.value = [...contacts.value];

    } catch (error: any) {
        console.log("Error: ", error?.response);
        showToast('Error', 'Failed to load contacts', 'error');
    }
};

const avoidNegativeStatus = ref(false);
watch(avoidNegativeStatus, (newVal) => {
    if (searchContactQuery.value) {
        loadPropertyMinimalListByText(newVal);
    }
});
watch(
    () => editor.value?.getHTML(),
    (newHTML) => {
        if (newHTML) {
            campaignForm.value.description = newHTML
        }
    }
)
const loadPropertyMinimalListByText = useDebounceFn(async (excludeNegativeStatus: boolean = false) => {
    const { data } = await getPropertyMinimalListByText(searchContactQuery.value, excludeNegativeStatus);
    propretySearchList.value = data || [];
}, 500);


const loadPropertyData = async (property: DBApiPropertyMinimal) => {
    isLoading.value = true;
    try {
        const tempResponse = await getProperty({ id: property.id });
        if (tempResponse.error) {
            showToast('Error', tempResponse.error, 'error');
            return;
        }
        campaignForm.value.propertyData = tempResponse.data?.[0] || null;


    } catch (error: any) {
        console.log("Error: ", error?.response);
        showToast('Loading Property', 'Failed to load property data', 'error');
    } finally {
        isLoading.value = false;
    }
};

const filterContacts = () => {
    let filtered = contacts.value;

    if (campaignForm.value.contactFilters.counties.length > 0) {
        filtered = filtered.filter(contact =>
            contact.counties.some(county =>
                campaignForm.value.contactFilters.counties.includes(county)
            )
        );
    }

    if (campaignForm.value.contactFilters.dealTypes.length > 0) {
        filtered = filtered.filter(contact =>
            contact.deal_type.some(dealType =>
                campaignForm.value.contactFilters.dealTypes.includes(dealType)
            )
        );
    }

    if (campaignForm.value.contactFilters.tags.length > 0) {
        filtered = filtered.filter(contact =>
            contact.tags.some(tag =>
                campaignForm.value.contactFilters.tags.includes(tag)
            )
        );
    }

    filteredContacts.value = filtered;
};

const selectContact = (contact: Contact) => {
    const index = selectedContacts.value.findIndex(c => c.id === contact.id);
    if (index > -1) {
        selectedContacts.value.splice(index, 1);
    } else {
        selectedContacts.value.push(contact);
    }
};


const selectProperty = (property: DBApiPropertyMinimal) => {
    campaignForm.value.propertyData = null;
    loadPropertyData(property);

    searchContactQuery.value = '';
    propretySearchList.value = [];
};

const nextStep = () => {
    if (canProceedToNext.value) {
        currentStep.value++;

    }
};

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const sendCampaign = async () => {
    isLoading.value = true;
    try {

        const { data } = await axios.post(route('email-marketing.campaign.send'), campaignForm.value);
        if (data.error) {
            showToast('Error', data.error, 'error');
            return;
        }
        showToast('Success', 'Campaign submitted successfully!', 'success');
        router.visit(route('email-marketing.campaign'));
    } catch (error: any) {

        if (error.response?.data?.errors) {
            const errorMessages = Object.values(error.response.data.errors).flat();
            showToast('Validation Error', errorMessages.join('<br>'), 'error');
            return;
        } else if (error.response?.data?.message) {
            showToast('Error', error.response.data.message, 'error');
            return;
        } else {
            showToast('Error', 'Failed to send campaign', 'error');
            return;
        }
        showToast('Error', 'Failed to send campaign', 'error');
    } finally {
        isLoading.value = false;
    }
};
const previewLoading = ref<boolean>(false);
const previewEmail = async () => {
    previewDialogOpen.value = true;
    previewLoading.value = true;
    await nextTick();
    await axios.post(route('email-marketing.campaign.email-preview'), {
        propertyInfo: campaignForm.value.propertyData,
        subject: campaignForm.value.subject,
        headerText: campaignForm.value.headerText,
        description: campaignForm.value.description,
        user: page.props.user,
    }).then(response => {
        previewDialogOpen.value = true;
        previewEmailContent.value = response.data;
    }).finally(() => {
        previewLoading.value = false;
    });

};

// Watch for filter changes
watch(() => campaignForm.value.contactFilters, filterContacts, { deep: true });


function onPaginate(url: string) {
    if (url) router.visit(url, { preserveScroll: true, preserveState: true });
}

onMounted(() => {
    loadContacts();
    loadAvailableOptions();
    // currentStep.value = 1; // remove after testing
    if (props.property_id) {
        activeTab.value = 'create';
        const tempProperty = { id: props.property_id, status: '', geo_address: '', image: null } as DBApiPropertyMinimal;
        showToast('Loading Property', 'Loading property data...', 'info', 2000);
        loadPropertyData(tempProperty).then(() => {
            if (campaignForm.value.propertyData) {
                showToast('Property Loaded', 'Selected property data has been loaded. Now you can create a campaign for this property.', 'success', 5000);
            } else {
                showToast('Property Not Found', 'Selected property data has not been loaded. Please try again.', 'error', 5000);
            }
        });
    }
});

const breadcrumbs: BreadcrumbItem[] = [

    {
        title: 'Email Campaigns',
        href: route('email-marketing.campaign'),
    },
];


</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Email Campaigns" />

        <div class="flex flex-col gap-2 px-4 pt-4">
            <!-- Header Actions -->
            <div class="flex items-center justify-between">
                <PageHeading class="px-0" sub-title="Create and manage your email marketing campaigns"
                    :breadcrumbs="breadcrumbs" title="Email Campaigns" />
            </div>

            <!-- Email Verification Warning
            <Card v-if="props.mustVerifyEmail"
                class="border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20 py-3">
                <CardContent class="px-2">
                    <div class="flex items-center gap-3">
                        <Icon icon="tabler:alert-triangle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                        <div>
                            <h3 class="font-medium text-yellow-800 dark:text-yellow-200">
                                Email Verification Required
                            </h3>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                Please verify your email address before creating campaigns.

                                <Link :href="route('verification.send')" method="post" as="button"
                                    class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500">
                                Click here to resend the verification email.
                                </Link>

                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card> -->

            <div v-if="!props.user.company_email">
                <Card>
                    <CardContent>
                        <p>Please add your company email address to create campaigns.
                            <Link :href="route('profile.professional.edit')" as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500">
                            Click here to add your company email address.</Link>
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Company Email Verification Section -->
            <div v-if="mustVerifyCompanyEmail && props.user.company_email">
                <div
                    class="rounded-lg border border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20 p-4">
                    <div class="flex items-start gap-3">
                        <Icon icon="tabler:alert-triangle"
                            class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                        <div class="flex-1">
                            <h3 class="font-medium text-yellow-800 dark:text-yellow-200">
                                Company Email Verification Required
                            </h3>
                            <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                Your company email address <strong>{{ props.user.company_email }}</strong> needs to be
                                verified.
                            </p>
                            <div class="mt-3">
                                <Link :href="route('profile.company-email.verification.send')" method="post" as="button"
                                    class="inline-flex items-center gap-2 rounded-md bg-yellow-600 px-3 py-2 text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-yellow-900">
                                <Icon icon="tabler:mail" class="h-4 w-4" />
                                Send Verification Email
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Success/Error Messages -->
            <Card v-if="props.success"
                class="border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20 py-3">
                <CardContent class="px-2">
                    <div class="flex items-center gap-3">
                        <Icon icon="tabler:check-circle" class="h-5 w-5 text-green-600 dark:text-green-400" />
                        <span class="text-green-800 dark:text-green-200">{{ props.success }}</span>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="props.error" class="border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 py-2">
                <CardContent class="px-2">
                    <div class="flex items-center gap-3">
                        <Icon icon="tabler:alert-circle" class="h-5 w-5 text-red-600 dark:text-red-400" />
                        <span class="text-red-800 dark:text-red-200">{{ props.error }}</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Main Content -->
            <div class="w-full " v-if="props.user.company_email && !mustVerifyCompanyEmail">

                <!-- Tab Navigation -->
                <div class="flex border-b mb-6">
                    <button @click="activeTab = 'campaigns'" :class="[
                        'px-4 py-2 text-sm font-medium border-b-2 transition-colors cursor-pointer',
                        activeTab === 'campaigns'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-muted-foreground hover:text-foreground'
                    ]">
                        My Campaigns
                    </button>
                    <button @click="activeTab = 'create'" :class="[
                        'px-4 py-2 text-sm font-medium border-b-2 transition-colors cursor-pointer',
                        activeTab === 'create'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-muted-foreground hover:text-foreground'
                    ]">
                        Create Campaign
                    </button>
                </div>

                
                <!-- Campaigns Tab -->
                <div v-if="activeTab === 'campaigns'" class="space-y-4 max-w-5xl mx-auto">
                    <CampaignList :campaigns="props.campaigns.data" />

                    <!-- Pagination would go here -->
                    <div class="flex justify-center">
                        <Pagination :links="props.campaigns.links" @navigate="onPaginate" />
                    </div>
                </div>

                <!-- Create Campaign Tab -->
                <div v-if="activeTab === 'create'"
                    class="space-y-2 max-w-5xl mx-auto h-[calc(100dvh-265px)] md:h-[calc(100dvh-245px)] overflow-y-auto">
                    <!-- Campaign Progress -->
                    <Card class=" pb-2 pt-4">
                        <CardContent class="px-5">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-xl font-semibold">Campaign Setup</h2>
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <span>Step {{ currentStep }} of 5</span>
                                </div>
                            </div>
                            <!-- Progress Steps -->
                            <div class="flex items-center justify-center mb-1">
                                <div v-for="step in 5" :key="step" class="flex flex-col items-start">

                                    <div class="flex justify-center items-center w-full">
                                        <div :class="cn(currentStep === step ? `w-10 md:w-15 xl:w-20` : `w-2 md:w-10 xl:w-16`, step == 1 ? `bg-transparent` : ``)"
                                            class="h-0.5 bg-muted"></div>
                                        <div :class="[
                                            'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                                            step <= currentStep
                                                ? 'bg-primary text-primary-foreground'
                                                : 'bg-muted text-muted-foreground'
                                        ]">
                                            {{ step }}
                                        </div>
                                        <div v-if="step <= 5"
                                            :class="cn(currentStep === step ? `w-10 md:w-15 xl:w-20` : `w-2 md:w-10 xl:w-16`, step == 5 ? `bg-transparent` : ``)"
                                            class="h-0.5 bg-muted"></div>
                                    </div>
                                    <div :class="currentStep === step ? `opacity-100 w-20` : `opacity-0`"
                                        class="text-center text-sm w-10 h-10 mx-auto">
                                        <div v-if="currentStep === step">
                                            <span v-if="step === 1">Campaign Details</span>
                                            <span v-else-if="step === 2">Select Recipients</span>
                                            <span v-else-if="step === 3">Property Data</span>
                                            <span v-else-if="step === 4">Email Content</span>
                                            <span v-else>Review & Send</span>
                                        </div>
                                        <div v-else>
                                            &nbsp;
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Step 1: Campaign Details -->
                    <Card v-if="currentStep === 1">
                        <CardHeader>
                            <CardTitle>Campaign Details</CardTitle>
                            <CardDescription>
                                Set up the basic information for your email campaign
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="campaign-name">Campaign Name</Label>
                                    <Input id="campaign-name" v-model="campaignForm.name"
                                        placeholder="Enter campaign name" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="campaign-subject">Email Subject</Label>
                                    <Input id="campaign-subject" v-model="campaignForm.subject"
                                        placeholder="Enter email subject" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Step 2: Select Recipients -->
                    <Card v-if="currentStep === 2">
                        <CardHeader>
                            <CardTitle>Select Recipients</CardTitle>
                            <CardDescription>
                                Choose who will receive your email campaign
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="space-y-4">

                                <div class="flex gap-4">
                                    <Label v-for="option in selectRecipientsOptions" :key="option.value"
                                        class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-md border"
                                        :class="{
                                            'border-stone-700 bg-stone-100 dark:bg-stone-800': campaignForm.contactSelection === option.value,
                                            'border-stone-300': campaignForm.contactSelection !== option.value
                                        }">
                                        <input type="radio" class="sr-only" :value="option.value"
                                            v-model="campaignForm.contactSelection" />
                                        <Icon :icon="option.icon" class="text-lg" />
                                        <span class="capitalize text-sm">{{ option.label }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Contact Filters -->
                            <div v-if="campaignForm.contactSelection === 'filtered'"
                                class="space-y-4 p-4 border rounded-lg">
                                <h4 class="font-medium">Filter Contacts</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label>Counties</Label>
                                        <Combobox v-model="campaignForm.contactFilters.counties"
                                            :option-values="availableCounties" placeholder="Select counties" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Deal Types</Label>
                                        <Combobox v-model="campaignForm.contactFilters.dealTypes"
                                            :option-values="availableDealTypes" placeholder="Select deal types" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Tags</Label>
                                        <Combobox v-model="campaignForm.contactFilters.tags"
                                            :option-values="availableTags" placeholder="Select tags" />
                                    </div>
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    Filtered contacts: {{ filteredContacts.length }}
                                </div>
                            </div>

                            <!-- Selected Contacts -->
                            <div v-if="campaignForm.contactSelection === 'selected'" class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium">Selected Contacts</h4>
                                    <Button variant="outline" size="sm" @click="contactsDialogOpen = true">
                                        Select Contacts
                                    </Button>
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    Selected: {{ selectedContacts.length }} contacts
                                </div>
                            </div>

                            <div class="text-lg font-medium text-primary">
                                Total Recipients: {{ totalRecipients }}
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Step 3: Property Data -->
                    <Card v-if="currentStep === 3">
                        <CardHeader>
                            <CardTitle>Property Data</CardTitle>
                            <CardDescription>
                                Configure the property data that will be included in your campaign
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="w-full space-y-2 relative ">
                                <Label :line-break="true">Search Properties By Address
                                    <Input v-model="searchContactQuery" placeholder="Search properties..."
                                        @input="loadPropertyMinimalListByText(avoidNegativeStatus)" />
                                    <div class="flex items-start gap-2">
                                        <CheckboxLoop v-model="avoidNegativeStatus" :value="true"
                                            id="avoid-negative-status" />
                                        <Label :line-break="true" for="avoid-negative-status" class="font-normal">
                                            Avoid Negative Status Properties
                                            <span class="text-xs text-muted-foreground">(Closed, Canceled, Withdrawn,
                                                Expired)</span>
                                        </Label>
                                    </div>
                                </Label>
                                <div v-if="propretySearchList.length > 0"
                                    class="space-y-1 text-sm absolute top-20 left-0 w-full p-2 z-50 rounded-lg bg-white dark:bg-gray-900 border max-h-[250px] overflow-y-auto">
                                    <div v-for="property in propretySearchList" :key="property.id"
                                        class="p-2 border rounded-lg cursor-pointer hover:bg-muted"
                                        @click="selectProperty(property)">
                                        <div class="flex items-center gap-2">
                                            <img v-if="property.image" :src="property.image" alt="Property Image"
                                                class="w-10 h-10 rounded-lg" />
                                            <div v-else class="w-10 h-10 rounded-lg bg-muted"></div>
                                            <div class="font-medium">{{ property.geo_address }}<br /><span
                                                    class="text-muted-foreground">{{ property.status }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="isLoading" class="flex items-center justify-center gap-2">
                                <Icon icon="tabler:loader-2" class="animate-spin" />
                                <span class="text-sm text-muted-foreground">Loading proprety data...</span>
                            </div>
                            <!-- Property Preview -->
                            <div v-if="campaignForm.propertyData" class="space-y-4 max-w-xs">
                                <h4 class="font-medium">Property Preview</h4>
                                <div class="grid grid-cols-1 justify-center  gap-4 border rounded-lg p-2">
                                    <div class="flex items-center justify-end">
                                        <img v-if="campaignForm.propertyData.full_location.split(',').length > 0 && campaignForm.propertyData.full_location.split(',')[0] != ''"
                                            :src="campaignForm.propertyData.full_location.split(',')[0]"
                                            alt="Property Image"
                                            class="w-full h-full md:max-h-[300px] object-cover rounded-lg" />
                                        <div v-else
                                            class="w-full h-full rounded-lg bg-muted flex items-center justify-center">
                                            <Icon icon="tabler:photo-off" class="w-10 h-10 text-muted-foreground" />
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <div class="font-medium">{{ campaignForm.propertyData.geo_address }}</div>
                                        <div class="text-base text-muted-foreground font-semibold">
                                            {{ formatPrice(campaignForm.propertyData.list_price) }} <PropertyStatusBadge
                                                :status="campaignForm.propertyData.status">
                                                {{ campaignForm.propertyData.status }}
                                            </PropertyStatusBadge>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <Icon icon="tabler:bed" class="w-4 h-4" />
                                            {{ formatNumber(campaignForm.propertyData.bedrooms_count) }}
                                            &nbsp;|&nbsp;
                                            <Icon icon="tabler:bath" class="w-4 h-4" />
                                            {{ formatNumber(campaignForm.propertyData.bathrooms_total_count) }}
                                            &nbsp;|&nbsp;
                                            <Icon icon="tabler:square-plus" class="w-4 h-4" />
                                            {{ formatNumber(campaignForm.propertyData.total_finished_sqft) }} sqft
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Step 4: Email Content -->
                    <Card v-if="currentStep === 4">
                        <CardHeader>
                            <CardTitle>Email Content</CardTitle>
                            <CardDescription>
                                Create the content for your email campaign
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="space-y-2">
                                <Label for="email-subject">Email Subject</Label>
                                <Input id="email-subject" v-model="campaignForm.subject"
                                    placeholder="Enter email subject" />
                            </div>
                            <div class="space-y-2">
                                <Label for="header-text">Header Text</Label>
                                <Input id="header-text" v-model="campaignForm.headerText"
                                    placeholder="Enter header text" />
                            </div>
                            <div class="space-y-2">
                                <Label for="email-body">Email Description</Label>
                                <!-- wysiwyg editor -->
                                <div class="border rounded-xl p-1 bg-white dark:bg-stone-900 shadow-sm">
                                    <!-- Toolbar -->
                                    <div
                                        class="flex flex-wrap gap-2 items-center bg-stone-100 dark:bg-stone-800 p-2 rounded-xl shadow-sm">
                                        <button @click="editor?.chain().focus().toggleBold().run()"
                                            :class="editor?.isActive('bold') ? 'bg-stone-700 text-white' : 'bg-white text-stone-700'"
                                            class="px-3 py-1 rounded text-sm font-medium shadow-sm">B</button>

                                        <button @click="editor?.chain().focus().toggleItalic().run()"
                                            :class="editor?.isActive('italic') ? 'bg-stone-700 text-white' : 'bg-white text-stone-700'"
                                            class="px-3 py-1 rounded text-sm font-medium italic shadow-sm">I</button>

                                        <button @click="editor?.chain().focus().toggleUnderline().run()"
                                            :class="editor?.isActive('underline') ? 'bg-stone-700 text-white' : 'bg-white text-stone-700'"
                                            class="px-3 py-1 rounded text-sm font-medium underline shadow-sm">U</button>


                                        <input type="color"
                                            @input="e => applyColor((e.target as HTMLInputElement)?.value)"
                                            class="ml-4 w-6 h-6 cursor-pointer border-none bg-transparent" />
                                    </div>
                                    <EditorContent :editor="editor"
                                        class="prose dark:prose-invert max-w-none border p-4 rounded-xl bg-white dark:bg-stone-900" />

                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button variant="outline" @click="previewEmail">
                                    <Icon icon="tabler:eye" class="mr-2" />
                                    Preview Email
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Step 5: Review & Send -->
                    <Card v-if="currentStep === 5">
                        <CardHeader>
                            <CardTitle>Review & Send</CardTitle>
                            <CardDescription>
                                Review your campaign settings and send
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h4 class="font-medium">Campaign Summary</h4>
                                    <div class="space-y-2 text-sm">
                                        <div><strong class="font-semibold">Name:</strong> {{ campaignForm.name }}</div>
                                        <div><strong class="font-semibold">Subject:</strong> {{ campaignForm.subject }}
                                        </div>
                                        <div><strong class="font-semibold">Recipients:</strong> {{ totalRecipients }}
                                        </div>
                                        <div class="space-y-2">
                                            <span><strong class="font-semibold">Description: </strong></span>
                                            <div class="prose max-w-none p-3 border rounded-lg"
                                                v-html="campaignForm.description"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="font-medium">Schedule</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <CheckboxLoop v-model="campaignForm.schedule.sendImmediately" :value="true"
                                                id="send-now" />
                                            <Label for="send-now">Send immediately</Label>
                                        </div>
                                        <div v-if="!campaignForm.schedule.sendImmediately"
                                            class="grid grid-cols-2 gap-2">
                                            <Input type="date" v-model="campaignForm.schedule.scheduledDate" />
                                            <Input v-model="campaignForm.schedule.scheduledTime" type="time" />
                                        </div>
                                    </div>
                                    <div v-if="campaignForm.propertyData" class="space-y-4 max-w-xs">
                                        <h4 class="font-medium">Property Preview</h4>
                                        <div class="grid grid-cols-1 justify-center  gap-4 border rounded-lg p-2">
                                            <div class="flex items-center justify-end">
                                                <img v-if="campaignForm.propertyData.full_location.split(',').length > 0 && campaignForm.propertyData.full_location.split(',')[0] != ''"
                                                    :src="campaignForm.propertyData.full_location.split(',')[0]"
                                                    alt="Property Image"
                                                    class="w-full h-full md:max-h-[300px] object-cover rounded-lg" />
                                                <div v-else
                                                    class="w-full h-full rounded-lg bg-muted flex items-center justify-center">
                                                    <Icon icon="tabler:photo-off"
                                                        class="w-10 h-10 text-muted-foreground" />
                                                </div>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <div class="font-medium">{{ campaignForm.propertyData.geo_address }}
                                                </div>
                                                <div
                                                    class="text-base text-muted-foreground font-semibold flex items-center gap-2">
                                                    {{ formatPrice(campaignForm.propertyData.list_price) }}
                                                    <PropertyStatusBadge :status="campaignForm.propertyData.status">
                                                        {{ campaignForm.propertyData.status }}
                                                    </PropertyStatusBadge>
                                                </div>
                                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                    <Icon icon="tabler:bed" class="w-4 h-4" />
                                                    {{ formatNumber(campaignForm.propertyData.bedrooms_count) }}
                                                    &nbsp;|&nbsp;
                                                    <Icon icon="tabler:bath" class="w-4 h-4" />
                                                    {{ formatNumber(campaignForm.propertyData.bathrooms_total_count) }}
                                                    &nbsp;|&nbsp;
                                                    <Icon icon="tabler:square-plus" class="w-4 h-4" />
                                                    {{ formatNumber(campaignForm.propertyData.total_finished_sqft) }}
                                                    sqft
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <Button variant="outline" @click="previewEmail">
                                    <Icon icon="tabler:eye" class="mr-2" />
                                    Preview
                                </Button>
                                <Button @click="sendCampaign" :disabled="isLoading" class="flex-1">
                                    <Icon v-if="isLoading" icon="tabler:loader-2" class="mr-2 animate-spin" />
                                    <Icon v-else icon="tabler:send" class="mr-2" />
                                    {{ campaignForm.schedule.sendImmediately ? 'Send Campaign' : 'Schedule Campaign' }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <Button variant="outline" @click="previousStep" :disabled="currentStep === 1">
                            <Icon icon="tabler:arrow-left" class="mr-2" />
                            Previous
                        </Button>

                        <Button @click="nextStep" :disabled="!canProceedToNext || currentStep === 5">
                            <Icon icon="tabler:arrow-right" class="ml-2" />
                            {{ currentStep === 5 ? 'Finalize' : 'Next' }}
                        </Button>
                    </div>
                </div>


            </div>
        </div>

        <!-- Contact Selection Dialog -->
        <Dialog v-model:open="contactsDialogOpen" class="max-h-[100dvh]">
            <DialogContent class="max-w-4xl">
                <DialogHeader>
                    <DialogTitle>Select Contacts <span class="text-sm text-muted-foreground">({{ selectedContacts.length
                    }} selected)</span></DialogTitle>
                    <DialogDescription>
                        Choose which contacts should receive this campaign
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4 ">
                    <div class="flex items-center justify-between">
                        <Input v-model="searchContactQuery" placeholder="Search contacts..." />
                    </div>

                    <div class="space-y-2 max-h-[calc(100dvh-250px)] overflow-y-auto">
                        <div v-for="contact in contacts.filter(contact => contact.name.toLowerCase().includes(searchContactQuery.toLowerCase()) || contact.email.toLowerCase().includes(searchContactQuery.toLowerCase())).slice(0, 25)"
                            :key="contact.id"
                            class="flex items-center justify-between p-3 border rounded-lg hover:bg-muted">
                            <div class="flex items-center gap-3">
                                <CheckboxLoop :model-value="selectedContacts.some(c => c.id === contact.id)"
                                    @update:model-value="selectContact(contact)" />
                                <div>
                                    <div class="font-medium">{{ contact.name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ contact.email }}</div>
                                </div>
                            </div>
                            <div class="text-sm text-muted-foreground">
                                {{ contact.counties.join(', ') }}
                            </div>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="contactsDialogOpen = false">
                        Cancel
                    </Button>
                    <Button @click="contactsDialogOpen = false">
                        Confirm Selection
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Email Preview Dialog -->
        <Dialog v-model:open="previewDialogOpen">
            <DialogContent class="max-w-4xl max-h-[100dvh]">
                <DialogHeader>
                    <DialogTitle>Email Preview</DialogTitle>
                    <DialogDescription>
                        Preview how your email will look to recipients
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4 max-h-[calc(100dvh-200px)] overflow-y-auto">
                    <IframeView v-if="!previewLoading" class="w-full h-[calc(100dvh-200px)] border rounded-lg"
                        sandbox="allow-same-origin allow-scripts" :content="previewEmailContent" />

                    <div v-else class="flex items-center justify-center gap-2">
                        <Icon icon="tabler:loader-2" class="animate-spin" />
                        <span class="text-sm text-muted-foreground text-center">Loading email preview...</span>
                    </div>
                </div>

                <DialogFooter>
                    <Button @click="previewDialogOpen = false">
                        Close
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>