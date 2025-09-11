<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, SupportTicket } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { useDateFormat } from '@/composables/useFormat';
import {
    supportTicketStatusOptions,
    supportTicketPriorityOptions,
    supportTicketSortOptions,
    getStatusColor,
    getStatusLabel,
    getPriorityColor,
    getPriorityLabel
} from '@/lib/supportTicketUtils';

const { formatDate } = useDateFormat();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Support Tickets',
        href: route('admin.support-tickets'),
    }
];

interface Paginator {
    data: SupportTicket[];
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
const pagination = computed<Paginator>(() => (page.props.supportTickets as Paginator) ?? defaultPaginator);
const supportTickets = computed(() => pagination.value.data ?? []);

const search = ref('');
const statusFilter = ref('');
const priorityFilter = ref('');
const categoryFilter = ref('');
const sortBy = ref('created_at');
const sortDir = ref('desc');

const statusOptions = supportTicketStatusOptions;
const priorityOptions = supportTicketPriorityOptions;
const sortOptions = supportTicketSortOptions;

function fetchSupportTickets(pageNum = 1) {
    router.get(route('admin.support-tickets'), {
        search: search.value,
        status: statusFilter.value,
        priority: priorityFilter.value,
        category: categoryFilter.value,
        sort_by: sortBy.value,
        sort_dir: sortDir.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function onRowClick(ticket: SupportTicket) {
    router.visit(route('admin.support-tickets.show', ticket.id));
}

</script>

<template>

    <Head title="Support Tickets" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="statusFilter" @change="fetchSupportTickets()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <select v-model="priorityFilter" @change="fetchSupportTickets()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option v-for="option in priorityOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <select v-model="sortBy" @change="fetchSupportTickets()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                            Sort by {{ option.label }}
                        </option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchSupportTickets()" type="text"
                        placeholder="Search by ticket number, title, or user" class="rounded-r-none px-3 py-2" />
                    <Button @click="fetchSupportTickets()" variant="default"
                        class="rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Ticket #</th>
                            <th class="px-4 py-2 text-left">Title</th>
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">Categories</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Priority</th>
                            <th class="px-4 py-2 text-left">Created</th>
                            <th class="px-4 py-2 text-left">Last Reply</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ticket in supportTickets" :key="ticket.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer" @click="onRowClick(ticket)">
                            <td class="px-4 py-2 font-mono text-xs">{{ ticket.ticket_number }}</td>
                            <td class="px-4 py-2 max-w-xs">
                                <span class="truncate block" :title="ticket.title">
                                    {{ ticket.title }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ ticket.user?.name || 'N/A' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ ticket.user?.email ||
                                        'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 max-w-xs">
                                <span class="truncate block text-xs" :title="ticket.categories_string">
                                    {{ ticket.categories_string }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getStatusColor(ticket.status)">
                                    {{ getStatusLabel(ticket.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getPriorityColor(ticket.priority)">
                                    {{ getPriorityLabel(ticket.priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-xs">{{ formatDate(ticket.created_at) }}</td>
                            <td class="px-4 py-2 text-xs">
                                <span v-if="ticket.replies && ticket.replies.length > 0">
                                    {{ formatDate(ticket.replies[0].created_at) }}
                                </span>
                                <span v-else class="text-gray-400">No replies</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-2">
                <button v-if="pagination.prev_page_url" @click="fetchSupportTickets(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchSupportTickets(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
        </div>
    </AppLayout>
</template>