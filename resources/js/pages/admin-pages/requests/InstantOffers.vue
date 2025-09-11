<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, InstantOffer } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { useDateFormat } from '@/composables/useFormat';
import EmailReplyDialog from '@/components/admin/EmailReplyDialog.vue';

const { formatDate } = useDateFormat();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Requests',
        href: route('admin.requests.instant-offers'),
    },
    {
        title: 'Instant Offers',
        href: route('admin.requests.instant-offers'),
    }
];

interface Paginator {
    data: InstantOffer[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

const defaultPaginator: Paginator = {
    data: [],
    current_page: 1,
    last_page: 1,
    prev_page_url: null,
    next_page_url: null,
};

const page = usePage();
const pagination = computed<Paginator>(() => (page.props.instantOffers as Paginator) ?? defaultPaginator);
const instantOffers = computed(() => pagination.value.data ?? []);

const search = ref('');
const stateFilter = ref('');
const sortBy = ref('created_at');
const emailDialogOpen = ref(false);
const selectedOffer = ref<InstantOffer | null>(null);

function fetchInstantOffers(pageNum = 1) {
    router.get(route('admin.requests.instant-offers'), {
        search: search.value,
        state: stateFilter.value,
        sort_by: sortBy.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function replyByEmail(offer: InstantOffer) {
    selectedOffer.value = offer;
    emailDialogOpen.value = true;
}

function getDefaultSubject(offer: InstantOffer): string {
    return `Re: Instant Offer - ${offer.full_street_address || offer.address || 'Property'}`;
}

function getDefaultContent(offer: InstantOffer): string {
    return `<p>Dear ${offer.name || offer.user?.name || 'there'},</p>

Thank you for your instant offer submission.
<br>
<p>Property: ${offer.full_street_address || offer.address || `Property ID: ${offer.property_id}`}</p>
<p>Offer Price: $${offer.offer_price?.toLocaleString() || 'N/A'}</p>
<p>Deposit Price: $${offer.deposit_price?.toLocaleString() || 'N/A'}</p>
<p>Preferred Closing Date: ${offer.preferred_closing_date ? formatDate(offer.preferred_closing_date) : 'Not specified'}</p>

<p>${offer.note ? `Additional Notes: "${offer.note}"` : ''}</p>

<p>We have received your offer and will review it carefully. We will contact you shortly with our response.</p>
<br>
<p>Best regards,</p>
<p>Revamp365 Team</p>`;
}

function formatCurrency(amount: number | null): string {
    if (amount === null || amount === undefined) return 'N/A';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}
</script>

<template>

    <Head title="Instant Offers" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="stateFilter" @change="fetchInstantOffers()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All States</option>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>
                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="OR">Oregon</option>
                        <option value="PA">Pennsylvania</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>
                        <option value="VA">Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                    </select>
                    <select v-model="sortBy" @change="fetchInstantOffers()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="created_at">Sort by Created</option>
                        <option value="offer_price">Sort by Offer Price</option>
                        <option value="name">Sort by Name</option>
                        <option value="full_street_address">Sort by Address</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchInstantOffers()" type="text"
                        placeholder="Search by name, email, address, or buyer name" class="rounded-r-none px-3 py-2" />
                    <Button @click="fetchInstantOffers()" variant="default"
                        class="rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Contact Info</th>
                            <th class="px-4 py-2 text-left">Property Address</th>
                            <th class="px-4 py-2 text-left">Offer Details</th>
                            <th class="px-4 py-2 text-left">Buyer Info</th>
                            <th class="px-4 py-2 text-left">Agent Info</th>
                            <th class="px-4 py-2 text-left">Created</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="offer in instantOffers" :key="offer.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ offer.name || offer.user?.name || 'N/A' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ offer.email ||
                                        offer.user?.email || 'N/A' }}</span>
                                    <span v-if="offer.phone" class="text-xs text-gray-500 dark:text-gray-400">{{
                                        offer.phone }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span v-if="offer.full_street_address" class="font-medium">{{
                                        offer.full_street_address }}</span>
                                    <span v-else-if="offer.address" class="font-medium">{{ offer.address }}</span>
                                    <span v-else class="font-medium">Property ID: {{ offer.property_id }}</span>
                                    <span v-if="offer.city && offer.state"
                                        class="text-xs text-gray-500 dark:text-gray-400">{{ offer.city }}, {{
                                            offer.state }} {{ offer.zip || '' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span class="font-medium text-green-600 dark:text-green-400">Offer: {{
                                        formatCurrency(offer.offer_price) }}</span>
                                    <span class="text-xs">Deposit: {{ formatCurrency(offer.deposit_price) }}</span>
                                    <span v-if="offer.preferred_closing_date"
                                        class="text-xs text-gray-500 dark:text-gray-400">Closing: {{
                                            formatDate(offer.preferred_closing_date) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span v-if="offer.buyer_name_llc" class="font-medium">{{ offer.buyer_name_llc
                                    }}</span>
                                    <span v-if="offer.tin" class="text-xs text-gray-500 dark:text-gray-400">TIN: {{
                                        offer.tin }}</span>
                                    <span v-if="offer.assignor_name"
                                        class="text-xs text-gray-500 dark:text-gray-400">Assignor: {{
                                            offer.assignor_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span v-if="offer.agent_name" class="font-medium">{{ offer.agent_name }}</span>
                                    <span v-if="offer.agent_email" class="text-xs text-gray-500 dark:text-gray-400">{{
                                        offer.agent_email }}</span>
                                    <span v-if="offer.agent_commission"
                                        class="text-xs text-gray-500 dark:text-gray-400">Commission: {{
                                            formatCurrency(offer.agent_commission) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-xs">{{ formatDate(offer.created_at) }}</td>
                            <td class="px-4 py-2">
                                <Button @click="replyByEmail(offer)" variant="outline" size="sm" class="text-xs">
                                    Reply by Email
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-2">
                <button v-if="pagination.prev_page_url" @click="fetchInstantOffers(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchInstantOffers(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
        </div>

        <!-- Email Reply Dialog -->
        <EmailReplyDialog v-if="selectedOffer" v-model:open="emailDialogOpen"
            :recipient-email="selectedOffer.email || selectedOffer.user?.email || ''"
            :recipient-name="selectedOffer.name || selectedOffer.user?.name || ''"
            :default-subject="getDefaultSubject(selectedOffer)" :default-content="getDefaultContent(selectedOffer)"
            request-type="offer" @email-sent="() => selectedOffer = null" />
    </AppLayout>
</template>