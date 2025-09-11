<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { FullScreenDialog } from '@/components/ui/full-screen-dialog';
import { Input } from '@/components/ui/input';
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Button from '@/components/ui/button/Button.vue';
import { Label } from '@/components/ui/label';
import Combobox from '@/components/ui/combobox/Combobox.vue';
import InputNumber from '@/components/ui/input-number/InputNumber.vue';
import axios from 'axios';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader, DialogTitle,
    DialogTrigger
} from "@/components/ui/dialog";
import { UserTypes } from '@/types/userTypes';
// import AppearanceTabs from '@/components/AppearanceTabs.vue';
import UserTypeBadge from '@/components/ui/user-type-badge/UserTypeBadge.vue';
import { useDateFormat } from '@/composables/useFormat';
import InputError from '@/components/InputError.vue';
import CheckboxLoop from '@/components/ui/checkbox-loop/CheckboxLoop.vue';
import { Icon } from '@iconify/vue';
import { useToast } from "@/composables/useToast";
import Toast from '@/components/ui/toast/Toast.vue';
import { getInitials } from '@/composables/useInitials';
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import { PropertyViewHistory } from '@/types/property';
import { SavedSearchFull } from '@/types/propertySearch';
import { LeadSource } from '@/types';
import { isMobileUserAgent } from '@/lib/utils';

const { showToast } = useToast();

const { formatDate, formatDateTime } = useDateFormat();

// Image cropper related reactive variables
const dialogOpen = ref(false);
const img = ref<string | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);
const avatar_error = ref('');
const avatar_loading = ref(false);

// Profile picture cropper related reactive variables
const profileDialogOpen = ref(false);
const profileImg = ref<string | null>(null);
const profileCanvas = ref<HTMLCanvasElement | null>(null);
const profile_error = ref('');
const profile_loading = ref(false);

const props = defineProps<{
    modelValue: boolean;
    userId: number | null;
    propertyTypes: string[];
    invStgy: string[];
    counties: string[];
    emailCategories: Record<string, any>;
}>();
const emit = defineEmits(['update:modelValue']);

const open = ref(props.modelValue);
const loading = ref(false);
const user = ref<any>(null);
const summary = ref<any>(null);
const recentViews = ref<PropertyViewHistory[]>([]);
const recentSavedSearches = ref<SavedSearchFull[]>([]);
const leadSource = ref<LeadSource | null>(null);
const tab = ref('basic');

const userTypeOptions = [
    { key: UserTypes.FREE, value: 'Free' },
    { key: UserTypes.PREMIUM, value: 'Premium' },
    { key: UserTypes.ADMIN, value: 'Admin' },
];

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    user_type: '',
    phone_number: '',
    street_address: '',
    city: '',
    state: '',
    zip: '',
    subscribed_counties: [] as string[],
    enable_mls_offer: false,
});

const buyBoxForm = useForm({
    investment_strategy: [] as string[],
    counties_invest: [] as string[],
    property_types: [] as string[],
    arv_min: null as number | null,
    arv_max: null as number | null,
    bath_min: null as number | null,
    bath_max: null as number | null,
    bed_min: null as number | null,
    bed_max: null as number | null,
    cashflow_min: null as number | null,
    cashflow_max: null as number | null,
    delta_psf_min: null as number | null,
    delta_psf_max: null as number | null,
    flip_profit_min: null as number | null,
    flip_profit_max: null as number | null,
    price_min: null as number | null,
    price_max: null as number | null,
    sqft_min: null as number | null,
    sqft_max: null as number | null,
    year_build_min: null as number | null,
    year_build_max: null as number | null,
});

const emailForm = useForm({
    email: '',
    emailVerified: false as boolean,
    emailVerifiedAt: null as string | null,
    global: false as boolean,
    emailUnsubscribedListPreference: [] as string[],
});

const businessForm = useForm({
    companyName: '',
    companyEmail: '',
    brokerageName: '',
    agentLicenseNumber: '',
});

// Image cropper methods
function change({ coordinates, canvas: croppedCanvas }: { coordinates: any; canvas: HTMLCanvasElement }) {
    console.log(coordinates, croppedCanvas);
    canvas.value = croppedCanvas;
}

function onFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            img.value = event.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

