<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { BreadcrumbItem } from '@/types';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import TextArea from '@/components/ui/textarea/TextArea.vue';
import Combobox from '@/components/ui/combobox/Combobox.vue';
import { useToast } from '@/composables/useToast';
import axios from 'axios';

const { showToast } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Support',
        href: route('support.tickets.index'),
    },
    {
        title: 'Create Ticket',
        href: route('support.create'),
    }
];

// Form data
const form = ref({
    title: '',
    description: '',
    categories: [] as string[],
});

const attachments = ref<File[]>([]);
const isSubmitting = ref(false);

// Available categories
const availableCategories = [
    'Account',
    'Property',
    'Wholesale (My Property)',
    'Email Marketing',
    'Payment',
    'Subscription',
    'Other'
];

function onFileSelect(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        // Limit to 3 files
        if (attachments.value.length + files.length > 3) {
            showToast('Error', 'Maximum 3 attachments allowed', 'error');
            return;
        }
        attachments.value = [...attachments.value, ...files];
    }
}

function removeAttachment(index: number) {
    attachments.value.splice(index, 1);
}

async function submitTicket() {
    if (!form.value.title.trim()) {
        showToast('Error', 'Please enter a title', 'error');
        return;
    }

    if (!form.value.description.trim()) {
        showToast('Error', 'Please enter a description', 'error');
        return;
    }

    if (form.value.categories.length === 0) {
        showToast('Error', 'Please select at least one category', 'error');
        return;
    }

    isSubmitting.value = true;

    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('description', form.value.description);
        form.value.categories.forEach((category, index) => {
            formData.append(`categories[${index}]`, category);
        });

        attachments.value.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });

        const response = await axios.post(route('support.tickets.store'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        if (response.data.success) {
            showToast('Success', 'Support ticket created successfully', 'success');
            router.visit(route('support.tickets.index'));
            // Reset form
            form.value = {
                title: '',
                description: '',
                categories: [],
            };
            attachments.value = [];
        }
    } catch (error: any) {
        showToast('Error', error.response?.data?.message || 'Failed to create ticket', 'error');
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <Head title="Create Support Ticket" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-65px)] overflow-auto">
            <div class="max-w-2xl mx-auto w-full">
                <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow">
                    <h1 class="text-2xl font-bold mb-6">Create Support Ticket</h1>

                    <form @submit.prevent="submitTicket" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium mb-2">Title/Subject</label>
                            <Input v-model="form.title" placeholder="Brief description of your issue" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Categories</label>
                            <Combobox v-model="form.categories" :option-values="availableCategories"
                                placeholder="Select categories" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select one or more categories that
                                best describe your issue</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Description</label>
                            <TextArea v-model="form.description"
                                placeholder="Please provide detailed information about your issue..." rows="6"
                                required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Attachments (optional)</label>
                            <input type="file" @change="onFileSelect" multiple accept="image/*"
                                class="block cursor-pointer file:cursor-pointer w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload screenshots or images (max 3
                                files, 15MB each)</p>

                            <!-- Selected Files -->
                            <div v-if="attachments.length > 0" class="mt-4">
                                <h4 class="text-sm font-medium mb-2">Selected Files:</h4>
                                <div class="space-y-2">
                                    <div v-for="(file, index) in attachments" :key="index"
                                        class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded">
                                        <div class="flex items-center gap-3">
                                            <div v-if="file.type.startsWith('image/')"
                                                class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                <span class="text-xs text-gray-500">Image</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium">{{ file.name }}</p>
                                                <p class="text-xs text-gray-500">{{ (file.size / 1024 / 1024).toFixed(2)
                                                    }} MB</p>
                                            </div>
                                        </div>
                                        <button @click="removeAttachment(index)" type="button"
                                            class="text-red-500 hover:text-red-700 p-1">
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <Button type="submit" :disabled="isSubmitting" variant="default" class="flex-1">
                                {{ isSubmitting ? 'Creating Ticket...' : 'Create Support Ticket' }}
                            </Button>
                            <Button type="button" variant="outline" @click="() => router.visit(route('support.tickets.index'))">
                                Cancel
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>