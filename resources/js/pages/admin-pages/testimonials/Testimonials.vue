<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, Testimonial } from '@/types';
import TestimonialDialog from '@/pages/admin-pages/testimonials/TestimonialDialog.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Testimonials',
        href: 'admin.testimonials',
    }
];

interface Paginator {
    data: Testimonial[];
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
const pagination = computed<Paginator>(() => (page.props.testimonials as Paginator) ?? defaultPaginator);
const testimonials = computed(() => pagination.value.data ?? []);

const search = ref('');
const rating = ref('');
const sortBy = ref('published_date');
const showDialog = ref(false);
const selectedTestimonialId = ref<number | null>(null);

function fetchTestimonials(pageNum = 1) {
    router.get(route('admin.testimonials'), {
        search: search.value,
        rating: rating.value,
        sort_by: sortBy.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function onRowClick(testimonial: Testimonial) {
    selectedTestimonialId.value = testimonial.id;
    showDialog.value = true;
}

function openNewDialog() {
    selectedTestimonialId.value = null;
    showDialog.value = true;
}

function getRatingStars(rating: number): string {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
}

function formatDate(dateString: string | null): string {
    if (!dateString) return 'Not published';
    return new Date(dateString).toLocaleDateString();
}
</script>

<template>
    <Head title="Testimonials" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="rating" @change="fetchTestimonials()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4+ Stars</option>
                        <option value="3">3+ Stars</option>
                    </select>
                    <select v-model="sortBy" @change="fetchTestimonials()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="published_date">Sort by Published Date</option>
                        <option value="published_date_desc">Sort by Published Date (Desc)</option>
                        <option value="rate">Sort by Rating</option>
                        <option value="name">Sort by Name</option>
                        <option value="created_at">Sort by Created</option>
                        <option value="created_at_desc">Sort by Created (Desc)</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchTestimonials()" type="text"
                        placeholder="Search by name, email, or description" class="rounded-r-none px-3 py-2" />
                    <Button @click="fetchTestimonials()" variant="default"
                        class="rounded-l-none rounded-r-lg">Search</Button>
                    <Button @click="openNewDialog()" variant="default" class="whitespace-nowrap">
                        Add Testimonial
                    </Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Description</th>
                            <th class="px-4 py-2 text-left">Rating</th>
                            <th class="px-4 py-2 text-left">Published Date</th>
                            <th class="px-4 py-2 text-left">Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="testimonial in testimonials" :key="testimonial.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer" @click="onRowClick(testimonial)">
                            <td class="px-4 py-2 font-medium">{{ testimonial.name || 'Anonymous' }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ testimonial.email || '-' }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                {{ testimonial.description }}
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-yellow-500 text-sm">{{ getRatingStars(testimonial.rate) }}</span>
                                <span class="ml-1 text-xs text-gray-500">({{ testimonial.rate }})</span>
                            </td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">
                                {{ formatDate(testimonial.published_date) }}
                            </td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">
                                {{ testimonial.title || '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-2">
                <button v-if="pagination.prev_page_url" @click="fetchTestimonials(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchTestimonials(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
            <TestimonialDialog v-if="showDialog" v-model="showDialog" :testimonialId="selectedTestimonialId" />
        </div>
    </AppLayout>
</template> 