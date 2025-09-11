<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BreadcrumbItem, SupportTicket } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import TextArea from '@/components/ui/textarea/TextArea.vue';
import { useDateFormat } from '@/composables/useFormat';
import { useToast } from '@/composables/useToast';
import axios from 'axios';
import FullScreenImageSlider from '@/components/ui/image-slider/FullScreenImageSlider.vue';

const { formatDate } = useDateFormat();
const { showToast } = useToast();

const page = usePage();
const supportTicket = computed(() => page.props.supportTicket as SupportTicket);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My Support Tickets',
        href: route('support.tickets.index'),
    },
    {
        title: supportTicket.value?.ticket_number || 'Ticket Detail',
        href: route('support.tickets.show', supportTicket.value?.id),
    }
];

// Reply form
const replyMessage = ref('');
const replyAttachments = ref<File[]>([]);
const isSubmitting = ref(false);

function getStatusColor(status: string): string {
    switch (status) {
        case 'open':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        case 'in_progress':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        case 'waiting_for_user':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
        case 'waiting_for_admin':
            return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
        case 'closed':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
}

function getStatusLabel(status: string): string {
    switch (status) {
        case 'open':
            return 'Open';
        case 'in_progress':
            return 'In Progress';
        case 'waiting_for_user':
            return 'Waiting for User';
        case 'waiting_for_admin':
            return 'Waiting for Admin';
        case 'closed':
            return 'Closed';
        default:
            return status;
    }
}

function onFileSelect(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        replyAttachments.value = [...replyAttachments.value, ...files];
    }
}

function removeAttachment(index: number) {
    replyAttachments.value.splice(index, 1);
}

async function submitReply() {
    if (!replyMessage.value.trim()) {
        showToast('Error', 'Please enter a message', 'error');
        return;
    }

    isSubmitting.value = true;

    try {
        const formData = new FormData();
        formData.append('message', replyMessage.value);

        replyAttachments.value.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });

        const response = await axios.post(route('support.tickets.reply', supportTicket.value.id), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        if (response.data.success) {
            showToast('Success', 'Reply sent successfully', 'success');
            replyMessage.value = '';
            replyAttachments.value = [];

            // Refresh the page to show the new reply
            router.reload();
        }
    } catch (error: any) {
        showToast('Error', error.response?.data?.message || 'Failed to send reply', 'error');
    } finally {
        isSubmitting.value = false;
    }
}

async function closeTicket() {
    if (!confirm('Are you sure you want to close this ticket?')) {
        return;
    }

    try {
        const response = await axios.patch(route('support.tickets.close', supportTicket.value.id));

        if (response.data.success) {
            showToast('Success', 'Ticket closed successfully', 'success');
            router.reload();
        }
    } catch (error: any) {
        showToast('Error', error.response?.data?.message || 'Failed to close ticket', 'error');
    }
}

const isImageSliderOpen = ref(false);
const selectedImage = ref<string[]>([]);

function openImageSlider(image: string) {
    selectedImage.value = [image];
    isImageSliderOpen.value = true;
}

</script>

<template>

    <Head :title="`Support Ticket - ${supportTicket?.ticket_number}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-auto">
            <!-- Ticket Header -->
            <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold">{{ supportTicket?.title }}</h1>
                        <p class="text-gray-600 dark:text-gray-400">Ticket #{{ supportTicket?.ticket_number }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium my-auto h-fit"
                            :class="getStatusColor(supportTicket?.status)">
                            {{ getStatusLabel(supportTicket?.status) }}
                        </span>
                        <Button v-if="supportTicket?.status !== 'closed'" @click="closeTicket" variant="destructive">
                            Close Ticket
                        </Button>
                    </div>
                </div>

                <!-- Ticket Info -->
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h3 class="font-semibold mb-2">Ticket Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><strong>Created:</strong> {{ formatDate(supportTicket?.created_at) }}</p>
                            <p v-if="supportTicket?.closed_at"><strong>Closed:</strong> {{
                                formatDate(supportTicket?.closed_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="mt-4">
                    <h3 class="font-semibold mb-2">Categories</h3>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="category in supportTicket?.categories" :key="category"
                            class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-sm">
                            {{ category }}
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <h3 class="font-semibold mb-2">Description</h3>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ supportTicket?.description }}</p>
                </div>

                <!-- Original Attachments -->
                <div v-if="supportTicket?.attachments && supportTicket.attachments.length > 0" class="mt-4">
                    <h3 class="font-semibold mb-2">Attachments</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div v-for="attachment in supportTicket.attachments" :key="attachment" class="relative">
                            <img :src="attachment" :alt="attachment" class="w-full h-32 object-cover rounded-lg cursor-pointer" @click="openImageSlider(attachment)" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Replies -->
            <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow flex-1">
                <h3 class="font-semibold mb-4">Conversation</h3>
                <div class="space-y-4">
                    <div v-for="reply in supportTicket?.replies" :key="reply.id" class="p-4 border rounded-lg"
                        :class="reply.is_admin_reply ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700'">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-semibold">{{ reply.user?.name }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                    {{ reply.is_admin_reply ? '(Admin)' : '(You)' }}
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(reply.created_at)
                                }}</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ reply.message }}</p>

                        <!-- Reply Attachments -->
                        <div v-if="reply.attachments && reply.attachments.length > 0" class="mt-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div v-for="attachment in reply.attachments" :key="attachment" class="relative">
                                    <img :src="attachment" :alt="attachment" 
                                        class="w-full h-32 object-cover rounded-lg cursor-pointer" @click="openImageSlider(attachment)" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div v-if="supportTicket?.status !== 'closed'" class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow">
                <h3 class="font-semibold mb-4">Add Reply</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Message</label>
                        <TextArea v-model="replyMessage" placeholder="Enter your reply..." rows="4" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Attachments (optional)</label>
                        <input type="file" @change="onFileSelect" multiple accept="image/*"
                            class="cursor-pointer file:cursor-pointer block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />

                        <!-- Selected Files -->
                        <div v-if="replyAttachments.length > 0" class="mt-2">
                            <div class="flex flex-wrap gap-2">
                                <div v-for="(file, index) in replyAttachments" :key="index"
                                    class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded">
                                    <span class="text-sm">{{ file.name }}</span>
                                    <button  @click="removeAttachment(index)" class="text-red-500 hover:text-red-700 cursor-pointer">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Button @click="submitReply" :disabled="isSubmitting" variant="default">
                        {{ isSubmitting ? 'Sending...' : 'Send Reply' }}
                    </Button>
                </div>
            </div>
        </div>
        <FullScreenImageSlider v-if="isImageSliderOpen" :images="selectedImage" v-model:open="isImageSliderOpen" />
    </AppLayout>
</template>