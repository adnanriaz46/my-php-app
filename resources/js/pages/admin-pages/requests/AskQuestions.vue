<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, AskQuestion } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { useDateFormat } from '@/composables/useFormat';
import EmailReplyDialog from '@/components/admin/EmailReplyDialog.vue';

const { formatDate } = useDateFormat();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Requests',
        href: route('admin.requests.ask-questions'),
    },
    {
        title: 'Ask Questions',
        href: route('admin.requests.ask-questions'),
    }
];

interface Paginator {
    data: AskQuestion[];
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
const pagination = computed<Paginator>(() => (page.props.askQuestions as Paginator) ?? defaultPaginator);
const askQuestions = computed(() => pagination.value.data ?? []);

const search = ref('');
const stateFilter = ref('');
const sortBy = ref('created_at');
const emailDialogOpen = ref(false);
const selectedQuestion = ref<AskQuestion | null>(null);

function fetchAskQuestions(pageNum = 1) {
    router.get(route('admin.requests.ask-questions'), {
        search: search.value,
        state: stateFilter.value,
        sort_by: sortBy.value,
        page: pageNum,
    }, { preserveState: true, replace: true });
}

function replyByEmail(question: AskQuestion) {
    selectedQuestion.value = question;
    emailDialogOpen.value = true;
}

function getDefaultSubject(question: AskQuestion): string {
    return `Re: Question about Property - ${question.full_street_address || question.property_id}`;
}

function getDefaultContent(question: AskQuestion): string {
    return `<p>Dear ${question.name || question.user?.name || 'there'},</p>

Thank you for your question about the property.
<br>
<p>${question.question ? `Your question: "${question.question}"` : ''}</p>

<p>We have received your inquiry and will get back to you shortly with a detailed response.</p>

<p>${question.preferred_contact_method ? `Preferred contact method: ${question.preferred_contact_method}` : ''}</p>
<br>
<p>Best regards,</p>
<p>Revamp365 Team</p>`;
}

function getContactMethodColor(method: string | null): string {
    switch (method?.toLowerCase()) {
        case 'email':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        case 'phone':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        case 'text':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
}
</script>

<template>
    <Head title="Ask Questions" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-2 mb-2 items-center text-sm">
                <div class="flex gap-2">
                    <select v-model="stateFilter" @change="fetchAskQuestions()"
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
                    <select v-model="sortBy" @change="fetchAskQuestions()"
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-pointer">
                        <option value="created_at">Sort by Created</option>
                        <option value="name">Sort by Name</option>
                        <option value="email">Sort by Email</option>
                        <option value="full_street_address">Sort by Address</option>
                    </select>
                </div>
                <div class="md:ml-auto flex w-full sm:w-auto">
                    <Input v-model="search" @keyup.enter="fetchAskQuestions()" type="text"
                        placeholder="Search by name, email, address, or question" class="rounded-r-none px-3 py-2" />
                    <Button @click="fetchAskQuestions()" variant="default"
                        class="rounded-l-none rounded-r-lg">Search</Button>
                </div>
            </div>
            <div class="overflow-auto rounded shadow bg-white dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 bg-input">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Contact Info</th>
                            <th class="px-4 py-2 text-left">Property Address</th>
                            <th class="px-4 py-2 text-left">Question</th>
                            <th class="px-4 py-2 text-left">Contact Method</th>
                            <th class="px-4 py-2 text-left">Created</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="question in askQuestions" :key="question.id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ question.name || question.user?.name || 'N/A' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ question.email || question.user?.email || 'N/A' }}</span>
                                    <span v-if="question.phone" class="text-xs text-gray-500 dark:text-gray-400">{{ question.phone }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col">
                                    <span v-if="question.full_street_address" class="font-medium">{{ question.full_street_address }}</span>
                                    <span v-else class="font-medium">Property ID: {{ question.property_id }}</span>
                                    <span v-if="question.city && question.state" class="text-xs text-gray-500 dark:text-gray-400">{{ question.city }}, {{ question.state }} {{ question.zip || '' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 max-w-xs">
                                <span v-if="question.question" class="truncate block" :title="question.question">
                                    {{ question.question }}
                                </span>
                                <span v-else class="text-gray-400">No question provided</span>
                            </td>
                            <td class="px-4 py-2">
                                <span v-if="question.preferred_contact_method" class="px-2 py-1 rounded-full text-xs font-medium" :class="getContactMethodColor(question.preferred_contact_method)">
                                    {{ question.preferred_contact_method }}
                                </span>
                                <span v-else class="text-gray-400 text-xs">Not specified</span>
                            </td>
                            <td class="px-4 py-2 text-xs">{{ formatDate(question.created_at) }}</td>
                            <td class="px-4 py-2">
                                <Button @click="replyByEmail(question)" variant="outline" size="sm" class="text-xs">
                                    Reply by Email
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-2">
                <button v-if="pagination.prev_page_url" @click="fetchAskQuestions(pagination.current_page - 1)"
                    class="px-3 py-1 rounded border mr-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Prev</button>
                <span class="px-2 py-1">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                <button v-if="pagination.next_page_url" @click="fetchAskQuestions(pagination.current_page + 1)"
                    class="px-3 py-1 rounded border ml-2 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 cursor-pointer">Next</button>
            </div>
        </div>

        <!-- Email Reply Dialog -->
        <EmailReplyDialog
            v-if="selectedQuestion"
            v-model:open="emailDialogOpen"
            :recipient-email="selectedQuestion.email || selectedQuestion.user?.email || ''"
            :recipient-name="selectedQuestion.name || selectedQuestion.user?.name || ''"
            :default-subject="getDefaultSubject(selectedQuestion)"
            :default-content="getDefaultContent(selectedQuestion)"
            request-type="question"
            @email-sent="() => selectedQuestion = null"
        />
    </AppLayout>
</template> 