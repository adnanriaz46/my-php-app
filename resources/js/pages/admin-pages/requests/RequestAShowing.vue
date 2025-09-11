<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, RequestAShowing } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { useDateFormat } from '@/composables/useFormat';
import EmailReplyDialog from '@/components/admin/EmailReplyDialog.vue';

const { formatDate, formatDateTime } = useDateFormat();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Requests',
        href: route('admin.requests.showing'),
    },
    {
        title: 'Request A Showing',
        href: route('admin.requests.showing'),
    }
];

interface Paginator {
    data: RequestAShowing[];
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
const pagination = computed<Paginator>(() => (page.props.requestAShowings as Paginator) ?? defaultPaginator);
const requestAShowings = computed(() => pagination.value.data ?? []);

const search = ref('');
const stateFilter = ref('');
const sortBy = ref('created_at');
const emailDialogOpen = ref(false);
const selectedRequest = ref<RequestAShowing | null>(null);

function fetchRequestAShowings(pageNum = 1) {
    router.get(route('admin.requests.showing'), {
        search: search.value,
        state: stateFilter.value,
        sort_by: sortBy.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function replyByEmail(request: RequestAShowing) {
    selectedRequest.value = request;
    emailDialogOpen.value = true;
}

function getDefaultSubject(request: RequestAShowing): string {
    return `Re: Request for Showing - ${request.full_street_address}`;
}

function getDefaultContent(request: RequestAShowing): string {
    return `<p>Dear ${request.user?.name || 'there'},</p>

Thank you for your request to show the property at ${request.full_street_address}.
<br>
<p>We have received your request for a showing on ${formatDate(request.preferred_time)}.</p>

<p>${request.message ? `Your message: "${request.message}"` : ''}</p>

<p>We will contact you shortly to confirm the appointment details.</p>
<br>
<p>Best regards,</p>
<p>Revamp365 Team</p>`;
}

function getStatusColor(request: RequestAShowing): string {
    const now = new Date();
    const preferredTime = new Date(request.preferred_time);
    const diffHours = (preferredTime.getTime() - now.getTime()) / (1000 * 60 * 60);
    
    if (diffHours < 0) {
        return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'; // Past
    } else if (diffHours < 24) {
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'; // Today
    } else {
        return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'; // Future
    }
}

function getStatusText(request: RequestAShowing): string {
    const now = new Date();
    const preferredTime = new Date(request.preferred_time);
    const diffHours = (preferredTime.getTime() - now.getTime()) / (1000 * 60 * 60);
    
    if (diffHours < 0) {
        return 'Past';
    } else if (diffHours < 24) {
        return 'Today';
    } else {
        return 'Future';
    }
}
</script>

<template>
    <Head title="Request A Showing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="stateFilter" @change="fetchRequestAShowings()"
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
                    <select v-model="sortBy" @change="fetchRequestAShowings()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="created_at">Sort by Created</option>
                        <option value="preferred_time">Sort by Preferred Time</option>
                        <option value="full_street_address">Sort by Address</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchRequestAShowings()" type="text"
                        placeholder="Search by address, city, or user" class="rounded-r-none px-3 py-2" />
                    <Button @click="fetchRequestAShowings()" variant="default"
                        class="rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">Property Address</th>
                            <th class="px-4 py-2 text-left">Preferred Time</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Message</th>
                            <th class="px-4 py-2 text-left">Created</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="request in requestAShowings" :key="request.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ request.user?.name || 'N/A' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ request.user?.email || 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ request.full_street_address }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ request.city }}, {{ request.state }} {{ request.zip }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">{{ formatDateTime(request.preferred_time) }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusColor(request)">
                                    {{ getStatusText(request) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 max-w-xs">
                                <span v-if="request.message" class="truncate block" :title="request.message">
                                    {{ request.message }}
                                </span>
                                <span v-else class="text-gray-400">No message</span>
                            </td>
                            <td class="px-4 py-2 text-xs">{{ formatDate(request.created_at) }}</td>
                            <td class="px-4 py-2">
                                <Button @click="replyByEmail(request)" variant="outline" size="sm" class="text-xs">
                                    Reply by Email
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-2">
                <button v-if="pagination.prev_page_url" @click="fetchRequestAShowings(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchRequestAShowings(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
        </div>

        <!-- Email Reply Dialog -->
        <EmailReplyDialog
            v-if="selectedRequest"
            v-model:open="emailDialogOpen"
            :recipient-email="selectedRequest.user?.email || ''"
            :recipient-name="selectedRequest.user?.name || ''"
            :default-subject="getDefaultSubject(selectedRequest)"
            :default-content="getDefaultContent(selectedRequest)"
            request-type="showing"
            @email-sent="() => selectedRequest = null"
        />
    </AppLayout>
</template> 