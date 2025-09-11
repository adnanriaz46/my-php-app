<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, CountyState } from '@/types';
import CountyStateDialog from '@/pages/admin-pages/county-states/CountyStateDialog.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'County States',
        href: 'admin.county-states',
    }
];

interface Paginator {
    data: CountyState[];
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
const pagination = computed<Paginator>(() => (page.props.countyStates as Paginator) ?? defaultPaginator);
const countyStates = computed(() => pagination.value.data ?? []);

const search = ref('');
const stateFilter = ref('');
const sortBy = ref('display');
const showDialog = ref(false);
const selectedCountyStateId = ref<number | null>(null);

function fetchCountyStates(pageNum = 1) {
    router.get(route('admin.county-states'), {
        search: search.value,
        state: stateFilter.value,
        sort_by: sortBy.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function onRowClick(countyState: CountyState) {
    selectedCountyStateId.value = countyState.id;
    showDialog.value = true;
}

function openNewDialog() {
    selectedCountyStateId.value = null;
    showDialog.value = true;
}
</script>

<template>
    <Head title="County States" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="stateFilter" @change="fetchCountyStates()"
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
                    <select v-model="sortBy" @change="fetchCountyStates()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="display">Sort by Display</option>
                        <option value="county">Sort by County</option>
                        <option value="state">Sort by State</option>
                        <option value="created_at">Sort by Created</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchCountyStates()" type="text"
                        placeholder="Search by county, display, or state" class="rounded-r-none px-3 py-2" />
                    <Button @click="fetchCountyStates()" variant="default"
                        class="rounded-l-none rounded-r-lg">Search</Button>
                    <Button @click="openNewDialog()" variant="default" class="whitespace-nowrap">
                        Add County State
                    </Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">County</th>
                            <th class="px-4 py-2 text-left">Display</th>
                            <th class="px-4 py-2 text-left">State</th>
                            <th class="px-4 py-2 text-left">FIPS</th>
                            <th class="px-4 py-2 text-left">Slug</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="countyState in countyStates" :key="countyState.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer" @click="onRowClick(countyState)">
                            <td class="px-4 py-2 font-medium">{{ countyState.county }}</td>
                            <td class="px-4 py-2">{{ countyState.display }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ countyState.state }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ countyState.fips || '-' }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ countyState.slug }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-2">
                <button v-if="pagination.prev_page_url" @click="fetchCountyStates(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchCountyStates(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
            <CountyStateDialog v-if="showDialog" v-model="showDialog" :countyStateId="selectedCountyStateId" />
        </div>
    </AppLayout>
</template> 