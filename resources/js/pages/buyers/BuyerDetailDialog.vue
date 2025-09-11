<template>
  <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Backdrop with blur effect -->
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm" @click="close"></div>
    <div v-if="buyer"
      class="bg-white dark:bg-neutral-800 rounded-xl shadow-xl p-6 w-full max-w-lg relative z-10 border border-gray-200 dark:border-gray-700">
      <button @click="close"
        class="absolute top-4 right-4 text-2xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">&times;</button>
      <div class="font-bold text-lg mb-1 text-gray-900 dark:text-white">{{ buyer.mrp_fullstreet }}</div>
      <div class="text-gray-600 dark:text-gray-300 mb-2">{{ buyer.mrp_city }}, {{ buyer.mrp_state }} {{ buyer.mrp_zip }}
      </div>
      <div class="flex items-center mb-2">
        <span class="mr-2 text-gray-700 dark:text-gray-200">💲 Paid {{ formatPrice(buyer.mrp_sales_price) }} <span
            v-if="buyer.mrp_purchase" class="text-gray-500 dark:text-gray-400">({{ formatDate(buyer.mrp_purchase)
            }})</span></span>
      </div>
      <div class="flex items-center mb-2">
        <span class="mr-2 text-gray-700 dark:text-gray-200">🏠 {{ buyer.mrp_beds }} bd | {{ buyer.mrp_bath }} ba | {{
          buyer.mrp_sqft }} sqft</span>
      </div>
      <div class="flex items-center mb-2">
        <span class="font-semibold mr-2 text-gray-900 dark:text-white">{{ buyer.investor_identifier }}</span>
        <span v-if="buyer.investor_identifier"
          class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs px-2 py-1 rounded">LLC</span>
        <span v-if="buyer.most_likely_buyer_tag"
          class="bg-yellow-200 dark:bg-yellow-700 text-yellow-800 dark:text-yellow-100 text-xs px-2 py-1 rounded ml-2">{{
            buyer.most_likely_buyer_tag }}</span>
      </div>
      <!-- Public Tags Section (visible to all users) -->
      <div v-if="buyer.public_tags && buyer.public_tags.length > 0" class="mb-2">
        <div class="flex flex-wrap gap-1">
          <span v-for="tag in buyer.public_tags" :key="tag" :class="tag === 'Skip-Traced' ?
            (buyer.skip_trace_has_contact_info ?
              'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border-green-200 dark:border-green-700' :
              'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 border-orange-200 dark:border-orange-700') :
            'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 border-blue-200 dark:border-blue-700'"
            class="text-xs px-2 py-1 rounded border">
            {{ tag }}
          </span>
        </div>
      </div>

      <!-- Private Tags Section (only visible to owner) -->
      <div v-if="buyer.private_tags && buyer.private_tags.length > 0" class="mb-2">
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Your Tags:</div>
        <div class="flex flex-wrap gap-1">
          <span v-for="tag in buyer.private_tags" :key="tag"
            class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 border-blue-200 dark:border-blue-700 text-xs px-2 py-1 rounded border">
            {{ tag }}
          </span>
        </div>
      </div>

      <hr class="my-3 border-gray-200 dark:border-gray-600" />

      <!-- Improved Owner Information Section -->
      <div v-if="buyer.skip_traced && (buyer.owner_name || buyer.owner_phone || buyer.owner_email)" class="space-y-3">
        <div class="font-semibold text-gray-900 dark:text-white">Owner Information</div>
        <div class="space-y-2">
          <div v-if="buyer.owner_name" class="flex items-center gap-2">
            <Icon icon="tabler:user" class="text-gray-400 w-4 h-4 flex-shrink-0" />
            <span class="text-gray-700 dark:text-gray-200">
              {{ buyer.owner_name }}
              <span v-if="buyer.owner_age" class="text-gray-500 dark:text-gray-400">(Age: {{ buyer.owner_age }})</span>
              <span v-if="buyer.owner_deceased"
                class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs px-2 py-1 rounded ml-2">
                Deceased
              </span>
            </span>
          </div>
          <div v-if="buyer.owner_phone && buyer.owner_phone !== 'Not Found'" class="flex items-center gap-2">
            <Icon icon="tabler:phone" class="text-gray-400 w-4 h-4 flex-shrink-0" />
            <span class="text-gray-700 dark:text-gray-200">{{ formatPhone(buyer.owner_phone) }}</span>
          </div>
          <div v-if="buyer.owner_email && buyer.owner_email !== 'Not Found'" class="flex items-center gap-2">
            <Icon icon="tabler:mail" class="text-gray-400 w-4 h-4 flex-shrink-0" />
            <span class="text-gray-700 dark:text-gray-200">{{ buyer.owner_email }}</span>
          </div>
          <div v-if="buyer.MailingFullStreetAddress" class="flex items-start gap-2">
            <Icon icon="tabler:map-pin" class="text-gray-400 w-4 h-4 flex-shrink-0 mt-0.5" />
            <span class="text-gray-700 dark:text-gray-200">
              {{ buyer.MailingFullStreetAddress }}, {{ buyer.MailingCity }}, {{ buyer.MailingState }} {{
                buyer.MailingZIP5 }}
            </span>
          </div>
        </div>
      </div>

      <!-- Fallback for when no skip trace data -->
      <div v-else-if="buyer.MailingFullStreetAddress" class="space-y-3">
        <div class="font-semibold text-gray-900 dark:text-white">Owner Information</div>
        <div class="flex items-start gap-2">
          <Icon icon="tabler:map-pin" class="text-gray-400 w-4 h-4 flex-shrink-0 mt-0.5" />
          <span class="text-gray-700 dark:text-gray-200">
            {{ buyer.MailingFullStreetAddress }}, {{ buyer.MailingCity }}, {{ buyer.MailingState }} {{ buyer.MailingZIP5
            }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup lang="ts">
import { useNumber, useDateFormat } from '@/composables/useFormat';
import { Icon } from '@iconify/vue'

const { formatPrice } = useNumber();
const { formatDate } = useDateFormat();
const { formatPhone } = useNumber();

const props = defineProps({
  buyer: {
    type: Object,
    default: null,
    required: false
  },
  visible: {
    type: Boolean,
    default: false
  }
});
console.log('BuyerDetailDialog buyer:', props.buyer);
const emit = defineEmits(['close']);
function close() {
  emit('close');
}
</script>