<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, User } from '@/types';
import UserDetailDialog from '@/pages/admin-pages/users/UserDetailDialog.vue';
import { UserTypes } from '@/types/userTypes';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { getInitials } from '@/composables/useInitials';

const props = defineProps<{
    propertyTypes: string[];
    invStgy: string[];
    counties: string[];
    emailCategories: Record<string, any>;
}>();

interface UserData extends User {
    property_view_histories_count?: number;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: 'admin/users',
    }
];

interface Paginator {
    data: UserData[];
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
const pagination = computed<Paginator>(() => (page.props.users as Paginator) ?? defaultPaginator);
const users = computed(() => pagination.value.data ?? []);

const search = ref('');
const userType = ref('');
const sortBy = ref('created_at');
const showDetail = ref(false);
const selectedUserId = ref<number | null>(null);

function fetchUsers(pageNum = 1) {
    router.get(route('admin.users'), {
        search: search.value,
        user_type: userType.value,
        sort_by: sortBy.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function onRowClick(user: UserData) {
    selectedUserId.value = user.id;
    showDetail.value = true;
}

</script>

<template>

    <Head title="All Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="userType" @change="fetchUsers()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All Types</option>
                        <option :value="UserTypes.ADMIN">Admin</option>
                        <option :value="UserTypes.PREMIUM">Premium</option>
                        <option :value="UserTypes.FREE">Free</option>
                    </select>
                    <select v-model="sortBy" @change="fetchUsers()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="name">Sort by Name</option>
                        <option value="created_at">Sort by Created</option>
                        <option value="property_view_histories_count">Sort by Views</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchUsers()" type="text"
                        placeholder="Search by name, email, or phone" class=" ml-auto rounded-r-none px-3 py-2" />
                    <Button @click="fetchUsers()" variant="default"
                        class="ml-auto rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Avatar</th>
                            <th class="px-4 py-2 text-left">Full Name</th>
                            <th class="px-4 py-2 text-left">User Type</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Phone</th>
                            <th class="px-4 py-2 text-left">No. Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer" @click="onRowClick(user)">
                            <td class="px-4 py-2">
                                <img v-if="user.profile_picture" :src="user.profile_picture"
                                    class="w-8 h-8 rounded-full object-cover" />
                                <div v-else
                                    class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-300">{{getInitials(user.name)}}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">{{ user.name }}</td>
                            <td class="px-4 py-2">{{ user.user_type == UserTypes.ADMIN ? 'Admin' : user.user_type ==
                                UserTypes.PREMIUM ? 'Premium' : 'Free' }}</td>
                            <td class="px-4 py-2">{{ user.email }}</td>
                            <td class="px-4 py-2">{{ user.phone_number }}</td>
                            <td class="px-4 py-2">{{ user.property_view_histories_count ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-2">
                <button v-if="pagination.prev_page_url" @click="fetchUsers(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchUsers(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
            <UserDetailDialog v-if="showDetail && selectedUserId" :propertyTypes="props.propertyTypes"
                :emailCategories="props.emailCategories" :invStgy="props.invStgy" :counties="props.counties"
                v-model="showDetail" :userId="selectedUserId" />
        </div>
    </AppLayout>
</template>