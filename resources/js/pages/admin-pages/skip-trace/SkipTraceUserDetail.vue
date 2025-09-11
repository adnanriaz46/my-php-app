<template>
    <Head :title="`${user.name} - Skip Trace Details`" />
    <AppLayout :breadcrumbs="breadcrumbs">
      <PageHeading :title="`${user.name} - Skip Trace Details`" />
  
      <div class="p-6">
        <!-- User Info -->
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 h-16 w-16">
              <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                <span class="text-lg font-medium text-gray-700 dark:text-gray-300">
                  {{ getInitials(user.name) }}
                </span>
              </div>
            </div>
            <div class="ml-4">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h2>
              <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-500">{{ user.user_type }}</p>
            </div>
          </div>
        </div>
  
        <!-- Buyer Lists -->
        <div v-for="list in buyerLists" :key="list.id" class="mb-6">
          <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ list.name }}</h3>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                  {{ list.buyer_count }} buyers • {{ list.skip_traced_count }} skip traced • 
                  {{ list.successful_count }} successful • {{ list.failed_count }} failed
                </div>
              </div>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Investor
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Property Address
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Contact Info
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="buyer in list.buyers" :key="buyer.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                      {{ buyer.investor_identifier }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                      {{ buyer.mrp_fullstreet }}, {{ buyer.mrp_city }}, {{ buyer.mrp_state }} {{ buyer.mrp_zip }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span v-if="buyer.skip_traced" 
                            :class="(buyer.emails?.length || buyer.phone_numbers?.length) ? 
                              'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 
                              'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200'"
                            class="text-xs px-2 py-1 rounded">
                        Skip Traced
                      </span>
                      <span v-else class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs px-2 py-1 rounded">
                        Not Traced
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                      <div v-if="buyer.skip_traced">
                        <div v-if="buyer.first_name || buyer.last_name">
                          <strong>{{ buyer.first_name }} {{ buyer.last_name }}</strong>
                        </div>
                        <div v-if="buyer.emails?.length" class="text-blue-600">
                          {{ buyer.emails.length }} email(s)
                        </div>
                        <div v-if="buyer.phone_numbers?.length" class="text-purple-600">
                          {{ buyer.phone_numbers.length }} phone(s)
                        </div>
                      </div>
                      <div v-else class="text-gray-400">
                        No contact info
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup lang="ts">
  import { ref } from 'vue';
  import { Head } from '@inertiajs/vue3';
  import AppLayout from '@/layouts/AppLayout.vue';
  import { type BreadcrumbItem } from '@/types';
  import { getInitials } from '@/composables/useInitials';
  
  // Props from Inertia
  const props = defineProps<{
    user: any;
    buyerLists: any[];
  }>();
  
  const user = ref(props.user || {});
  const buyerLists = ref(props.buyerLists || []);
  
  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Skip Trace Analytics',
      href: '/admin/skip-trace-stats',
    },
    {
      title: user.value.name,
      href: `/admin/skip-trace-stats/${user.value.id}`,
    },
  ];
  </script>