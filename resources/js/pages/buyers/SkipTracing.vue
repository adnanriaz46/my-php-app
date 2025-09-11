<template>

  <Head title="Buyer Skip Tracing" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <PageHeading title="Buyer Skip Tracing" />

    <!-- Success Message Toast -->
    <div v-if="showSuccessMessage"
      class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
      <span>✅</span>
      <span>{{ successMessage }}</span>
      <button @click="showSuccessMessage = false" class="ml-2 text-white hover:text-green-100">×</button>
    </div>

    <div class="p-6 max-h-[calc(100dvh-110px)] overflow-y-auto">
      <!-- Skip Trace Stats Dashboard -->
      <div v-if="buyerLists.length > 0" class="mb-6">
        <div class="flex justify-between items-center mb-4">
          <div class="text-lg font-semibold">Skip Trace Stats</div>
          <div class="flex md:flex-row flex-col items-center gap-2">
            <span class="text-sm text-gray-500">{{ stats.from }} – {{ stats.to }}</span>
            <div class="flex gap-1 ml-2 text-sm">
              <button v-for="option in rangeOptions" :key="option.value"
                :class="['px-2 py-1 rounded border', lastDays === option.value ? 'bg-white dark:bg-gray-900 border-primary text-primary dark:text-primary font-bold' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200']"
                @click="changeDateRange(option.value)">
                {{ option.label }}
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Total Buyers</div>
            <div class="text-3xl font-bold">{{ totalSkipTraceStats.total_buyers }}</div>
            <div :class="['text-xs mt-1', changeClass(stats.total_skip_traces, stats.total_skip_traces_prev)]">
              {{ percentChange(stats.total_skip_traces, stats.total_skip_traces_prev) }} {{ periodLabel() }}
            </div>
          </div>
          <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Total Skip Traces</div>
            <div class="text-3xl font-bold">{{ totalSkipTraceStats.total_skip_traced }}</div>
            <div :class="['text-xs mt-1', changeClass(stats.total_skip_traces, stats.total_skip_traces_prev)]">
              {{ percentChange(stats.total_skip_traces, stats.total_skip_traces_prev) }} {{ periodLabel() }}
            </div>
          </div>
          <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Successful Returns</div>
            <div class="text-3xl font-bold text-green-600">{{ stats.successful_count }}</div>
            <div class="text-xs mt-1 text-green-600">
              {{ totalSkipTraceStats.total_skip_traced > 0 ?
                Math.round((stats.successful_count / totalSkipTraceStats.total_skip_traced) * 100) : 0 }}% success rate
            </div>
          </div>
          <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Failed Returns</div>
            <div class="text-3xl font-bold text-red-600">{{ stats.failed_count }}</div>
            <div class="text-xs mt-1 text-red-600">
              {{ totalSkipTraceStats.total_skip_traced > 0 ?
                Math.round((stats.failed_count / totalSkipTraceStats.total_skip_traced) * 100) : 0 }}% failure rate
            </div>
          </div>
          <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Emails Found</div>
            <div class="text-3xl font-bold text-blue-600">{{ stats.total_emails_returned }}</div>
            <div :class="['text-xs mt-1', changeClass(stats.total_emails_returned, stats.total_emails_returned_prev)]">
              {{ percentChange(stats.total_emails_returned, stats.total_emails_returned_prev) }} {{ periodLabel() }}
            </div>
          </div>
          <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Phones Found</div>
            <div class="text-3xl font-bold text-purple-600">{{ stats.total_phones_returned }}</div>
            <div :class="['text-xs mt-1', changeClass(stats.total_phones_returned, stats.total_phones_returned_prev)]">
              {{ percentChange(stats.total_phones_returned, stats.total_phones_returned_prev) }} {{ periodLabel() }}
            </div>
          </div>
          <div class="bg-yellow-100 dark:bg-yellow-900/40 rounded-lg p-4 flex flex-col min-h-[120px]">
            <div class="text-sm font-medium mb-1">Community Skip Traces</div>
            <div class="text-3xl font-bold text-orange-600">{{ communitySkipTraces }}</div>
            <div :class="['text-xs mt-1', changeClass(stats.community_skip_traces, stats.community_skip_traces_prev)]">
              {{ percentChange(stats.community_skip_traces, stats.community_skip_traces_prev) }} {{ periodLabel() }}
            </div>
          </div>
        </div>
      </div>
      <!-- Top Section: Two Columns Side by Side -->
      <!-- Top Section: Better Aligned Two Columns -->
      <div class="mb-6">
        <div class="bg-white dark:bg-neutral-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
          <div class="flex flex-col lg:flex-row lg:items-center gap-8">
            <!-- Left Column: Dropdown -->
            <div class="lg:w-1/2">
              <label class="block mb-3 font-semibold text-gray-700 dark:text-gray-300 text-sm uppercase tracking-wide">
                Select Buyer List
              </label>
              <SelectBox v-model="selectedListId" :options="buyerLists.map((list: any) => ({
                key: list.id.toString(),
                value: `${list.name} (${list.buyer_count} buyers)`
              }))" placeholder="Select a list to skip trace" class="w-full" @update:model-value="onListChange" />
            </div>

            <!-- Right Column: Details + Button -->
            <div v-if="selectedList" class="lg:w-1/2 lg:pl-8 lg:border-l lg:border-gray-200 dark:border-gray-600">
              <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ selectedList.name }}
                  </h3>
                  <div class="text-gray-600 dark:text-gray-300 text-sm">
                    {{ selectedList.buyer_count }} buyers •
                    {{ selectedList.skip_traced_count }} skip traced •
                    <span class="text-green-600">{{ selectedList.successful_count || 0 }} successful</span> •
                    <span class="text-red-600">{{ selectedList.failed_count || 0 }} failed</span> •
                    Created {{ selectedList.created_at }}
                  </div>
                </div>
                <button @click="openTracePrompt" :disabled="isSkipTracing || !selectedList" :class="[
                  'lg:flex-shrink-0 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200',
                  // COLORS:
                  // Start = green (when NO appended records yet)
                  // Re-Skip = orange (when there IS at least one appended record)
                  hasAnyAppended ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600',
                  isSkipTracing ? 'opacity-50 cursor-not-allowed' : ''
                ]">
                  <span v-if="isSkipTracing" class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Skip Tracing...
                  </span>
                  <span v-else>
                    {{ hasAnyAppended ? 'Re-Skip Trace' : 'Start Skip Tracing' }}
                  </span>
                </button>

              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Buyer List Table -->
      <div v-if="selectedList" class="space-y-4">
        <div class="flex flex-col md:flex-row gap-2 md:justify-between">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buyers in List</h3>
        </div>

        <!-- Table -->
        <div
          class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <!-- Desktop Table (hidden on mobile) -->
          <div class="hidden md:block">
            <div
              class="max-h-[calc(100vh-300px)] overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg">
              <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 dark:text-white sticky top-0 z-10">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">
                      Investor
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">
                      Owner Name
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">
                      Property Address
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">
                      Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="buyer in filteredBuyers" :key="buyer.id">
                    <!-- Main Row -->
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                      <td class="px-4 py-3">
                        <div class="font-semibold text-gray-900 dark:text-white">
                          {{ buyer.investor_identifier }}
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div v-if="buyer.skip_traced && (buyer.first_name || buyer.last_name)"
                          class="text-gray-600 dark:text-gray-300">
                          {{ buyer.first_name }} {{ buyer.last_name }}
                        </div>
                        <div v-else class="text-gray-400 dark:text-gray-500 text-sm">
                          Not available
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="text-gray-600 dark:text-gray-300">
                          <button
                            @click="openPropertyDetails(`${buyer.mrp_fullstreet}, ${buyer.mrp_city}, ${buyer.mrp_state} ${buyer.mrp_zip}`)"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline cursor-pointer text-left">
                            {{ buyer.mrp_fullstreet }}, {{ buyer.mrp_city }}, {{ buyer.mrp_state }} {{ buyer.mrp_zip }}
                          </button>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          Paid {{ formatPrice(buyer.mrp_sales_price) }}
                          <span v-if="buyer.mrp_purchase" class="text-gray-400"> | Closed {{
                            formatDate(buyer.mrp_purchase)
                          }}</span>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <span v-if="buyer.skip_traced" :class="(buyer.phone_numbers?.length || buyer.emails?.length) ?
                          'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' :
                          'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200'"
                          class="text-xs px-2 py-1 rounded">
                          Skip Traced
                        </span>
                        <span v-else
                          class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs px-2 py-1 rounded">
                          Not Traced
                        </span>
                      </td>
                      <td class="px-4 py-3">
                        <button @click="toggleBuyerDetails(buyer.id)"
                          class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium hover:underline cursor-pointer">
                          {{ expandedBuyers.includes(buyer.id) ? 'Hide Details' : 'Show Details' }}
                        </button>
                      </td>
                    </tr>

                    <!-- Expanded Details Row -->
                    <tr v-if="expandedBuyers.includes(buyer.id)" class="bg-gray-50 dark:bg-gray-800">
                      <td colspan="5" class="px-4 py-4">
                        <div v-if="buyer.skip_traced && (buyer.first_name || buyer.phone_numbers?.length)"
                          class="space-y-3">
                          <h5 class="font-medium text-gray-900 dark:text-white">Contact Information</h5>

                          <!-- Name and Age -->
                          <div v-if="buyer.first_name || buyer.last_name" class="flex items-center gap-4">
                            <span class="text-gray-700 dark:text-gray-200 font-medium">Name:</span>
                            <span class="text-gray-600 dark:text-gray-300">
                              {{ buyer.first_name }} {{ buyer.last_name }}
                              <span v-if="buyer.age" class="text-gray-500">(Age: {{ buyer.age }})</span>
                              <span v-if="buyer.deceased"
                                class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs px-2 py-1 rounded ml-2">
                                Deceased
                              </span>
                            </span>
                          </div>

                          <!-- Mailing Address -->
                          <div v-if="buyer.MailingFullStreetAddress" class="flex items-center gap-4">
                            <span class="text-gray-700 dark:text-gray-200 font-medium">Mailing Address:</span>
                            <span class="text-gray-600 dark:text-gray-300">
                              {{ buyer.MailingFullStreetAddress }}, {{ buyer.MailingCity }}, {{ buyer.MailingState }} {{
                                buyer.MailingZIP5 }}</span>
                          </div>
                          <div v-else class="text-gray-400 dark:text-gray-500 text-sm">
                            No mailing address
                          </div>

                          <!-- Phone Numbers -->
                          <div v-if="buyer.phone_numbers?.length" class="flex items-start gap-4">
                            <span class="text-gray-700 dark:text-gray-200 font-medium">Phone:</span>
                            <div class="space-y-1">
                              <div v-for="(phone, index) in buyer.phone_numbers" :key="index"
                                class="text-gray-600 dark:text-gray-300">
                                {{ formatPhone(phone) }}
                                <span v-if="buyer.phone_type?.[index]" class="text-gray-500 text-xs">({{
                                  buyer.phone_type[index] }})</span>
                              </div>
                            </div>
                          </div>

                          <!-- Email Addresses -->
                          <div v-if="buyer.emails?.length" class="flex items-start gap-4">
                            <span class="text-gray-700 dark:text-gray-200 font-medium">Email:</span>
                            <div class="space-y-1">
                              <div v-for="email in buyer.emails" :key="email" class="text-gray-600 dark:text-gray-300">
                                {{ email }}
                              </div>
                            </div>
                          </div>

                          <!-- Connected People -->
                          <div v-if="buyer.connected_people?.length" class="flex items-start gap-4">
                            <span class="text-gray-700 dark:text-gray-200 font-medium">Connected:</span>
                            <div class="flex flex-wrap gap-2">
                              <span v-for="person in buyer.connected_people" :key="person"
                                class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded">
                                {{ person }}
                              </span>
                            </div>
                          </div>
                        </div>
                        <div v-else class="text-gray-500 dark:text-gray-400 text-center py-4">
                          No contact information available
                        </div>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Mobile Cards (visible only on mobile) -->
          <div class="md:hidden">
            <div v-for="buyer in filteredBuyers" :key="buyer.id"
              class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
              <div class="p-4 space-y-3">
                <!-- Investor -->
                <div>
                  <div class="font-semibold text-gray-900 dark:text-white text-lg">
                    {{ buyer.investor_identifier }}
                  </div>
                </div>
                <!-- Mailing Address -->
                <div>
                  <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Owner Name</div>
                  <div v-if="buyer.first_name || buyer.last_name" class="text-gray-600 dark:text-gray-300 text-sm">
                    {{ buyer.first_name }} {{ buyer.last_name }}
                  </div>
                </div>
                <!-- Property Address -->
                <div>
                  <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Property Address</div>
                  <button
                    @click="openPropertyDetails(`${buyer.mrp_fullstreet}, ${buyer.mrp_city}, ${buyer.mrp_state} ${buyer.mrp_zip}`)"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline cursor-pointer text-left text-sm">
                    {{ buyer.mrp_fullstreet }}, {{ buyer.mrp_city }}, {{ buyer.mrp_state }} {{ buyer.mrp_zip }}
                  </button>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Paid {{ formatPrice(buyer.mrp_sales_price) }}
                    <span v-if="buyer.mrp_purchase" class="text-gray-400"> | Closed {{ formatDate(buyer.mrp_purchase)
                    }}</span>
                  </div>
                </div>

                <!-- Status and Actions Row -->
                <div class="flex items-center justify-between pt-2">
                  <div>
                    <span v-if="buyer.skip_traced" :class="(buyer.phone_numbers?.length || buyer.emails?.length) ?
                      'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' :
                      'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200'"
                      class="text-xs px-2 py-1 rounded">
                      Skip Traced
                    </span>
                    <span v-else
                      class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs px-2 py-1 rounded">
                      Not Traced
                    </span>
                  </div>
                  <button @click="toggleBuyerDetails(buyer.id)"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                    {{ expandedBuyers.includes(buyer.id) ? 'Hide Details' : 'Show Details' }}
                  </button>
                </div>

                <!-- Expanded Details (Mobile) -->
                <div v-if="expandedBuyers.includes(buyer.id)"
                  class="pt-3 border-t border-gray-200 dark:border-gray-700">
                  <div v-if="buyer.skip_traced && (buyer.first_name || buyer.phone_numbers?.length)" class="space-y-3">
                    <h5 class="font-medium text-gray-900 dark:text-white text-sm">Contact Information</h5>

                    <!-- Name and Age -->
                    <div v-if="buyer.first_name || buyer.last_name" class="flex flex-col gap-1">
                      <span class="text-gray-700 dark:text-gray-200 font-medium text-sm">Name:</span>
                      <span class="text-gray-600 dark:text-gray-300 text-sm">
                        {{ buyer.first_name }} {{ buyer.last_name }}
                        <span v-if="buyer.age" class="text-gray-500">(Age: {{ buyer.age }})</span>
                        <span v-if="buyer.deceased"
                          class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs px-2 py-1 rounded ml-2">
                          Deceased
                        </span>
                      </span>
                    </div>
                    <!-- Mailing Address -->
                    <div>
                      <div v-if="buyer.MailingFullStreetAddress" class="flex flex-col gap-1">
                        <span class="text-gray-700 dark:text-gray-200 font-medium text-sm">Mailing Address:</span>
                        <span class="text-gray-600 dark:text-gray-300 text-sm">
                          {{ buyer.MailingFullStreetAddress }}, {{ buyer.MailingCity }}, {{ buyer.MailingState }} {{
                            buyer.MailingZIP5 }}
                        </span>
                      </div>
                      <div v-else class="text-gray-400 dark:text-gray-500 text-sm">
                        No mailing address
                      </div>
                    </div>

                    <!-- Phone Numbers -->
                    <div v-if="buyer.phone_numbers?.length" class="flex flex-col gap-1">
                      <span class="text-gray-700 dark:text-gray-200 font-medium text-sm">Phone:</span>
                      <div class="space-y-1">
                        <div v-for="(phone, index) in buyer.phone_numbers" :key="index"
                          class="text-gray-600 dark:text-gray-300 text-sm">
                          {{ formatPhone(phone) }}
                          <span v-if="buyer.phone_type?.[index]" class="text-gray-500 text-xs">({{
                            formatPhone(buyer.phone_type[index]) }})</span>
                        </div>
                      </div>
                    </div>

                    <!-- Email Addresses -->
                    <div v-if="buyer.emails?.length" class="flex flex-col gap-1">
                      <span class="text-gray-700 dark:text-gray-200 font-medium text-sm">Email:</span>
                      <div class="space-y-1">
                        <div v-for="email in buyer.emails" :key="email"
                          class="text-gray-600 dark:text-gray-300 text-sm">
                          {{ email }}
                        </div>
                      </div>
                    </div>

                    <!-- Connected People -->
                    <div v-if="buyer.connected_people?.length" class="flex flex-col gap-1">
                      <span class="text-gray-700 dark:text-gray-200 font-medium text-sm">Connected:</span>
                      <div class="flex flex-wrap gap-2">
                        <span v-for="person in buyer.connected_people" :key="person"
                          class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded">
                          {{ person }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-gray-500 dark:text-gray-400 text-center py-4 text-sm">
                    No contact information available
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- (Re)Skip Trace Prompt -->
    <div v-if="showTracePrompt" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-xl w-[92vw] max-w-md p-5">
        <div class="flex items-start justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Skip Trace Options</h3>
          <button @click="closeTracePrompt"
            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">✕</button>
        </div>

        <div class="space-y-3">
          <!-- Option B - Only not appended (DEFAULT) -->
          <label
            class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer">
            <input type="radio" class="mt-1" value="missing" v-model="traceScope" :disabled="notAppendedCount === 0" />
            <div class="flex-1">
              <div class="font-medium text-gray-900 dark:text-gray-100">
                Only not appended ({{ notAppendedCount }})
              </div>
              <div class="text-xs text-gray-500 dark:text-gray-400">
                Buyers that have no phones or emails yet.
              </div>
            </div>
          </label>

          <!-- Option A - All -->
          <label
            class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer">
            <input type="radio" class="mt-1" value="all" v-model="traceScope" />
            <div class="flex-1">
              <div class="font-medium text-gray-900 dark:text-gray-100">
                Skip trace all ({{ totalCount }})
              </div>
              <div class="text-xs text-gray-500 dark:text-gray-400">
                Re-runs on everyone in the list.
              </div>
            </div>
          </label>
        </div>

        <div class="mt-5 flex justify-end gap-2">
          <button @click="closeTracePrompt"
            class="px-4 h-9 rounded-md border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-neutral-700">
            Cancel
          </button>
          <button @click="confirmTrace" class="px-4 h-9 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-neutral-700">
            Continue
          </button>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted} from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import axios from 'axios';
