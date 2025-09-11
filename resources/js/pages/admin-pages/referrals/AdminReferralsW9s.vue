<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, UserReferralW9, User } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import TextArea from '@/components/ui/textarea/TextArea.vue';
import { Badge } from '@/components/ui/badge';
import { getInitials } from '@/composables/useInitials';
import { useToast } from '@/composables/useToast';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { CheckCircle, Clock, FileText, ExternalLink, Edit } from 'lucide-vue-next';

const { showToast } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: '/admin',
    },
    {
        title: 'Referrals',
        href: '/admin/referrals',
    },
    {
        title: 'W9 Documents',
        href: '/admin/referrals/w9s',
    }
];

interface W9Data extends UserReferralW9 {
    user?: User;
}

interface Paginator {
    data: W9Data[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    total: number;
}

const defaultPaginator: Paginator = {
    data: [],
    current_page: 1,
    last_page: 1,
    prev_page_url: null,
    next_page_url: null,
    total: 0,
};

const page = usePage();
const pagination = computed<Paginator>(() => (page.props.w9s as Paginator) ?? defaultPaginator);
const w9s = computed(() => pagination.value.data ?? []);

const search = ref('');
const statusFilter = ref('');
const sortBy = ref('created_at');
const sortOrder = ref('desc');

// Dialog state
const showApprovalDialog = ref(false);
const selectedW9 = ref<W9Data | null>(null);

// Form for approval/remarks
const approvalForm = useForm({
    w9_id: 0,
    approved: false,
    remarks: '',
});

function fetchW9s(pageNum = 1) {
    router.get(route('admin.referrals.w9s'), {
        search: search.value,
        status: statusFilter.value,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function openApprovalDialog(w9: W9Data) {
    selectedW9.value = w9;
    approvalForm.w9_id = w9.id;
    approvalForm.approved = w9.approved;
    approvalForm.remarks = w9.remarks || '';
    showApprovalDialog.value = true;
}

function submitApproval() {
    approvalForm.patch(route('admin.referrals.w9s.approve'), {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Success', 'W9 document status updated successfully', 'success');
            showApprovalDialog.value = false;
            fetchW9s();
        },
        onError: (errors) => {
            console.log('errors', errors);    
            showToast('Error', 'Failed to update W9 document status', 'error');
        },
    });
}

function openFile(fileUrl: string) {
    window.open(fileUrl, '_blank');
}

const getStatusIcon = (approved: boolean) => {
    if (approved) {
        return CheckCircle;
    }
    return Clock;
};

const getStatusColor = (approved: boolean) => {
    if (approved) {
        return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    }
    return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
};

const getStatusText = (approved: boolean) => {
    if (approved) {
        return 'Approved';
    }
    return 'Pending Review';
};

// Summary statistics
const summaryStats = computed(() => {
    const totalW9s = pagination.value.total;
    const approvedW9s = w9s.value.filter(w9 => w9.approved).length;
    const pendingW9s = w9s.value.filter(w9 => !w9.approved).length;
    
    return {
        totalW9s,
        approvedW9s,
        pendingW9s
    };
});
</script>

<template>
    <Head title="Admin - W9 Documents" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-blue-600">{{ summaryStats.totalW9s }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total W9 Documents</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-green-600">{{ summaryStats.approvedW9s }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Approved</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-yellow-600">{{ summaryStats.pendingW9s }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pending Review</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="statusFilter" @change="fetchW9s()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                    </select>
                    <select v-model="sortBy" @change="fetchW9s()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="created_at">Sort by Upload Date</option>
                        <option value="name">Sort by File Name</option>
                        <option value="user_name">Sort by User Name</option>
                        <option value="approved">Sort by Status</option>
                    </select>
                    <select v-model="sortOrder" @change="fetchW9s()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchW9s()" type="text"
                        placeholder="Search by file name, user name, or email" class="ml-auto rounded-r-none px-3 py-2" />
                    <Button @click="fetchW9s()" variant="default"
                        class="ml-auto rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>

            <!-- W9s Table -->
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">File Name</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Upload Date</th>
                            <th class="px-4 py-2 text-left">Remarks</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="w9 in w9s" :key="w9.id" class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <img v-if="w9.user?.profile_picture" :src="w9.user.profile_picture"
                                        class="w-8 h-8 rounded-full object-cover" />
                                    <div v-else
                                        class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-300">{{ getInitials(w9.user?.name || '') }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ w9.user?.name }}</div>
                                        <div class="text-xs text-gray-500">{{ w9.user?.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <FileText class="h-4 w-4 text-blue-500" />
                                    <span class="truncate max-w-xs">{{ w9.name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <Badge :class="getStatusColor(w9.approved)">
                                    <component :is="getStatusIcon(w9.approved)" class="h-3 w-3 mr-1" />
                                    {{ getStatusText(w9.approved) }}
                                </Badge>
                            </td>
                            <td class="px-4 py-2">{{ new Date(w9.created_at).toLocaleDateString() }}</td>
                            <td class="px-4 py-2">
                                <span v-if="w9.remarks" class="text-sm text-gray-600 dark:text-gray-400 truncate max-w-xs">
                                    {{ w9.remarks }}
                                </span>
                                <span v-else class="text-sm text-gray-400">-</span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        @click="openFile(w9.file_url)"
                                        class="flex items-center gap-1"
                                    >
                                        <ExternalLink class="h-3 w-3" />
                                        View
                                    </Button>
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        @click="openApprovalDialog(w9)"
                                        class="flex items-center gap-1"
                                    >
                                        <Edit class="h-3 w-3" />
                                        Review
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-2">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ ((pagination.current_page - 1) * 15) + 1 }} to {{ Math.min(pagination.current_page * 15, pagination.total) }} of {{ pagination.total }} results
                </div>
                <div class="flex gap-2">
                    <button v-if="pagination.prev_page_url" @click="fetchW9s(pagination.current_page - 1)"
                        class="px-3 py-1 rounded border bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                    <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                    <button v-if="pagination.next_page_url" @click="fetchW9s(pagination.current_page + 1)"
                        class="px-3 py-1 rounded border bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
                </div>
            </div>
        </div>

        <!-- Approval Dialog -->
        <Dialog v-model:open="showApprovalDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Review W9 Document</DialogTitle>
                    <DialogDescription>
                        Update the approval status and add remarks for this W9 document.
                    </DialogDescription>
                </DialogHeader>
                
                <div class="space-y-4">
                    <div v-if="selectedW9">
                        <div class="flex items-center gap-2 mb-2">
                            <FileText class="h-4 w-4 text-blue-500" />
                            <span class="font-medium">{{ selectedW9.name }}</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Uploaded by: {{ selectedW9.user?.name }} ({{ selectedW9.user?.email }})
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Date: {{ new Date(selectedW9.created_at).toLocaleDateString() }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Approval Status</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input 
                                    type="radio" 
                                    v-model="approvalForm.approved" 
                                    :value="true"
                                    class="rounded border-gray-300"
                                />
                                <span class="text-sm">Approve</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input 
                                    type="radio" 
                                    v-model="approvalForm.approved" 
                                    :value="false"
                                    class="rounded border-gray-300"
                                />
                                <span class="text-sm">Reject</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Remarks (Optional)</label>
                        <TextArea 
                            v-model="approvalForm.remarks"
                            placeholder="Add any remarks or notes about this W9 document..."
                            rows="3"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button 
                        @click="submitApproval" 
                        :disabled="approvalForm.processing"
                        :loading="approvalForm.processing"
                    >
                        Update Status
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template> 