<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Icon } from '@iconify/vue';
import UploadPdfTemplateDialog from '@/components/ui/mls-offers/UploadPdfTemplateDialog.vue';
import UpdateTemplateNameDialog from '@/components/ui/mls-offers/UpdateTemplateNameDialog.vue';
import AppLayout from "@/layouts/AppLayout.vue";
import PageHeading from '@/components/ui/page-heading/PageHeading.vue';
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import ConfirmDialog from '@/components/ui/confirm-dialog/ConfirmDialog.vue';
import { useToast } from '@/composables/useToast';
import Toast from '@/components/ui/toast/Toast.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'MLS Application/PDF Templates', href: route('mls-offer.pdf-templates') },
];

const props = defineProps<{ templates: any[] }>();
const showUploadDialog = ref(false);
const showUpdateNameDialog = ref(false);
const selectedTemplate = ref<any>(null);

function formatDate(date: string) {
    return new Date(date).toLocaleDateString();
}
const { showToast } = useToast()

function reloadPage(message: string) {
    showToast('MLS Templates', message, 'success');
    router.reload({ only: ['templates'] });
}
const confirmDialog = useConfirmDialog()
const onHandlingConfirmation = async (message: string, type: string, id: number) => {
    const confirmed = await confirmDialog.openConfirm(message, 'Delete Template')
    if (confirmed) {
        router.delete(route('mls-offer.pdf-templates.destroy', id), {
            onSuccess: () => reloadPage('Template deleted successfully.'),
        });
    }
}

const openUpdateNameDialog = (template: any) => {
    selectedTemplate.value = template;
    showUpdateNameDialog.value = true;
}

const downloadTemplate = (id: number) => {
    window.open(route('mls-offer.pdf-templates.download', id));
}
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <ConfirmDialog :dialog="confirmDialog" />
        <Toast />

        <Head title="MLS Application/PDF Templates" />
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <PageHeading title="MLS Application/PDF Templates" />
                <Button @click="showUploadDialog = true" class="flex items-center gap-2">
                    <Icon icon="tabler:upload" /> Upload PDF Template
                </Button>
            </div>

            <div class="rounded shadow p-4 bg-white dark:bg-gray-900 dark:shadow-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Template ID</th>
                            <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Uploaded At</th>
                            <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Template Name</th>
                            <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Fillable</th>
                            <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="template in templates" :key="template.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ template.template_id }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ formatDate(template.created_at) }}
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ template.template_name }}</td>
                            <td class="px-4 py-2">
                                <span v-if="template.fillable" class="text-green-600 dark:text-green-400"><Icon icon="tabler:circle-check-filled" class="size-5 text-green-600 dark:text-green-400"/></span>
                                <span v-else class="text-gray-400 dark:text-gray-500"><Icon icon="tabler:hourglass-empty" class="size-5 text-gray-400 dark:text-gray-500"/></span>
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <Button size="icon" variant="ghost" title="Edit" @click="openUpdateNameDialog(template)">
                                    <Icon icon="tabler:edit" class="size-5"/>
                                </Button>
                                <Button size="icon" variant="ghost" title="Download" @click="downloadTemplate(template.id)">
                                    <Icon icon="tabler:download" class="size-5"/>
                                </Button>
                                <Button size="icon" variant="ghost" title="List Fields">
                                    <Icon icon="tabler:list-details" class="size-5"/>
                                </Button>
                                <Button size="icon" variant="ghost" title="Delete"
                                    @click="onHandlingConfirmation('Do you want to delete this template?', 'delete', template.id)">
                                    <Icon icon="tabler:trash" class="text-red-500 dark:text-red-400 size-5"/>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="!props.templates.length" class="text-center text-gray-400 dark:text-gray-500 py-8">No
                    templates found.</div>
            </div>

            <UploadPdfTemplateDialog :open="showUploadDialog" @update:open="showUploadDialog = $event"
                @uploaded="reloadPage('Template uploaded successfully.')" />

            <UpdateTemplateNameDialog 
                :open="showUpdateNameDialog" 
                :template="selectedTemplate"
                @update:open="showUpdateNameDialog = $event"
                @updated="reloadPage('Template name updated successfully.')" />

        </div>
    </AppLayout>
</template>