import { type BreadcrumbItem } from '@/types';
import { useNumber, useDateFormat } from '@/composables/useFormat';
import PageHeading from '@/components/ui/page-heading/PageHeading.vue';

const { formatDate } = useDateFormat();
const { formatPhone } = useNumber();
// const allBuyersSkipTraced = computed(() => {
//   const buyers = selectedList.value?.buyers || [];
//   return buyers.length > 0 && buyers.every(b => b.skip_traced);
// });
const openPropertyDetails = async (address: string) => {
  try {
    // First search for the property by address to get the numeric ID
    const response = await axios.get(route('data-db-api.get_property_minimal_list_by_text'), {
      params: {
        query: address
      }
    });

    if (response.data?.data?.length > 0) {
      // Get the first matching property's numeric ID
      const propertyId = response.data.data[0].id;

      // Open property search page with the numeric property ID
      window.open(`/property/search?propertyid=${propertyId}`, '_blank');
    } else {
      // If no property found, show alert
      alert('Property not found for this address');
    }
  } catch (error) {
    console.error('Error searching property:', error);
    alert('Error searching for property');
  }
};
// Add these computed properties after your existing computed properties
const totalSkipTraceStats = computed(() => {
  return buyerLists.value.reduce((stats, list) => {
    stats.total_buyers += list.buyer_count;
    stats.total_skip_traced += list.skip_traced_count;
    stats.total_successful += list.successful_count || 0;
    stats.total_failed += list.failed_count || 0;
    stats.total_emails_returned += list.total_emails_returned || 0;
    stats.total_phones_returned += list.total_phones_returned || 0;
    return stats;
  }, {
    total_buyers: 0,
    total_skip_traced: 0,
    total_successful: 0,
    total_failed: 0,
    total_emails_returned: 0,
    total_phones_returned: 0
  });
});
// Add this computed property
const communitySkipTraces = computed(() => {
  // This will be populated from the backend
  return props.communitySkipTraces || 0;
});
// Add these after your existing refs
const searchQuery = ref('');
const expandedBuyers = ref<number[]>([]);

