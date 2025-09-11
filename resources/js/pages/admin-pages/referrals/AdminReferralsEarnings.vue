<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, UserReferralEarning, User } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import TextArea from '@/components/ui/textarea/TextArea.vue';
import { Badge } from '@/components/ui/badge';
import { getInitials } from '@/composables/useInitials';
import { useToast } from '@/composables/useToast';
import { useNumber } from '@/composables/useFormat';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { CheckCircle, XCircle, Clock, DollarSign, Edit} from 'lucide-vue-next';

const { showToast } = useToast();
const { formatPrice } = useNumber();

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
        title: 'Earnings',
        href: '/admin/referrals/earnings',
    }
];

interface EarningData extends UserReferralEarning {
    user?: User;
    fromUser?: User;
}

interface Paginator {
    data: EarningData[];
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
const pagination = computed<Paginator>(() => (page.props.earnings as Paginator) ?? defaultPaginator);
const earnings = computed(() => pagination.value.data ?? []);

const search = ref('');
const statusFilter = ref('');
const typeFilter = ref('');
const sortBy = ref('created_at');
const sortOrder = ref('desc');

// Dialog state
const showApprovalDialog = ref(false);
const showPaymentDialog = ref(false);
const selectedEarning = ref<EarningData | null>(null);

// Form for approval/remarks
const approvalForm = useForm({
    earning_id: 0,
    approved: false,
    notes: '',
});

// Form for payment
const paymentForm = useForm({
    earning_id: 0,
    notes: '',
});

function fetchEarnings(pageNum = 1) {
    router.get(route('admin.referrals.earnings'), {
        search: search.value,
        status: statusFilter.value,
        type: typeFilter.value,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function openApprovalDialog(earning: EarningData) {
    selectedEarning.value = earning;
    approvalForm.earning_id = earning.id;
    approvalForm.approved = earning.status === 'approved';
    approvalForm.notes = earning.notes || '';
    showApprovalDialog.value = true;
}

function openPaymentDialog(earning: EarningData) {
    selectedEarning.value = earning;
    paymentForm.earning_id = earning.id;
    paymentForm.notes = '';
    showPaymentDialog.value = true;
}

function submitApproval() {
    approvalForm.patch(route('admin.referrals.earnings.approve'), {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Success', 'Earning status updated successfully', 'success');
            showApprovalDialog.value = false;
            fetchEarnings();
        },
        onError: (errors) => {
            console.log('errors', errors);    
            showToast('Error', 'Failed to update earning status', 'error');
        },
    });
}

function submitPayment() {
    paymentForm.patch(route('admin.referrals.earnings.pay'), {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Success', 'Earning marked as paid successfully', 'success');
            showPaymentDialog.value = false;
            fetchEarnings();
        },
        onError: (errors) => {
            console.log('errors', errors);    
            showToast('Error', 'Failed to mark earning as paid', 'error');
        },
    });
}

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'approved':
            return CheckCircle;
        case 'paid':
            return DollarSign;
        case 'rejected':
            return XCircle;
        default:
            return Clock;
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'approved':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'paid':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'rejected':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'cancelled':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

const getTypeColor = (type: string) => {
    switch (type) {
        case 'subscription_upgrade':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'first_payment':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'recurring_payment':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
        case 'bonus':
            return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

// Summary statistics
const summaryStats = computed(() => {
    const totalEarnings = pagination.value.total;
    const totalAmount = earnings.value.reduce((sum, earning) => sum + parseFloat(earning.amount), 0);
    const pendingEarnings = earnings.value.filter(earning => earning.status === 'pending').length;
    const approvedEarnings = earnings.value.filter(earning => earning.status === 'approved').length;
    const paidEarnings = earnings.value.filter(earning => earning.status === 'paid').length;
    
    return {
        totalEarnings,
        totalAmount,
        pendingEarnings,
        approvedEarnings,
        paidEarnings
    };
});
</script>

<template>
    <Head title="Admin - Referral Earnings" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-blue-600">{{ summaryStats.totalEarnings }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Earnings</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-green-600">{{ formatPrice(summaryStats.totalAmount) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Amount</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-yellow-600">{{ summaryStats.pendingEarnings }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pending</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-green-600">{{ summaryStats.approvedEarnings }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Approved</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-blue-600">{{ summaryStats.paidEarnings }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Paid</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="statusFilter" @change="fetchEarnings()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="paid">Paid</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select v-model="typeFilter" @change="fetchEarnings()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All Types</option>
                        <option value="subscription_upgrade">Subscription Upgrade</option>
                        <option value="first_payment">First Payment</option>
                        <option value="recurring_payment">Recurring Payment</option>
                        <option value="bonus">Bonus</option>
                    </select>
                    <select v-model="sortBy" @change="fetchEarnings()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="created_at">Sort by Date</option>
                        <option value="amount">Sort by Amount</option>
                        <option value="status">Sort by Status</option>
                        <option value="type">Sort by Type</option>
                    </select>
                    <select v-model="sortOrder" @change="fetchEarnings()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchEarnings()" type="text"
                        placeholder="Search by user name, email, or description" class="ml-auto rounded-r-none px-3 py-2" />
                    <Button @click="fetchEarnings()" variant="default"
                        class="ml-auto rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>

            <!-- Earnings Table -->
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Referrer</th>
                            <th class="px-4 py-2 text-left">Affiliate</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-left">Amount</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Description</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="earning in earnings" :key="earning.id" class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <img v-if="earning.user?.profile_picture" :src="earning.user.profile_picture"
                                        class="w-8 h-8 rounded-full object-cover" />
                                    <div v-else
                                        class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-300">{{ getInitials(earning.user?.name || '') }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ earning.user?.name }}</div>
                                        <div class="text-xs text-gray-500">{{ earning.user?.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <img v-if="earning.fromUser?.profile_picture" :src="earning.fromUser.profile_picture"
                                        class="w-6 h-6 rounded-full object-cover" />
                                    <div v-else
                                        class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-300 text-xs">{{ getInitials(earning.fromUser?.name || '') }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-sm">{{ earning.fromUser?.name }}</div>
                                        <div class="text-xs text-gray-500">{{ earning.fromUser?.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <Badge :class="getTypeColor(earning.type)">
                                    {{ earning.type.replace('_', ' ').toUpperCase() }}
                                </Badge>
                            </td>
                            <td class="px-4 py-2 font-medium">{{ formatPrice(earning.amount) }}</td>
                            <td class="px-4 py-2">
                                <Badge :class="getStatusColor(earning.status)">
                                    <component :is="getStatusIcon(earning.status)" class="h-3 w-3 mr-1" />
                                    {{ earning.status.toUpperCase() }}
                                </Badge>
                            </td>
                            <td class="px-4 py-2">{{ new Date(earning.created_at).toLocaleDateString() }}</td>
                            <td class="px-4 py-2 max-w-xs truncate" :title="earning.description">
                                {{ earning.description }}
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <Button 
                                        v-if="earning.status === 'pending'"
                                        variant="outline" 
                                        size="sm"
                                        @click="openApprovalDialog(earning)"
                                        class="flex items-center gap-1"
                                    >
                                        <Edit class="h-3 w-3" />
                                        Review
                                    </Button>
                                    <Button 
                                        v-if="earning.status === 'approved'"
                                        variant="outline" 
                                        size="sm"
                                        @click="openPaymentDialog(earning)"
                                        class="flex items-center gap-1"
                                    >
                                        <DollarSign class="h-3 w-3" />
                                        Pay
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
                    <button v-if="pagination.prev_page_url" @click="fetchEarnings(pagination.current_page - 1)"
                        class="px-3 py-1 rounded border bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                    <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                    <button v-if="pagination.next_page_url" @click="fetchEarnings(pagination.current_page + 1)"
                        class="px-3 py-1 rounded border bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
                </div>
            </div>
        </div>

        <!-- Approval Dialog -->
        <Dialog v-model:open="showApprovalDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Review Earning</DialogTitle>
                    <DialogDescription>
                        Update the approval status and add notes for this earning.
                    </DialogDescription>
                </DialogHeader>
                
                <div class="space-y-4">
                    <div v-if="selectedEarning">
                        <div class="flex items-center gap-2 mb-2">
                            <DollarSign class="h-4 w-4 text-green-500" />
                            <span class="font-medium">{{ formatPrice(selectedEarning.amount) }}</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            From: {{ selectedEarning.fromUser?.name }} ({{ selectedEarning.fromUser?.email }})
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            To: {{ selectedEarning.user?.name }} ({{ selectedEarning.user?.email }})
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Type: {{ selectedEarning.type.replace('_', ' ').toUpperCase() }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Date: {{ new Date(selectedEarning.created_at).toLocaleDateString() }}
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
                        <label class="text-sm font-medium">Notes (Optional)</label>
                        <TextArea 
                            v-model="approvalForm.notes"
                            placeholder="Add any notes about this earning..."
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

        <!-- Payment Dialog -->
        <Dialog v-model:open="showPaymentDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Mark as Paid</DialogTitle>
                    <DialogDescription>
                        Mark this earning as paid and add any payment notes.
                    </DialogDescription>
                </DialogHeader>
                
                <div class="space-y-4">
                    <div v-if="selectedEarning">
                        <div class="flex items-center gap-2 mb-2">
                            <DollarSign class="h-4 w-4 text-green-500" />
                            <span class="font-medium">{{ formatPrice(selectedEarning.amount) }}</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            From: {{ selectedEarning.fromUser?.name }} ({{ selectedEarning.fromUser?.email }})
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            To: {{ selectedEarning.user?.name }} ({{ selectedEarning.user?.email }})
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Type: {{ selectedEarning.type.replace('_', ' ').toUpperCase() }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Payment Notes (Optional)</label>
                        <TextArea 
                            v-model="paymentForm.notes"
                            placeholder="Add payment notes (e.g., payment method, transaction ID)..."
                            rows="3"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button 
                        @click="submitPayment" 
                        :disabled="paymentForm.processing"
                        :loading="paymentForm.processing"
                    >
                        Mark as Paid
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template> 