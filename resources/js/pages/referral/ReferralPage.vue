<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, User, UserReferralW9 } from '@/types';
import { Head } from '@inertiajs/vue3';
import PageHeading from "@/components/ui/page-heading/PageHeading.vue";
import { useNumber } from "@/composables/useFormat";
import { ref } from "vue";
import HeadingSmall from "@/components/HeadingSmall.vue";
import Button from "@/components/ui/button/Button.vue";
import { useClipboardCopy } from "@/composables/useCopy";
import { useToast } from "@/composables/useToast";
import Input from "@/components/ui/input/Input.vue";
import W9FilesDialog from "@/components/referral/W9FilesDialog.vue";
import axios from 'axios';

const { showToast } = useToast()
const { formatNumber, formatPrice } = useNumber()
// const { formatDate, formatDateTime } = useDateFormat()
const { handleCopy, copied } = useClipboardCopy()

// const page = usePage<SharedData>();
const props = defineProps<{
  status?: string,
  total_affiliates: number,
  eligibleAffiliate: User[],
  eligibleAffiliateCount: number,
  earnings: { 
    id: number, 
    from: string, 
    amount: number, 
    status: string,
    type: string,
    description: string,
    created_at: string,
    updated_at: string,
    paid_at: string | null
  }[],
  totalEarning: number,
  pendingEarnings: number,
  approvedEarnings: number,
  paidEarnings: number,
  canPayout: boolean,
  minPayoutThreshold: number,
  referralLink: string,
  w9s: UserReferralW9[],
}>();

// const user = computed(() => page.props.auth.user as User);

const tab = ref<number>(1);

const copyLink = (txt: string) => {
  handleCopy(txt)
  if (copied) {
    showToast('Copied', 'Referral Link has been copied!')
  }
}