// Add these new refs and interfaces
const lastDays = ref<number>(30);
const stats = ref<any>({
  from: '',
  to: '',
  total_skip_traces: 0,
  total_skip_traces_prev: 0,
  successful_count: 0,
  successful_count_prev: 0,
  failed_count: 0,
  failed_count_prev: 0,
  total_emails_returned: 0,
  total_emails_returned_prev: 0,
  total_phones_returned: 0,
  total_phones_returned_prev: 0,
  community_skip_traces: 0,
  community_skip_traces_prev: 0,
});

const rangeOptions = [
  { label: '1d', value: 1 },
  { label: '1m', value: 30 },
  { label: '3m', value: 90 },
  { label: '6m', value: 180 },
  { label: '1yr', value: 365 },
];
function changeDateRange(days: number) {
  lastDays.value = days;
  // Reload the page with new date filter
  router.get(route('buyers.skip_tracing'), {
    list_id: selectedListId.value,
    lastDays: days
  });
}
// Add these functions

const getStats = async () => {
  try {
    // Calculate the correct date range
    const toDate = new Date();
    const fromDate = new Date();
    fromDate.setDate(fromDate.getDate() - lastDays.value);
    
    // Format dates for display
    const from = fromDate.toISOString().split('T')[0];
    const to = toDate.toISOString().split('T')[0];
    
    // Update stats with correct date range
    stats.value = {
      from: from,
      to: to,
      total_skip_traces: totalSkipTraceStats.value.total_skip_traced,
      total_skip_traces_prev: 0, // You can calculate this later
      successful_count: totalSkipTraceStats.value.total_successful,
      successful_count_prev: 0,
      failed_count: totalSkipTraceStats.value.total_failed,
      failed_count_prev: 0,
      total_emails_returned: totalSkipTraceStats.value.total_emails_returned,
      total_emails_returned_prev: 0,
      total_phones_returned: totalSkipTraceStats.value.total_phones_returned,
      total_phones_returned_prev: 0,
      community_skip_traces: communitySkipTraces.value,
      community_skip_traces_prev: 0
    };
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const percentChange = (current: number, prev: number): string => {
  if (prev === 0 && current === 0) return '0%';
  if (prev === 0) return '+100%';
  const change = ((current - prev) / Math.abs(prev)) * 100;
  return `${change > 0 ? '+' : ''}${change.toFixed(2)}%`;
};

const changeClass = (current: number, prev: number): string => {
  if (prev === 0 && current === 0) return 'text-gray-500';
  if (current > prev) return 'text-green-600 dark:text-green-400';
  if (current < prev) return 'text-red-600 dark:text-red-400';
  return 'text-gray-500';
};

const periodLabel = () => {
  switch (lastDays.value) {
    case 1: return 'from yesterday';
    case 30: return 'from last month';
    case 90: return 'from last quarter';
    case 180: return 'from last 6 months';
    case 365: return 'from last year';
    default: return 'from previous period';
  }
};

// Add this to your onMounted or existing setup
onMounted(() => {
  getStats();
});
// Add this computed property
const filteredBuyers = computed(() => {
  if (!selectedList.value?.buyers) return [];

  let buyers = selectedList.value.buyers;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    buyers = buyers.filter(buyer =>
      buyer.investor_identifier.toLowerCase().includes(query) ||
      buyer.mrp_fullstreet.toLowerCase().includes(query) ||
      buyer.mrp_city.toLowerCase().includes(query)
    );
  }

  return buyers;
});
// function changePeriod(days: number) {
//   lastDays.value = days;
//   // Reload the page with new date filter
//   router.get(route('buyers.skip_tracing'), {
//     list_id: selectedListId.value,
//     lastDays: days
//   });
// }
// Add this function
function toggleBuyerDetails(buyerId: number) {
  const index = expandedBuyers.value.indexOf(buyerId);
  if (index > -1) {
    expandedBuyers.value.splice(index, 1);
  } else {
    expandedBuyers.value.push(buyerId);
  }
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Buyer Skip Tracing',
    href: '/buyers/skip-tracing',
  },
];

