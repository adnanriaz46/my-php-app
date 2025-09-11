<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="MLS Offer Email Templates" />
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <PageHeading title="MLS Offer Email Templates" />
        <Button @click="showDialog = true" class="flex items-center gap-2">
          <Icon icon="tabler:plus" /> Add Email Template
        </Button>
      </div>
      <div class="rounded shadow p-4 bg-white dark:bg-gray-900 dark:shadow-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Name</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Subject</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Body</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="template in templates" :key="template.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ template.name }}</td>
              <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ template.subject }}</td>
              <td class="px-4 py-2 text-gray-900 dark:text-gray-100 whitespace-pre-line">
                <span v-if="template.body.length > 100">{{ template.body.substring(0, 100) }} ...</span>
                <span v-else>{{ template.body }}</span>
              </td>
              <td class="px-4 py-2 flex gap-2">
                <Button size="icon" variant="ghost" title="Edit" @click="editTemplate(template)"><Icon icon="tabler:edit" /></Button>
                <Button size="icon" variant="ghost" title="Delete" @click="deleteTemplate(template.id)"><Icon icon="tabler:trash" class="text-red-500 dark:text-red-400" /></Button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!templates.length" class="text-center text-gray-400 dark:text-gray-500 py-8">No templates found.</div>
      </div>
      <EmailTemplateDialog
        :open="showDialog"
        :tags="props.tags"
        :template="selectedTemplate"
        @update:open="onDialogClose"
        @saved="reloadPage"
      />
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Icon } from '@iconify/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeading from '@/components/ui/page-heading/PageHeading.vue';
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import EmailTemplateDialog from '@/components/ui/mls-offers/EmailTemplateDialog.vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'MLS Offer Email Templates', href: route('mls-offer.email-templates') },
];

const props = defineProps<{ templates: any[], tags: string[] }>();
const showDialog = ref(false);
const selectedTemplate = ref(null);

function reloadPage() {
  router.reload({ only: ['templates'] });
}

function editTemplate(template: any) {
  selectedTemplate.value = template;
  showDialog.value = true;
}

function deleteTemplate(id: number) {
  if (confirm('Are you sure you want to delete this template?')) {
    router.delete(route('mls-offer.email-templates.destroy', id), {
      onSuccess: reloadPage,
    });
  }
}

function onDialogClose(val: boolean) {
  showDialog.value = val;
  if (!val) selectedTemplate.value = null;
}
</script> 