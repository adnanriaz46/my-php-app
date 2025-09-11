<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="MLS Offer Generated Logs" />
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <PageHeading title="MLS Offer Generated Logs" />
      </div>
      <div class="rounded shadow p-4 bg-white dark:bg-gray-900 dark:shadow-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">File Name</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Subject</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Sent To</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Offer Price</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Email Success</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Sent At</th>
              <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ log.file_name }}</td>
              <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ log.subject }}</td>
              <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ log.sent_to }}</td>
              <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ log.offer_price }}</td>
              <td class="px-4 py-2">
                <span v-if="log.email_success" class="text-green-600 dark:text-green-400">✔</span>
                <span v-else class="text-red-500 dark:text-red-400">✖</span>
              </td>
              <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ formatDate(log.send_at) }}</td>
              <td class="px-4 py-2 flex gap-2">
                <Button size="icon" variant="ghost" title="View"><Icon icon="tabler:eye" /></Button>
                <Button size="icon" variant="ghost" title="Download"><Icon icon="tabler:download" /></Button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!props.logs.length" class="text-center text-gray-400 dark:text-gray-500 py-8">No logs found.</div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Icon } from '@iconify/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeading from '@/components/ui/page-heading/PageHeading.vue';
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'MLS Offer Generated Logs', href: route('mls-offer.mail-logs') },
];

const props = defineProps<{ logs: any[] }>();

function formatDate(date: string) {
  if (!date) return '';
  return new Date(date).toLocaleString();
}
</script> 