// --- TypeScript interfaces for strong typing ---
interface Buyer {
  id: number;
  investor_identifier: string;
  mrp_fullstreet: string;
  mrp_city: string;
  mrp_state: string;
  mrp_zip: string;
  mrp_sales_price: number | string;
  mrp_purchase?: string;
  first_name?: string;
  last_name?: string;
  age?: number;
  deceased?: boolean;
  phone_numbers?: string[];
  phone_type?: string[];
  emails?: string[];
  street?: string;
  city?: string;
  state?: string;
  zip?: string;
  connected_people?: string[];
  skip_traced?: boolean;
  MailingFullStreetAddress?: string;
  MailingCity?: string;
  MailingState?: string;
  MailingZIP5?: string;
}

interface BuyerList {
  id: number;
  name: string;
  buyer_count: number;
  skip_traced_count: number;
  successful_count: number;
  failed_count: number;
  total_emails_returned?: number;
  total_phones_returned?: number;
  total_contact_info_returned?: number;
  created_at: string;
  buyers: Buyer[];
}

// Props from Inertia
const props = defineProps<{
  buyerLists: BuyerList[];
  selectedListId: string | number | null;
  user: any;
  communitySkipTraces: number;
}>();

const buyerLists = ref<BuyerList[]>(props.buyerLists || []);
const selectedListId = ref<string | number>(props.selectedListId || (buyerLists.value[0]?.id ?? ''));
const isSkipTracing = ref(false);
const showSuccessMessage = ref(false);
const successMessage = ref('');
// add this
// const userChoice = ref<'all' | 'missing'>('missing');