// Profile picture cropper methods
function onProfileChange({ coordinates, canvas: croppedCanvas }: { coordinates: any; canvas: HTMLCanvasElement }) {
    console.log(coordinates, croppedCanvas);
    profileCanvas.value = croppedCanvas;
}

function onProfileFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            profileImg.value = event.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

function emitProfileCroppedImage() {
    if (!profileCanvas.value) return;
    profileCanvas.value.toBlob((blob: Blob | null) => {
        if (!blob) {
            profile_error.value = 'Failed to create image blob';
            return;
        }

        const formData = new FormData();
        const userId = props.userId;

        formData.append('image', blob, 'cropped.jpg');
        profile_error.value = '';
        profile_loading.value = true;

        axios.post(route('admin.users.profile-picture.upload', userId?.toString()), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
            .then((response) => {
                console.log('Upload success:', response.data);
                if (response.data?.error) {
                    profile_error.value = response.data.error;
                } else {
                    profileDialogOpen.value = false;
                    refreshPage();
                }
                profile_loading.value = false;
            })
            .catch((error) => {
                profile_error.value = error.response?.data?.error || error.message || 'Upload failed';
                profile_loading.value = false;
            });
    }, 'image/jpeg');
}

async function refreshPage() {
    await fetchUserDetail(props.userId ?? 0);
}

function emitCroppedImage() {
    if (!canvas.value) return;
    canvas.value.toBlob((blob: Blob | null) => {
        if (!blob) {
            avatar_error.value = 'Failed to create image blob';
            return;
        }

        const formData = new FormData();
        const userId = props.userId;

        formData.append('image', blob, 'cropped.jpg');
        avatar_error.value = '';
        avatar_loading.value = true;

        axios.post(route('admin.users.business.logo.upload', userId?.toString()), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
            .then((response) => {
                console.log('Upload success:', response.data);
                if (response.data?.error) {
                    avatar_error.value = response.data.error;
                } else {
                    dialogOpen.value = false;
                    refreshPage();
                }
                avatar_loading.value = false;
            })
            .catch((error) => {
                avatar_error.value = error.response?.data?.error || error.message || 'Upload failed';
                avatar_loading.value = false;
            });
    }, 'image/jpeg');
}

// Always keep open in sync with prop
watch(() => props.modelValue, (val) => {
    open.value = val;
});

// Fetch user details when dialog opens and userId is set
watch(
    [open, () => props.userId],
    async ([isOpen, id]) => {
        if (isOpen && id) {
            await fetchUserDetail(id);
        }
        if (!isOpen) {
            user.value = null;
            summary.value = null;
            recentViews.value = [];
            recentSavedSearches.value = [];
            form.reset();
            buyBoxForm.reset();
            emailForm.reset();
            businessForm.reset();
        }
    },
    { immediate: true }
);

async function fetchUserDetail(id: number) {
    loading.value = true;
    try {
        const response = await axios.get(route('admin.users.show', id));
        user.value = response.data.user;
        summary.value = response.data.summary;
        recentViews.value = response.data.recentViews;
        recentSavedSearches.value = response.data.recentSavedSearches;
        leadSource.value = response.data.leadSource;
        // Update basic form
        form.first_name = user.value.first_name;
        form.last_name = user.value.last_name;
        form.email = user.value.email;
        form.user_type = user.value.user_type;
        form.phone_number = user.value.phone_number;
        form.street_address = user.value.street_address;
        form.subscribed_counties = user.value.subscribed_counties;
        form.city = user.value.city;
        form.state = user.value.state;
        form.zip = user.value.zip;
        form.enable_mls_offer = user.value.enable_mls_offer;

        // Update buy box form
        if (user.value.buy_box) {
            buyBoxForm.investment_strategy = user.value.buy_box.investment_strategy || [];
            buyBoxForm.counties_invest = user.value.buy_box.counties_invest || [];
            buyBoxForm.property_types = user.value.buy_box.property_types || [];
            buyBoxForm.arv_min = user.value.buy_box.arv_min ? Number(user.value.buy_box.arv_min) : null;
            buyBoxForm.arv_max = user.value.buy_box.arv_max ? Number(user.value.buy_box.arv_max) : null;
            buyBoxForm.bath_min = user.value.buy_box.bath_min ? Number(user.value.buy_box.bath_min) : null;
            buyBoxForm.bath_max = user.value.buy_box.bath_max ? Number(user.value.buy_box.bath_max) : null;
            buyBoxForm.bed_min = user.value.buy_box.bed_min ? Number(user.value.buy_box.bed_min) : null;
            buyBoxForm.bed_max = user.value.buy_box.bed_max ? Number(user.value.buy_box.bed_max) : null;
            buyBoxForm.cashflow_min = user.value.buy_box.cashflow_min ? Number(user.value.buy_box.cashflow_min) : null;
            buyBoxForm.cashflow_max = user.value.buy_box.cashflow_max ? Number(user.value.buy_box.cashflow_max) : null;
            buyBoxForm.delta_psf_min = user.value.buy_box.delta_psf_min ? Number(user.value.buy_box.delta_psf_min) : null;
            buyBoxForm.delta_psf_max = user.value.buy_box.delta_psf_max ? Number(user.value.buy_box.delta_psf_max) : null;
            buyBoxForm.flip_profit_min = user.value.buy_box.flip_profit_min ? Number(user.value.buy_box.flip_profit_min) : null;
            buyBoxForm.flip_profit_max = user.value.buy_box.flip_profit_max ? Number(user.value.buy_box.flip_profit_max) : null;
            buyBoxForm.price_min = user.value.buy_box.price_min ? Number(user.value.buy_box.price_min) : null;
            buyBoxForm.price_max = user.value.buy_box.price_max ? Number(user.value.buy_box.price_max) : null;
            buyBoxForm.sqft_min = user.value.buy_box.sqft_min ? Number(user.value.buy_box.sqft_min) : null;
            buyBoxForm.sqft_max = user.value.buy_box.sqft_max ? Number(user.value.buy_box.sqft_max) : null;
            buyBoxForm.year_build_min = user.value.buy_box.year_build_min ? Number(user.value.buy_box.year_build_min) : null;
            buyBoxForm.year_build_max = user.value.buy_box.year_build_max ? Number(user.value.buy_box.year_build_max) : null;
        }

        // Update email form
        if (user.value) {
            emailForm.email = user.value.email;
            emailForm.emailVerified = user.value.email_verified_at ? true : false;
            emailForm.emailVerifiedAt = user.value.email_verified_at;
            emailForm.global = user.value.email_unsubscribed_global || false;
            emailForm.emailUnsubscribedListPreference = user.value.email_unsubscribed_list_preference || [];
        }

        // Update business form
        if (user.value) {
            businessForm.companyName = user.value.company_name || '';
            businessForm.companyEmail = user.value.company_email || '';
            businessForm.brokerageName = user.value.brokerage_name || '';
            businessForm.agentLicenseNumber = user.value.agent_license_number || '';
        }
    } finally {
        loading.value = false;
    }
}

function close() {
    emit('update:modelValue', false);
}

function updateUser() {
    if (!user.value) return;
    form.patch(route('admin.users.update', user.value.id), {
        onStart: () => { loading.value = true; },
        onFinish: () => { loading.value = false; },
        onError: () => { showToast('Error', 'Please check the form and try again', 'error'); },
        onSuccess: async () => { showToast('Success', 'User updated successfully', 'success'); await fetchUserDetail(user.value.id); },
    });
}

function updateBuyBox() {
    if (!user.value) return;

    buyBoxForm.patch(route('admin.users.buy-box.update', user.value.id), {
        onStart: () => { loading.value = true; },
        onFinish: () => { loading.value = false; },
        onError: () => { showToast('Error', 'Please check the form and try again', 'error'); },
        onSuccess: async () => { showToast('Success', 'Buy Box updated successfully', 'success'); await fetchUserDetail(user.value.id); },
    });
}

function updateEmail() {
    if (!user.value) return;
    emailForm.patch(route('admin.users.email.update', user.value.id), {
        onStart: () => { loading.value = true; },
        onFinish: () => { loading.value = false; },
        onError: () => { showToast('Error', 'Please check the form and try again', 'error'); },
        onSuccess: async () => { showToast('Success', 'Email updated successfully', 'success'); await fetchUserDetail(user.value.id); },
    });
}

function updateBusiness() {
    if (!user.value) return;
    businessForm.patch(route('admin.users.business.update', user.value.id), {
        onStart: () => { loading.value = true; },
        onFinish: () => { loading.value = false; },
        onError: () => { showToast('Error', 'Please check the form and try again', 'error'); },
        onSuccess: async () => { showToast('Success', 'Business updated successfully', 'success'); await fetchUserDetail(user.value.id); },
    });
}

async function loginAsUser() {
    if (!user.value) return;

    try {
        const response = await axios.post(route('admin.users.login-as', user.value.id));

        if (response.data.success) {
            showToast('Success', response.data.message, 'success');
            // Redirect to dashboard after a short delay
            setTimeout(() => {
                window.location.href = response.data.redirect_url;
            }, 1000);
        }
    } catch (error: any) {
        const errorMessage = error.response?.data?.error || 'Failed to login as user';
        showToast('Error', errorMessage, 'error');
    }
}
</script>

<template>

    <FullScreenDialog v-model="open" :title="'User Details'" @update:modelValue="close">
        <Toast />
        <div v-if="loading"
            class="p-8 text-center text-gray-500 flex items-center justify-center  h-[calc(100dvh-5px)]">
            <div class="flex flex-col items-center justify-center">
                <Icon icon="mdi:loading" class="w-10 h-10 animate-spin" />
            </div>
        </div>
        <div v-else-if="user"
            class="flex flex-col md:flex-row gap-6 rounded-t-xl pt-4 mb-2 px-2 bg-accent  h-[calc(100dvh-5px)]">
            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                <!-- Header -->
                <div class="flex items-center gap-4 mb-4 px-2">
                    <img v-if="user.profile_picture" :src="user.profile_picture"
                        class="w-20 h-20 rounded-full object-cover" />
                    <div v-else
                        class="w-20 h-20 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                        <span class="text-4xl text-gray-500 dark:text-gray-300">{{ getInitials(user.name) }}</span>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ user.name }}</div>
                        <div class="flex items-center gap-2">
                            <UserTypeBadge :userType="user.user_type" />
                            <Button variant="outline" size="sm" class="cursor-pointer" @click="loginAsUser">Login as
                                User</Button>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Joined at {{ user.created_at ?
                            formatDate(user.created_at) : '' }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Last Active {{ user.last_login ?
                            Math.round((Date.now() - new Date(user.last_login).getTime()) / (1000 * 60 * 60)) : '?' }}
                            hours ago</div>
                    </div>
                    <!-- <div class="absolute right-0 top-0" v-if="page.props.auth.user.email === 'admin@admin.com'">
                        <AppearanceTabs />
                    </div> -->

                </div>
                <!-- Stats Row -->
                <div class="flex gap-6 mb-2 text-center px-4 text-base">
                    <div class="flex flex-col items-center"><span class="font-bold">{{ summary.views +
                        summary.unlisted_views + summary.searches + summary.saved_searches }}</span><span
                            class="text-xs text-gray-500">Score</span></div>
                    <div class="flex flex-col items-center"><span class="font-bold">{{ summary.views }}</span><span
                            class="text-xs text-gray-500">Views</span></div>
                    <div class="flex flex-col items-center"><span class="font-bold">{{ summary.unlisted_views
                            }}</span><span class="text-xs text-gray-500">Unlisted</span></div>
                    <div class="flex flex-col items-center"><span class="font-bold">{{ summary.searches }}</span><span
                            class="text-xs text-gray-500">Searches</span></div>
                    <div class="flex flex-col items-center"><span class="font-bold">{{ summary.saved_searches
                            }}</span><span class="text-xs text-gray-500">Saved</span></div>
                </div>

                <!-- Tabs -->
                <div class="flex gap-4 border-b px-2 text-base">
                    <button
                        :class="['px-4 py-2 cursor-pointer', tab === 'basic' ? 'border-b-2 border-yellow-400 font-bold' : 'text-gray-500']"
                        @click="tab = 'basic'">Basic</button>
                    <button
                        :class="['px-4 py-2 cursor-pointer', tab === 'buybox' ? 'border-b-2 border-yellow-400 font-bold' : 'text-gray-500']"
                        @click="tab = 'buybox'">Buy Box</button>
                    <button
                        :class="['px-4 py-2 cursor-pointer', tab === 'email' ? 'border-b-2 border-yellow-400 font-bold' : 'text-gray-500']"
                        @click="tab = 'email'">Email</button>
                    <button
                        :class="['px-4 py-2 cursor-pointer', tab === 'business' ? 'border-b-2 border-yellow-400 font-bold' : 'text-gray-500']"
                        @click="tab = 'business'">Business</button>
                </div>

                <div class="flex flex-col md:flex-row overflow-auto max-h-[calc(100dvh-250px)] pt-2 px-2  gap-2">
                    <!-- Tab Content -->
                    <div v-if="tab === 'basic'" class="flex flex-col md:flex-row pt-2 px-2 gap-2 w-full h-full">
                        <form @submit.prevent="updateUser"
                            class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow gap-4  h-full w-full">
                            <div class="text-xl font-bold mb-2">Details</div>

                            <!-- Profile Picture Section -->
                            <div class="flex justify-center mb-6">
                                <Dialog v-model:open="profileDialogOpen">
                                    <DialogTrigger as-child>
                                        <div class="flex items-center gap-2 cursor-pointer">
                                            <Avatar class="size-24 overflow-hidden rounded-full border-2">
                                                <AvatarImage v-if="user.profile_picture" :src="user.profile_picture"
                                                    :alt="user.name" />
                                                <AvatarFallback
                                                    class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                                    {{ getInitials(user?.name) }}
                                                </AvatarFallback>
                                            </Avatar>
                                            <Icon icon="tabler:edit" class="size-6" />
                                        </div>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader class="space-y-3">
                                            <DialogTitle>Upload Profile Picture</DialogTitle>
                                        </DialogHeader>

                                        <div class="flex justify-center">
                                            <Input type="file" @change="onProfileFileChange" class=""
                                                accept="image/*" />
                                        </div>
                                        <div class="flex justify-center">
                                            <cropper :hidden="!profileImg" style="max-width: 320px; height: 320px"
                                                class="cropper rounded-xl content-center" :src="profileImg"
                                                :auto-zoom="true" :stencil-props="{ aspectRatio: 1 }"
                                                @change="onProfileChange" />
                                        </div>
                                        <div class="flex justify-center">
                                            <InputError class="mt-2" :message="profile_error" />
                                        </div>
                                        <DialogFooter class="gap-2">
                                            <DialogClose as-child>
                                                <Button variant="secondary"> Cancel</Button>
                                            </DialogClose>
                                            <Button v-if="profileImg" @click="emitProfileCroppedImage"
                                                :disabled="profile_loading">Upload
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium">First Name</label>
                                    <Input v-model="form.first_name" />
                                    <InputError :message="form.errors.first_name" />
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium">Last Name</label>
                                    <Input v-model="form.last_name" />
                                    <InputError :message="form.errors.last_name" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-sm font-medium">Email Address</label>
                                    <Input disabled v-model="form.email" />
                                    <InputError :message="form.errors.email" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-sm font-medium">Account Type</label>
                                    <SelectBox v-model="form.user_type" :options="userTypeOptions" />
                                    <InputError :message="form.errors.user_type" />
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="subscribed_counties" class="block mb-1 text-sm font-medium">Subscribed
                                        Counties ({{ form.subscribed_counties?.length ?? '0' }})</Label>
                                    <Combobox v-model="form.subscribed_counties" :optionValues="props.counties" />
                                    <InputError :message="form.errors.subscribed_counties" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-sm font-medium">Phone Number</label>
                                    <Input v-model="form.phone_number" />
                                    <InputError :message="form.errors.phone_number" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-sm font-medium">Address</label>
                                    <Input v-model="form.street_address" />
                                    <InputError :message="form.errors.street_address" />
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium">City</label>
                                    <Input v-model="form.city" />
                                    <InputError :message="form.errors.city" />
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium">State</label>
                                    <Input v-model="form.state" />
                                    <InputError :message="form.errors.state" />
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium">Zip Code</label>
                                    <Input v-model="form.zip" />
                                    <InputError :message="form.errors.zip" />
                                </div>
                                <!-- <div class="flex items-center gap-2 mt-2">
                                    <label class="block text-sm font-medium">Enable MLS Offer</label>
                                    <Checkbox v-model="form.enable_mls_offer" />
                                    <InputError :message="form.errors.enable_mls_offer" />
                                </div> -->
                            </div>
                            <Button class="mt-4 w-full" :loading="form.processing" type="submit">Update</Button>
                        </form>
                    </div>
                    <div v-else-if="tab === 'buybox'" class="flex flex-col md:flex-row pt-2 px-2 gap-2 w-full h-full">
                        <form @submit.prevent="updateBuyBox"
                            class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow gap-4  h-full w-full">
                            <div class="text-xl font-bold mb-2">Buy Box Details</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <Label for="investment_strategy" class="block mb-1 text-sm font-medium">Investment
                                        Strategy</Label>
                                    <Combobox v-model="buyBoxForm.investment_strategy" :optionValues="invStgy" />
                                    <InputError :message="buyBoxForm.errors.investment_strategy" />
                                </div>
                                <div>
                                    <Label for="counties_invest" class="block mb-1 text-sm font-medium">Counties
                                        Invested In</Label>
                                    <Combobox v-model="buyBoxForm.counties_invest" :optionValues="props.counties" />
                                    <InputError :message="buyBoxForm.errors.counties_invest" />
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="property_types" class="block mb-1 text-sm font-medium">Property
                                        Types</Label>
                                    <Combobox v-model="buyBoxForm.property_types" :optionValues="props.propertyTypes" />
                                    <InputError :message="buyBoxForm.errors.property_types" />
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="arv_min" class="block mb-1 text-sm font-medium">ARV Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="arv_min" class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.arv_min" />
                                            <InputError :message="buyBoxForm.errors.arv_min" />
                                        </div>
                                        <div>
                                            <Label for="arv_max" class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.arv_max" />
                                            <InputError :message="buyBoxForm.errors.arv_max" />
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="bath_min" class="block mb-1 text-sm font-medium">Bathrooms Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="bath_min" class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.bath_min" />
                                            <InputError :message="buyBoxForm.errors.bath_min" />
                                        </div>
                                        <div>
                                            <Label for="bath_max" class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.bath_max" />
                                            <InputError :message="buyBoxForm.errors.bath_max" />
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="bed_min" class="block mb-1 text-sm font-medium">Bedrooms Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="bed_min" class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.bed_min" />
                                            <InputError :message="buyBoxForm.errors.bed_min" />
                                        </div>
                                        <div>
                                            <Label for="bed_max" class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.bed_max" />
                                            <InputError :message="buyBoxForm.errors.bed_max" />
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="cashflow_min" class="block mb-1 text-sm font-medium">Cashflow
                                        Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="cashflow_min" class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.cashflow_min" />
                                            <InputError :message="buyBoxForm.errors.cashflow_min" />
                                        </div>
                                        <div>
                                            <Label for="cashflow_max" class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.cashflow_max" />
                                            <InputError :message="buyBoxForm.errors.cashflow_max" />
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="delta_psf_min" class="block mb-1 text-sm font-medium">Delta PSF
                                        Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="delta_psf_min"
                                                class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.delta_psf_min" />
                                            <InputError :message="buyBoxForm.errors.delta_psf_min" />
                                        </div>
                                        <div>
                                            <Label for="delta_psf_max"
                                                class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.delta_psf_max" />
                                            <InputError :message="buyBoxForm.errors.delta_psf_max" />
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="flip_profit_min" class="block mb-1 text-sm font-medium">Flip Profit
                                        Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="flip_profit_min"
                                                class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.flip_profit_min" />
                                            <InputError :message="buyBoxForm.errors.flip_profit_min" />
                                        </div>
                                        <div>
                                            <Label for="flip_profit_max"
                                                class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.flip_profit_max" />
                                            <InputError :message="buyBoxForm.errors.flip_profit_max" />
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="price_min" class="block mb-1 text-sm font-medium">Price Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="price_min" class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.price_min" />
                                            <InputError :message="buyBoxForm.errors.price_min" />
                                        </div>
                                        <div>
                                            <Label for="price_max" class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.price_max" />
                                            <InputError :message="buyBoxForm.errors.price_max" />
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="sqft_min" class="block mb-1 text-sm font-medium">Square Footage
                                        Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="sqft_min" class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.sqft_min" />
                                            <InputError :message="buyBoxForm.errors.sqft_min" />
                                        </div>
                                        <div>
                                            <Label for="sqft_max" class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.sqft_max" />
                                            <InputError :message="buyBoxForm.errors.sqft_max" />
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="year_build_min" class="block mb-1 text-sm font-medium">Year Built
                                        Range</Label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <Label for="year_build_min"
                                                class="block mb-1 text-sm font-medium">Min</Label>
                                            <InputNumber v-model="buyBoxForm.year_build_min" />
                                            <InputError :message="buyBoxForm.errors.year_build_min" />
                                        </div>
                                        <div>
                                            <Label for="year_build_max"
                                                class="block mb-1 text-sm font-medium">Max</Label>
                                            <InputNumber v-model="buyBoxForm.year_build_max" />
                                            <InputError :message="buyBoxForm.errors.year_build_max" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <Button class="mt-4 w-full" :loading="buyBoxForm.processing" type="submit">Update Buy
                                Box</Button>
                        </form>
                    </div>
                    <div v-else-if="tab === 'email'" class="flex flex-col md:flex-row pt-2 px-2 gap-2 w-full h-full">
                        <form @submit.prevent="updateEmail"
                            class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow gap-4 text-base  h-full w-full">
                            <div class="text-xl font-bold mb-2">Email Preferences</div>

                            <div class="grid gap-2 mb-4">
                                <Label for="email">Email address</Label>
                                <Input disabled class="mt-1 block w-full" v-model="emailForm.email" />
                            </div>
                            <div class="space-y-6">
                                <table class="table-auto w-full">
                                    <tbody>

                                        <tr :for="`checkbox-global`">
                                            <td width="35px" class="align-top pt-1">
                                                <Checkbox :id="`checkbox-global`" v-model="emailForm.emailVerified"
                                                    :tabindex="3"></Checkbox>
                                            </td>
                                            <td>
                                                <div class="font-medium">Mark as verified</div>
                                                <p class="text-sm text-muted-foreground">{{ emailForm.emailVerifiedAt
                                                    ? 'at ' + formatDate(emailForm.emailVerifiedAt) : 'Not verified' }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="35px" class="align-top ">
                                                &nbsp;</td>
                                            <td class=" align-top ">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td width="35px" class="align-top border-t-1 border-t-gray-500 mt-2">
                                                &nbsp;</td>
                                            <td class=" align-top border-t-1 border-t-gray-500 mt-2">&nbsp;</td>
                                        </tr>


                                        <tr :for="`checkbox-${key}`" v-for="(item, key) in props.emailCategories"
                                            :key="key">
                                            <td width="35px" class="align-top pt-1">
                                                <CheckboxLoop :id="`checkbox-${key}`"
                                                    v-model="emailForm.emailUnsubscribedListPreference" :value="key"
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
                                                <Checkbox :id="`checkbox-global`" v-model="emailForm.global"
                                                    :tabindex="3"></Checkbox>
                                            </td>
                                            <td class="pt-3 align-top border-t-1 border-t-gray-500 mt-2">
                                                <div class="font-medium">Global Unsubscribe</div>
                                                <p class="text-sm text-muted-foreground">Unsubscribe from all emails
                                                    (this does not include important emails such as account or
                                                    security-related messages).</p>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>

                            <Button class="mt-4 w-full" :loading="emailForm.processing" type="submit">Update Email
                                Preferences</Button>
                        </form>
                    </div>
                    <div v-else-if="tab === 'business'" class="flex flex-col md:flex-row pt-2 px-2 gap-2 w-full h-full">



                        <form @submit.prevent="updateBusiness"
                            class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow gap-4 text-base  h-full w-full">
                            <div class="text-xl font-bold mb-2">Business Details</div>

                            <Dialog v-model:open="dialogOpen">
                                <DialogTrigger as-child>
                                    <div class="flex items-center gap-2 cursor-pointer my-5 justify-center">
                                        <Avatar class="size-24 overflow-hidden rounded-full border-2">
                                            <AvatarImage v-if="user.company_logo" :src="user.company_logo"
                                                :alt="user.name" />
                                            <AvatarFallback
                                                class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                                {{ getInitials(user?.name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <Icon icon="tabler:edit" class="size-6" />
                                    </div>

                                </DialogTrigger>
                                <DialogContent>

                                    <DialogHeader class="space-y-3">
                                        <DialogTitle>Upload a picture</DialogTitle>
                                    </DialogHeader>

                                    <div class="flex justify-center">
                                        <Input type="file" @change="onFileChange" class="" accept="image/*" />
                                    </div>
                                    <div class="flex justify-center">

                                        <cropper :hidden="!img" style="max-width: 320px; height: 320px"
                                            class="cropper rounded-xl content-center" :src="img" :auto-zoom="true"
                                            :stencil-props="{ aspectRatio: 1 }" @change="change" />
                                    </div>
                                    <div class="flex justify-center">
                                        <InputError class="mt-2" :message="avatar_error" />
                                    </div>
                                    <DialogFooter class="gap-2">
                                        <DialogClose as-child>
                                            <Button variant="secondary"> Cancel</Button>
                                        </DialogClose>

                                        <Button v-if="img" @click="emitCroppedImage" :disabled="avatar_loading">Upload
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <Label for="companyName" class="block mb-1 text-sm font-medium">Company Name</Label>
                                    <Input v-model="businessForm.companyName" />
                                    <InputError :message="businessForm.errors.companyName" />
                                </div>
                                <div>
                                    <Label for="companyEmail" class="block mb-1 text-sm font-medium">Company
                                        Email</Label>
                                    <Input v-model="businessForm.companyEmail" />
                                    <InputError :message="businessForm.errors.companyEmail" />
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="brokerageName" class="block mb-1 text-sm font-medium">Brokerage
                                        Name</Label>
                                    <Input v-model="businessForm.brokerageName" />
                                    <InputError :message="businessForm.errors.brokerageName" />
                                </div>
                                <div class="md:col-span-2">
                                    <Label for="agentLicenseNumber" class="block mb-1 text-sm font-medium">Agent License
                                        Number</Label>
                                    <Input v-model="businessForm.agentLicenseNumber" />
                                    <InputError :message="businessForm.errors.agentLicenseNumber" />
                                </div>
                            </div>
                            <Button class="mt-4 w-full" :loading="businessForm.processing" type="submit">Update Business
                                Details</Button>
                        </form>
                    </div>
                    <!-- Sidebar -->
                    <div class="w-full md:w-80 flex-shrink-0 flex flex-col gap-4 text-base h-full pt-2">
                        <div class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow">
                            <div class="font-bold text-lg mb-2">Total Count Summary</div>
                            <div class="flex flex-col gap-1">
                                <div class="flex justify-between"><span>Views</span><span>{{ summary.views }}</span>
                                </div>
                                <div class="flex justify-between"><span>Unlisted Views</span><span>{{
                                    summary.unlisted_views
                                        }}</span></div>
                                <div class="flex justify-between"><span>Searches</span><span>{{ summary.searches
                                }}</span></div>
                                <div class="flex justify-between"><span>Saved Searches</span><span>{{
                                    summary.saved_searches
                                        }}</span></div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow">
                            <div class="font-bold text-lg mb-2">Recent Views</div>
                            <div v-if="recentViews.length === 0" class="text-gray-400 text-sm">No recent views.</div>
                            <div v-for="view in recentViews" :key="view.id" class="flex gap-2 items-center mb-2">
                                <div
                                    class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs text-gray-500">
                                    #{{ view.id }}
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-medium text-sm truncate">{{ view.street_address }}</div>
                                    <div class="font-medium text-xs truncate text-muted-foreground">{{ view.city }}, {{
                                        view.state }} {{ view.zip }}</div>
                                    <div class="font-medium text-xs truncate text-muted-foreground">{{
                                        view.created_at ? formatDateTime(view.created_at) : '' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow">
                            <div class="font-bold text-lg mb-2">Saved Searches</div>
                            <div v-if="recentSavedSearches.length === 0" class="text-gray-400 text-sm">No saved
                                searches.</div>
                            <div v-for="search in recentSavedSearches" :key="search.id" class="mb-2">
                                <div class="font-medium text-sm">{{ search.saved_search_name }}</div>
                                <div class="text-xs text-gray-500">{{ search.default ? 'Default - ' : '' }}{{
                                    search.created_at
                                        ? formatDateTime(search.created_at) : '' }}</div>
                            </div>
                        </div>
                        <!-- Generate Lead Source -->
                        <div class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow">
                            <div class="font-bold text-lg mb-2">Lead Source</div>
                            <div v-if="!leadSource" class="text-gray-400 text-sm">No lead source.</div>
                            <div v-else class="mb-2">
                                <div class="font-medium text-sm">{{ leadSource.page_name }}</div>
                                <div class="text-xs text-gray-500">{{ leadSource.page_url }}</div>
                                <div class="text-xs text-gray-500">
                                    <Icon icon="tabler:device-mobile" v-if="isMobileUserAgent(leadSource.user_agent)"
                                        class="size-4" />
                                    <Icon icon="tabler:device-desktop" v-else class="size-4" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div v-else class="p-8 text-center text-gray-500">No user data.</div>
    </FullScreenDialog>
</template>