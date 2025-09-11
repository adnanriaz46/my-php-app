<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, User } from '@/types';
import { UserTypes } from '@/types/userTypes';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { getInitials } from '@/composables/useInitials';
import { useNumber } from '@/composables/useFormat';

const { formatNumber, formatPrice } = useNumber();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: '/admin',
    },
    {
        title: 'Referrals',
        href: '/admin/referrals',
    }
];

interface ReferralData extends User {
    affiliates_count?: number;
    eligible_affiliates_count?: number;
    total_earnings?: number;
}

interface Paginator {
    data: ReferralData[];
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
const pagination = computed<Paginator>(() => (page.props.referrals as Paginator) ?? defaultPaginator);
const referrals = computed(() => pagination.value.data ?? []);

const search = ref('');
const userType = ref('');
const sortBy = ref('created_at');
const sortOrder = ref('desc');

function fetchReferrals(pageNum = 1) {
    router.get(route('admin.referrals.index'), {
        search: search.value,
        user_type: userType.value,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function onRowClick(referral: ReferralData) {
    // Navigate to referral detail or open dialog
    console.log('Referral clicked:', referral);
}

// Summary statistics
const summaryStats = computed(() => {
    const totalReferrals = pagination.value.total;
    const totalAffiliates = referrals.value.reduce((sum, referral) => sum + (referral.affiliates_count || 0), 0);
    const totalEligibleAffiliates = referrals.value.reduce((sum, referral) => sum + (referral.eligible_affiliates_count || 0), 0);
    const totalEarnings = referrals.value.reduce((sum, referral) => sum + (referral.total_earnings || 0), 0);
    
    return {
        totalReferrals,
        totalAffiliates,
        totalEligibleAffiliates,
        totalEarnings
    };
});
</script>

<template>
    <Head title="Admin - Referrals" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-blue-600">{{ formatNumber(summaryStats.totalReferrals) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Referrers</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-green-600">{{ formatNumber(summaryStats.totalAffiliates) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Affiliates</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-purple-600">{{ formatNumber(summaryStats.totalEligibleAffiliates) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Eligible Affiliates</div>
                </div>
                <div class="border rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-orange-600">{{ formatPrice(summaryStats.totalEarnings) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Earnings</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="userType" @change="fetchReferrals()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All Types</option>
                        <option :value="UserTypes.ADMIN">Admin</option>
                        <option :value="UserTypes.PREMIUM">Premium</option>
                        <option :value="UserTypes.FREE">Free</option>
                    </select>
                    <select v-model="sortBy" @change="fetchReferrals()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="name">Sort by Name</option>
                        <option value="created_at">Sort by Created</option>
                        <option value="affiliates_count">Sort by Affiliates</option>
                        <option value="total_earnings">Sort by Earnings</option>
                    </select>
                    <select v-model="sortOrder" @change="fetchReferrals()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchReferrals()" type="text"
                        placeholder="Search by name, email, or phone" class="ml-auto rounded-r-none px-3 py-2" />
                    <Button @click="fetchReferrals()" variant="default"
                        class="ml-auto rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>

            <!-- Referrals Table -->
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Avatar</th>
                            <th class="px-4 py-2 text-left">Full Name</th>
                            <th class="px-4 py-2 text-left">User Type</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Phone</th>
                            <th class="px-4 py-2 text-left">Affiliates</th>
                            <th class="px-4 py-2 text-left">Eligible</th>
                            <th class="px-4 py-2 text-left">Earnings</th>
                            <th class="px-4 py-2 text-left">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="referral in referrals" :key="referral.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer" @click="onRowClick(referral)">
                            <td class="px-4 py-2">
                                <img v-if="referral.profile_picture" :src="referral.profile_picture"
                                    class="w-8 h-8 rounded-full object-cover" />
                                <div v-else
                                    class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-300">{{ getInitials(referral.name) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">{{ referral.name }}</td>
                            <td class="px-4 py-2">{{ referral.user_type == UserTypes.ADMIN ? 'Admin' : referral.user_type ==
                                UserTypes.PREMIUM ? 'Premium' : 'Free' }}</td>
                            <td class="px-4 py-2">{{ referral.email }}</td>
                            <td class="px-4 py-2">{{ referral.phone_number }}</td>
                            <td class="px-4 py-2">{{ referral.affiliates_count || 0 }}</td>
                            <td class="px-4 py-2">{{ referral.eligible_affiliates_count || 0 }}</td>
                            <td class="px-4 py-2">{{ formatPrice(referral.total_earnings || 0) }}</td>
                            <td class="px-4 py-2">{{ new Date(referral.created_at).toLocaleDateString() }}</td>
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
                    <button v-if="pagination.prev_page_url" @click="fetchReferrals(pagination.current_page - 1)"
                        class="px-3 py-1 rounded border bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                    <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                    <button v-if="pagination.next_page_url" @click="fetchReferrals(pagination.current_page + 1)"
                        class="px-3 py-1 rounded border bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 