const selectedList = computed<BuyerList | undefined>(() => {
  return buyerLists.value.find(list => list.id == selectedListId.value);
});

function onListChange(val: string | number) {
  selectedListId.value = val;
}


function formatPrice(price: number | string) {
  if (!price) return '';
  const num = typeof price === 'string' ? parseFloat(price) : price;
  if (isNaN(num)) return '';
  if (num >= 1000000) return `$${(num / 1000000).toFixed(1)}M`;
  if (num >= 1000) return `$${Math.round(num / 1000)}K`;
  return `$${num}`;
}

// async function startSkipTracing() {
//   if (!selectedList.value) return;
//   isSkipTracing.value = true;
//   try {
//     const response = await axios.post(`/buyers/skip-tracing/${selectedList.value.id}`, {
//       list_id: selectedList.value.id,
//       scope: userChoice.value === 'all' ? 'all' : 'missing',
//     });
//     if (response.data.success) {
//       // Show nice success message instead of alert
//       successMessage.value = response.data.message || 'Skip tracing completed successfully!';
//       showSuccessMessage.value = true;

//       // Set skip trace completed to true

//       // Auto-hide success message after 5 seconds
//       setTimeout(() => {
//         showSuccessMessage.value = false;
//       }, 5000);

//       // Update the buyer data in place instead of refreshing the page
//       if (response.data.results) {
//         const updatedBuyers = response.data.results.map((result: any) => {
//           if (result.success && result.contact_info) {
//             return {
//               ...selectedList.value!.buyers.find(b => b.id === result.buyer_id),
//               ...result.contact_info,
//               skip_traced: true
//             };
//           }
//           return selectedList.value!.buyers.find(b => b.id === result.buyer_id);
//         });

