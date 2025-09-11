<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, UpgradeFeature } from '@/types';
import UpgradeFeatureDialog from '@/pages/admin-pages/upgrade-features/UpgradeFeatureDialog.vue';
import { UserTypes } from '@/types/userTypes';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Upgrade Features',
        href: 'admin.upgrade-features',
    }
];

interface Paginator {
    data: UpgradeFeature[];
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
const pagination = computed<Paginator>(() => (page.props.upgradeFeatures as Paginator) ?? defaultPaginator);
const upgradeFeatures = computed(() => pagination.value.data ?? []);

const search = ref('');
const userType = ref('');
const sortBy = ref('order');
const showDialog = ref(false);
const selectedFeatureId = ref<number | null>(null);

function fetchUpgradeFeatures(pageNum = 1) {
    router.get(route('admin.upgrade-features'), {
        search: search.value,
        user_type: userType.value,
        sort_by: sortBy.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function onRowClick(feature: UpgradeFeature) {
    selectedFeatureId.value = feature.id;
    showDialog.value = true;
}

function openNewDialog() {
    selectedFeatureId.value = null;
    showDialog.value = true;
}

function getUserTypeLabel(group: number): string {
    switch (group) {
        case UserTypes.ADMIN:
            return 'Admin';
        case UserTypes.PREMIUM:
            return 'Premium';
        case UserTypes.FREE:
            return 'Free';
        default:
            return 'Unknown';
    }
}
</script>

<template>
    <Head title="Upgrade Features" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="userType" @change="fetchUpgradeFeatures()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All Types</option>
                        <option :value="UserTypes.ADMIN">Admin</option>
                        <option :value="UserTypes.PREMIUM">Premium</option>
                        <option :value="UserTypes.FREE">Free</option>
                    </select>
                    <select v-model="sortBy" @change="fetchUpgradeFeatures()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="order">Sort by Order</option>
                        <option value="feature">Sort by Feature</option>
                        <option value="created_at">Sort by Created</option>
                        <option value="created_at_desc">Sort by Created (Desc)</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchUpgradeFeatures()" type="text"
                        placeholder="Search by feature or description" class="rounded-r-none px-3 py-2" />
                    <Button @click="fetchUpgradeFeatures()" variant="default"
                        class="rounded-l-none rounded-r-lg">Search</Button>
                    <Button @click="openNewDialog()" variant="default" class="whitespace-nowrap">
                        Add Feature
                    </Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Feature</th>
                            <th class="px-4 py-2 text-left">Description</th>
                            <th class="px-4 py-2 text-left">User Type</th>
                            <th class="px-4 py-2 text-left">Order</th>
                            <th class="px-4 py-2 text-left">No Access</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="feature in upgradeFeatures" :key="feature.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer" @click="onRowClick(feature)">
                            <td class="px-4 py-2 font-medium">{{ feature.feature }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ feature.description }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': feature.group === UserTypes.ADMIN,
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': feature.group === UserTypes.PREMIUM,
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': feature.group === UserTypes.FREE
                                    }">
                                    {{ getUserTypeLabel(feature.group) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ feature.order }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="feature.no_access ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'">
                                    {{ feature.no_access ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-2">
                <button v-if="pagination.prev_page_url" @click="fetchUpgradeFeatures(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchUpgradeFeatures(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
            <UpgradeFeatureDialog v-if="showDialog" v-model="showDialog" :featureId="selectedFeatureId" />
        </div>
    </AppLayout>
</template> 