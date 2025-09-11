<template>

    <Head title="Skip Trace Analytics" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <PageHeading title="Skip Trace Analytics" />

        <div class="p-6">
            <!-- Overall Stats Dashboard -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-lg font-semibold">Overall Statistics</div>
                    <div class="flex md:flex-row flex-col items-center gap-2">
                        <span class="text-sm text-gray-500">Last {{ lastDays }} days</span>
                        <div class="flex gap-1 ml-2 text-sm">
                            <button v-for="option in rangeOptions" :key="option.value"
                                :class="['px-2 py-1 rounded border', lastDays === option.value ? 'bg-white dark:bg-gray-900 border-primary text-primary dark:text-primary font-bold' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200']"
                                @click="changePeriod(option.value)">
                                {{ option.label }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="bg-blue-100 dark:bg-blue-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                        <div class="text-sm font-medium mb-1">Total Users</div>
                        <div class="text-3xl font-bold">{{ overallStats.total_users }}</div>
                        <div class="text-xs mt-1 text-gray-600">Active users</div>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                        <div class="text-sm font-medium mb-1">Total Skip Traces</div>
                        <div class="text-3xl font-bold">{{ overallStats.total_skip_traces }}</div>
                        <div class="text-xs mt-1 text-gray-600">Across all users</div>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                        <div class="text-sm font-medium mb-1">Successful Returns</div>
                        <div class="text-3xl font-bold text-green-600">{{ overallStats.total_successful }}</div>
                        <div class="text-xs mt-1 text-green-600">{{ overallStats.overall_success_rate }}% success rate
                        </div>
                    </div>
                    <div class="bg-red-100 dark:bg-red-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                        <div class="text-sm font-medium mb-1">Failed Returns</div>
                        <div class="text-3xl font-bold text-red-600">{{ overallStats.total_failed }}</div>
                        <div class="text-xs mt-1 text-red-600">No contact info</div>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                        <div class="text-sm font-medium mb-1">Emails Found</div>
                        <div class="text-3xl font-bold text-blue-600">{{ overallStats.total_emails_returned }}</div>
                        <div class="text-xs mt-1 text-blue-600">Total emails</div>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                        <div class="text-sm font-medium mb-1">Phones Found</div>
                        <div class="text-3xl font-bold text-purple-600">{{ overallStats.total_phones_returned }}</div>
                        <div class="text-xs mt-1 text-purple-600">Total phones</div>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
                        <div class="text-sm font-medium mb-1">Total Cost</div>
                        <div class="text-3xl font-bold text-green-600">${{ overallStats.total_cost }}</div>
                        <div class="text-xs mt-1 text-green-600">$0.10 per successful skip trace</div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div
                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Breakdown</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Skip Traces
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Successful
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Failed
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Success Rate
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Emails
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Phones
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Cost
                                </th>
                                <!-- <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th> -->
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ getInitials(user.name) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ user.name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ user.email }}
                                            </div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ user.user_type }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ user.total_skip_traces }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                    {{ user.successful_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                    {{ user.failed_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span
                                        :class="user.success_rate >= 70 ? 'text-green-600' : user.success_rate >= 50 ? 'text-yellow-600' : 'text-red-600'">
                                        {{ user.success_rate }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                    {{ user.total_emails_returned }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600">
                                    {{ user.total_phones_returned }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                                    ${{ user.cost }}
                                </td>
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="viewUserDetail(user.id)"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        View Details
                                    </button>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { getInitials } from '@/composables/useInitials';

// Props from Inertia
const props = defineProps<{
    users: any[];
    overallStats: any;
    lastDays: number;
}>();

const users = ref(props.users || []);
const overallStats = ref(props.overallStats || {});
const lastDays = ref(props.lastDays || 30);

const rangeOptions = [
    { label: '1d', value: 1 },
    { label: '1m', value: 30 },
    { label: '3m', value: 90 },
    { label: '6m', value: 180 },
    { label: '1yr', value: 365 }
];

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Skip Trace Analytics',
        href: '/admin/skip-trace-stats',
    },
];

function changePeriod(days: number) {
    lastDays.value = days;
    router.get(route('admin.skip-trace-stats'), { lastDays: days });
}

// function viewUserDetail(userId: number) {
//     router.visit(route('admin.skip-trace-stats.user', { userId }));
// }
</script>