//         // Update the selected list with new buyer data
//         const listIndex = buyerLists.value.findIndex(list => list.id === selectedList.value!.id);
//         if (listIndex !== -1) {
//           buyerLists.value[listIndex].buyers = updatedBuyers;
//           buyerLists.value[listIndex].skip_traced_count = updatedBuyers.filter((b: any) => b.skip_traced).length;
//         }
//       }
//     } else {
//       // Show error message
//       successMessage.value = response.data.message || 'Skip tracing failed';
//       showSuccessMessage.value = true;
//     }
//   } catch (e: any) {
//     console.error('Skip tracing error:', e);
//     successMessage.value = 'Skip tracing failed: ' + (e.response?.data?.message || e.message || 'Unknown error');
//     showSuccessMessage.value = true;
//   } finally {
//     isSkipTracing.value = false;
//   }
// }
// === (Re)Skip prompt state ===
const showTracePrompt = ref(false);
const traceScope = ref<'missing' | 'all'>('missing'); // default is "Only not appended"
// add this


// Counts for the prompt
const totalCount = computed(() => selectedList.value?.buyers?.length ?? 0);

// "Appended" means it already has any contact info (phones or emails) from anyone (community or me)
const notAppendedCount = computed(() => {
  const b = selectedList.value?.buyers ?? [];
  return b.filter(x =>
    !(Array.isArray(x.emails) && x.emails.length > 0) &&
    !(Array.isArray(x.phone_numbers) && x.phone_numbers.length > 0)
  ).length;
});