const uploading = ref(false);
const w9File = ref<File | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const handleFileChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    w9File.value = file;
  }
  console.log(w9File.value);
}
const uploadW9 = () => {
  if (!w9File.value) {
    showToast('Error', 'Please select a file first', 'error')
    return;
  }

  uploading.value = true;

  const formData = new FormData();
  formData.append('w9File', w9File.value);

  axios.post(route('referral.upload-w9'), formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  }).then((response) => {
    if (response.data.success) {
      showToast('Success', response.data.message, 'success')
      w9File.value = null; // Clear the file input
      if (fileInput.value) {
        fileInput.value.value = ''; // Clear the file input element
      }
      // Optionally refresh the page or update the w9s list
      window.location.reload();
    } else {
      showToast('Error', response.data.error, 'error')
    }
  }).catch((error) => {
    if (error?.response?.data?.errors?.w9File) {
      showToast('Error', error.response.data.errors.w9File[0], 'error')
    } else if (error?.response?.data?.message) {
      showToast('Error', error.response.data.message, 'error')
    } else {
      showToast('Error', 'Upload failed. Please try again.', 'error')
    }
  }).finally(() => {
    uploading.value = false;
  });
}
const openW9Dialog = ref(false);
const openW9list = () => {
  openW9Dialog.value = true;
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Referrals',
    href: '/referrals',
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="Referrals" />
    <PageHeading title="Overview" />
    <div class="flex-1 flex-col gap-4 rounded-xl p-4 h-[calc(100dvh-100px)] md:h-[calc(100dvh-120px)] overflow-y-auto">
      <div class="w-full ">
        <div class="w-full grid grid-cols-1 md:grid-cols-4 justify-center gap-3">
          <div class="border rounded-lg w-full px-6 py-5 text-center shadow-sm">
            <div class="font-bold">All Affiliates</div>
            <div class="font-medium text-gray-500 dark:text-gray-400">{{ formatNumber(props.total_affiliates) }}</div>
          </div>
          <div class="border rounded-lg w-full px-6 py-5 text-center shadow-sm">
            <div class="font-bold">All Eligible Affiliates</div>
            <div class="font-medium text-gray-500 dark:text-gray-400">{{
              formatNumber(props.eligibleAffiliateCount)
            }}
            </div>
          </div>
          <div class="border rounded-lg w-full px-6 py-5 text-center shadow-sm">
            <div class="font-bold">Total Earnings</div>
            <div class="font-medium text-gray-500 dark:text-gray-400">{{ formatPrice(props.totalEarning) }}</div>
          </div>
          <div class="border rounded-lg w-full px-6 py-5 text-center shadow-sm">
            <div class="font-bold">Pending Earnings</div>
            <div class="font-medium text-gray-500 dark:text-gray-400">{{ formatPrice(props.pendingEarnings) }}</div>
            <div v-if="props.canPayout" class="text-xs text-green-600 mt-1">Ready for payout!</div>
            <div v-else class="text-xs text-gray-500 mt-1">Min: ${{ props.minPayoutThreshold }}</div>
          </div>
        </div>
      </div>
      <div class="w-full my-3">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
          <div class="">
            <HeadingSmall title="My Referral Link" />
            <div class="max-w-full border truncate underline text-primary text-sm px-4 py-2 rounded-lg"
              @click="copyLink(referralLink)" variant="link">{{ referralLink }}</div>
          </div>
          <div class="">
            <HeadingSmall title="Upload W9 Document" />
            <div class="flex">
              <Input ref="fileInput" type="file" class="flex-grow rounded-r-none" @change="handleFileChange"
                accept="application/pdf" />
              <Button @click="uploadW9" class="rounded-l-none" variant="default" :disabled="uploading"
                :loading="uploading">Upload</Button>
              <Button @click="openW9list" v-if="props.w9s.length > 0" class="ml-2" variant="default"
                :disabled="uploading" :loading="uploading">View Files</Button>
            </div>
          </div>
        </div>
      </div>
      <div class="w-full flex justify-center my-4">
        <Button class="rounded-r-none" @click="tab = 1" :variant="tab == 1 ? 'default' : 'outline'">View
          Affiliates</Button>
        <Button class="rounded-l-none" @click="tab = 2" :variant="tab == 2 ? 'default' : 'outline'">View
          Earnings</Button>
      </div>
      <div class="flex flex-col md:flex-row gap-2 md:gap-4 h-[calc(100dvh-460px)] md:h-[calc(100dvh-460px)]">
        <div class="w-full" v-if="tab == 1">
          <HeadingSmall title="Affiliates" />
          <br />
          <table class="table-auto w-full">
            <thead>
              <tr>
                <th>Email</th>
                <th>Joint At</th>
                <th>Plan</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="affiliate in props.eligibleAffiliate" :key="affiliate.id">
                <td class="text-sm border-b">{{ affiliate.email }}</td>
                <td class="text-sm border-b">{{ affiliate.created_at }}</td>
                <td class="text-sm border-b">{{ affiliate.user_type }}</td>
                <td class="text-sm border-b">{{ affiliate.subscription_status }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="w-full" v-else>
          <HeadingSmall title="Earnings" />
          <br />
          <table class="table-auto w-full">
            <thead>
              <tr>
                <th>From</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="earning in props.earnings" :key="earning.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="text-sm border-b px-2 py-2">{{ earning.from }}</td>
                <td class="text-sm border-b px-2 py-2">
                  <span class="px-2 py-1 rounded text-xs" 
                        :class="{
                          'bg-blue-100 text-blue-800': earning.type === 'subscription_upgrade',
                          'bg-green-100 text-green-800': earning.type === 'first_payment',
                          'bg-purple-100 text-purple-800': earning.type === 'recurring_payment',
                          'bg-orange-100 text-orange-800': earning.type === 'bonus'
                        }">
                    {{ earning.type.replace('_', ' ').toUpperCase() }}
                  </span>
                </td>
                <td class="text-sm border-b px-2 py-2 font-medium">{{ formatPrice(earning.amount) }}</td>
                <td class="text-sm border-b px-2 py-2">
                  <span class="px-2 py-1 rounded text-xs" 
                        :class="{
                          'bg-yellow-100 text-yellow-800': earning.status === 'pending',
                          'bg-green-100 text-green-800': earning.status === 'approved',
                          'bg-blue-100 text-blue-800': earning.status === 'paid',
                          'bg-red-100 text-red-800': earning.status === 'rejected',
                          'bg-gray-100 text-gray-800': earning.status === 'cancelled'
                        }">
                    {{ earning.status.toUpperCase() }}
                  </span>
                </td>
                <td class="text-sm border-b px-2 py-2">{{ new Date(earning.created_at).toLocaleDateString() }}</td>
                <td class="text-sm border-b px-2 py-2 max-w-xs truncate" :title="earning.description">
                  {{ earning.description }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
  
    <!-- W9 Files Dialog -->
    <W9FilesDialog 
      v-model:open="openW9Dialog" 
      :w9s="props.w9s" 
    />
  </AppLayout>
</template>
