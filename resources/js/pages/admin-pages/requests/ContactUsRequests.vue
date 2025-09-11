<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, ContactUsRequest } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { useDateFormat } from '@/composables/useFormat';
import EmailReplyDialog from '@/components/admin/EmailReplyDialog.vue';

const { formatDate } = useDateFormat();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Requests',
        href: route('admin.requests.contact-us'),
    },
    {
        title: 'Contact Us Requests',
        href: route('admin.requests.contact-us'),
    }
];

interface Paginator {
    data: ContactUsRequest[];
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
const pagination = computed<Paginator>(() => (page.props.contactUsRequests as Paginator) ?? defaultPaginator);
const contactUsRequests = computed(() => pagination.value.data ?? []);

const search = ref('');
const statusFilter = ref('');
const sortBy = ref('created_at');
const emailDialogOpen = ref(false);
const selectedRequest = ref<ContactUsRequest | null>(null);

function fetchContactUsRequests(pageNum = 1) {
    router.get(route('admin.requests.contact-us'), {
        search: search.value,
        status: statusFilter.value,
        sort_by: sortBy.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function replyByEmail(request: ContactUsRequest) {
    selectedRequest.value = request;
    emailDialogOpen.value = true;
}

function getDefaultSubject(request: ContactUsRequest): string {
    const fullName = request.full_name || `${request.first_name} ${request.last_name}`;
    return `Re: Contact Us Request - ${fullName}`;
}

function getDefaultContent(request: ContactUsRequest): string {
    const fullName = request.full_name || `${request.first_name} ${request.last_name}`;
    return `<p>Dear ${fullName},</p>

Thank you for reaching out to us.
<br>
<p>${request.message ? `Your message: "${request.message}"` : ''}</p>

<p>We have received your inquiry and will get back to you shortly with a detailed response.</p>

<p>Contact Information:</p>
<ul>
<li>Email: ${request.email}</li>
${request.phone ? `<li>Phone: ${request.phone}</li>` : ''}
</ul>
<br>
<p>Best regards,</p>
<p>Revamp365 Team</p>`;
}

function getStatusColor(status: string): string {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        case 'replied':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        case 'closed':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
}
</script>

<template>
    <Head title="Contact Us Requests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="statusFilter" @change="fetchContactUsRequests()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="replied">Replied</option>
                        <option value="closed">Closed</option>
                    </select>
                    <select v-model="sortBy" @change="fetchContactUsRequests()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="created_at">Sort by Created</option>
                        <option value="name">Sort by Name</option>
                        <option value="email">Sort by Email</option>
                        <option value="status">Sort by Status</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchContactUsRequests()" type="text"
                        placeholder="Search by name, email, or message" class="rounded-r-none px-3 py-2" />
                    <Button @click="fetchContactUsRequests()" variant="default"
                        class="rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Contact Info</th>
                            <th class="px-4 py-2 text-left">Message</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Created</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="request in contactUsRequests" :key="request.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ request.full_name || `${request.first_name} ${request.last_name}` }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ request.email }}</span>
                                    <span v-if="request.phone" class="text-xs text-gray-500 dark:text-gray-400">{{ request.phone }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 max-w-xs">
                                <span v-if="request.message" class="truncate block" :title="request.message">
                                    {{ request.message }}
                                </span>
                                <span v-else class="text-gray-400">No message provided</span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusColor(request.status)">
                                    {{ request.status }}
                                </span>
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
                <button v-if="pagination.prev_page_url" @click="fetchContactUsRequests(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchContactUsRequests(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
        </div>

        <!-- Email Reply Dialog -->
        <EmailReplyDialog
            v-if="selectedRequest"
            v-model:open="emailDialogOpen"
            :recipient-email="selectedRequest.email"
            :recipient-name="selectedRequest.full_name || `${selectedRequest.first_name} ${selectedRequest.last_name}`"
            :default-subject="getDefaultSubject(selectedRequest)"
            :default-content="getDefaultContent(selectedRequest)"
            request-type="contact"
            @email-sent="() => selectedRequest = null"
        />
    </AppLayout>
</template> 