// Any appended at all? (used for coloring the button)
const hasAnyAppended = computed(() => (totalCount.value - notAppendedCount.value) > 0);

// Open / close prompt
function openTracePrompt() {
  // default to "Only not appended" if there are any missing; otherwise "all"
  traceScope.value = notAppendedCount.value > 0 ? 'missing' : 'all';
  showTracePrompt.value = true;
}
function closeTracePrompt() {
  showTracePrompt.value = false;
}

// Confirm from prompt → actually call API with scope
async function confirmTrace() {
  if (!selectedList.value) return;
  isSkipTracing.value = true;
  try {
    const response = await axios.post(`/buyers/skip-tracing/${selectedList.value.id}`, {
      list_id: selectedList.value.id,
      scope: traceScope.value, // 'missing' | 'all'
    });

    if (response.data.success) {
      successMessage.value = response.data.message || 'Skip tracing completed successfully!';
      showSuccessMessage.value = true;
      setTimeout(() => (showSuccessMessage.value = false), 5000);

      if (response.data.results) {
        const updatedBuyers = response.data.results.map((result: any) => {
          if (result.success && result.contact_info) {
            return {
              ...selectedList.value!.buyers.find(b => b.id === result.buyer_id),
              ...result.contact_info,
              skip_traced: true
            };
          }
          return selectedList.value!.buyers.find(b => b.id === result.buyer_id);
        });

        const listIndex = buyerLists.value.findIndex(list => list.id === selectedList.value!.id);
        if (listIndex !== -1) {
          buyerLists.value[listIndex].buyers = updatedBuyers;
          buyerLists.value[listIndex].skip_traced_count =
            updatedBuyers.filter((b: any) => b.skip_traced).length;
        }
      }
    } else {
      successMessage.value = response.data.message || 'Skip tracing failed';
      showSuccessMessage.value = true;
    }
  } catch (e: any) {
    console.error('Skip tracing error:', e);
    successMessage.value = 'Skip tracing failed: ' + (e.response?.data?.message || e.message || 'Unknown error');
    showSuccessMessage.value = true;
  } finally {
    isSkipTracing.value = false;
    showTracePrompt.value = false;
  }
}

</script>
