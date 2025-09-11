<template>
  <div class="buyer-popup-inner bg-white dark:bg-neutral-800 border border-gray-200 dark:border-gray-700">
    <div class="popup-left">
      <div class="font-bold text-lg text-gray-900 dark:text-white">{{ buyer.mrp_fullstreet }}</div>
      <div class="text-gray-500 dark:text-gray-300 text-sm">{{ buyer.mrp_city }}, {{ buyer.mrp_state }} {{ buyer.mrp_zip }}</div>
      <div class="my-2">
        <span class="font-semibold text-gray-700 dark:text-gray-200">Paid {{ formatPrice(buyer.mrp_sales_price) }}</span>
        <span v-if="buyer.mrp_purchase" class="text-gray-400 dark:text-gray-400">({{ formatDate(buyer.mrp_purchase) }})</span>
      </div>
      <div class="flex gap-4 text-sm text-gray-600 dark:text-gray-300">
        <span>🏠 {{ buyer.mrp_beds }} bd</span>
        <span>🛁 {{ buyer.mrp_bath }} ba</span>
        <span>📏 {{ buyer.mrp_sqft }} sqft</span>
      </div>
    </div>
    <div class="popup-right flex flex-col items-end min-w-[120px] gap-1">
      <div class="font-semibold text-xs text-gray-700 dark:text-gray-200 text-right">
        {{ buyer.investor_identifier }}
      </div>
      <span v-if="buyer.investor_identifier"
                class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-[10px] px-2 py-0.5 rounded">
                LLC
              </span>
      <span v-if="buyer.most_likely_buyer_tag" class="badge-gold dark:bg-yellow-600 dark:text-white">{{ buyer.most_likely_buyer_tag }}</span>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useNumber, useDateFormat } from '@/composables/useFormat';

const { formatPrice } = useNumber();
const { formatDate } = useDateFormat();

defineProps<{ buyer: any }>();
</script>
<style scoped>
.buyer-popup-inner {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  min-width: 320px;
  padding: 20px 24px 16px 24px;
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.12);
}

/* Dark theme shadow */
.dark .buyer-popup-inner {
  box-shadow: 0 4px 24px rgba(0,0,0,0.3);
}

.badge-black {
  background: #111;
  color: #fff;
  border-radius: 6px;
  padding: 2px 8px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 4px;
  display: inline-block;
}

.badge-gold {
  background: #e6c98f;
  color: #222;
  border-radius: 6px;
  padding: 2px 8px;
  font-size: 13px;
  font-weight: 600;
  margin-left: 4px;
  display: inline-block;
}
</style>


