<script setup lang="ts">
import { HTMLAttributes, onMounted, ref } from "vue";
import { Icon } from '@iconify/vue'


import { PropertyStatusBadge } from "@/components/ui/proprety-status-badge";
import { useDateFormat, useNumber } from "@/composables/useFormat";
import { getMainImage, PropertyStatus } from "../../types/property";
import Separator from "@/components/ui/separator/Separator.vue";
import { cn } from "@/lib/utils";
import { WholesaleProperty } from "@/types/wholesale";
import ConfirmDialog from "@/components/ui/confirm-dialog/ConfirmDialog.vue";
import { useConfirmDialog } from "@/composables/useConfirmDialog";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import { useToast } from "@/composables/useToast";
import Toast from "@/components/ui/toast/Toast.vue";
import { addPropertyIdToUrl } from "@/lib/zilowAndlocationUtil";
import CustomTooltip from "@/components/ui/custom-tooltip/CustomTooltip.vue";

const props = defineProps<{
  wholesaleProperty: WholesaleProperty,
  isLive: boolean,
  class?: HTMLAttributes['class']
}>()

defineEmits<{
  (e: 'click'): void;
}>();

const { formatNumber, formatPrice } = useNumber()
const { formatDate, formatGetDays } = useDateFormat()

const propertyImage = ref<string>('');
onMounted(async () => {
  propertyImage.value = getMainImage(props.wholesaleProperty?.images?.join(','));
})
const { showToast } = useToast()
const confirmDialog = useConfirmDialog()
const onHandlingConfirmation = async (message: string, type: string, id: number) => {
  const confirmed = await confirmDialog.openConfirm(message, 'Make it Canceled')
  if (confirmed) {
    if (type == 'cancel') {
      try {
        const response = await axios.post(route('my_properties.action.cancel'), { id: id })
        if (response.data?.error) {
          showToast('Cancel Property', response.data?.error, 'error')
          return;
        }
        router.visit(route('my_properties'), {
          replace: true,
          preserveScroll: true,
        })
        showToast('Cancel Property', response.data?.success ?? 'This property cancellation request has been submitted.', 'success');
      } catch (err) {
        showToast('Cancel Property', 'Error: ' + err?.toString(), 'error')
      } finally {

      }
    }
  } else {
    console.log("Cancelled")
  }
}

const viewProperty = (id: number) => {
  addPropertyIdToUrl(id);
}

const goToAddressRequests = () => {
  router.visit(route('my_properties.address_requests', props.wholesaleProperty.id))
}
const goToCheckView = () => {
  router.visit(route('my_properties.view_list', props.wholesaleProperty.id))
}

const goToEmailMarketing = () => {
  router.visit(route('email-marketing.campaign') + '/?property_id=' + props.wholesaleProperty.id)
}

</script>

<template class="relative">
  <div @click="$emit('click')" class="rounded-lg shadow-md text-sm font-normal cursor-pointer border"
    :class="cn(props.class, isLive ? 'border-2 border-green-600' : '')">
    <div class="img relative h-[200px] p-2 bg-white rounded-t-lg bg-cover bg-center"
      :style="{ backgroundImage: `url(${propertyImage})` }">
      <div class="property-status absolute top-2 left-2">
        <PropertyStatusBadge :status="wholesaleProperty.status" />
      </div>
      <div class="action-buttons absolute top-2 right-2 flex gap-3 rounded-lg px-2 py-1 bg-gray-800/40">
        <CustomTooltip text="Email Marketing" v-if="wholesaleProperty.database_id">
          <Icon @click.stop="goToEmailMarketing()" class=" text-white hover:text-green-600 action-icon"
            icon="solar:letter-opened-line-duotone" />
        </CustomTooltip>
        <CustomTooltip text="View Live Property Detail" v-if="wholesaleProperty.database_id">
          <Icon @click.stop="viewProperty(wholesaleProperty.database_id)"
            class=" text-white hover:text-green-600 action-icon" icon="solar:eye-scan-line-duotone" />
        </CustomTooltip>
        <CustomTooltip text="Views Histories">
          <Icon class=" text-white hover:text-primary action-icon" icon="solar:clipboard-list-line-duotone"
            @click.stop="goToCheckView()" />
        </CustomTooltip>
        <CustomTooltip text="Address Request">
          <Icon class=" text-white hover:text-primary action-icon" icon="solar:user-id-line-duotone"
            @click.stop="goToAddressRequests()" />
        </CustomTooltip>
        <CustomTooltip text="Edit">
          <Icon class=" text-white hover:text-primary action-icon" icon="solar:clapperboard-edit-line-duotone" />
        </CustomTooltip>
        <CustomTooltip text="Cancel the property">
          <Icon class=" text-white hover:text-red-600/50 action-icon" icon="tabler:trash"
            @click.stop="onHandlingConfirmation('Would you like to cancel this property?', 'cancel', wholesaleProperty.id)" />
        </CustomTooltip>
      </div>

      <div class="absolute bottom-2 left-2 flex flex-col items-start gap-2 text-sm">
        <div @click.stop="goToCheckView()" class="flex-1 flex p-1 rounded-lg bg-gray-800/40 px-2 text-white">
          <Icon icon="tabler:eye" class="size-5 " /> &nbsp;{{ wholesaleProperty.view_histories_count }}
        </div>
        <CustomTooltip text="Pending Address Requests" v-if="wholesaleProperty.pending_address_requests_count">
          <div @click.stop="goToAddressRequests()" class="flex-1 flex p-1 rounded-lg bg-gray-800/40 px-2 text-white">
            <Icon icon="solar:user-id-line-duotone" class="size-5" /> &nbsp; Pending &nbsp;
            <span class="bg-primary text-black rounded-full px-2">{{
              wholesaleProperty.pending_address_requests_count
            }}</span>
          </div>
        </CustomTooltip>
      </div>

      <div class="absolute bottom-2 right-2 flex flex-col items-end gap-2 text-sm">
        <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold"
          v-if="wholesaleProperty.status == PropertyStatus.Closed">{{
            formatDate(wholesaleProperty.closed_date)
          }}
        </div>
        <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold"
          v-if="wholesaleProperty.status != PropertyStatus.Closed">
          {{ formatGetDays(wholesaleProperty.created_at) }}
          DOM
        </div>
        <div class="rounded-full bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold flex"
          v-if="!isLive">
          <Icon icon="tabler:circle-dashed" class="size-5" /> &nbsp;Draft
        </div>
        <div v-else
          class="rounded-full bg-gray-800/70 py-1 px-2 w-fit text-right font-semibold flex border-2 border-green-500 shadow-md text-green-500">
          <Icon icon="tabler:circle-check" class="size-5" /> &nbsp;Live
        </div>
      </div>
    </div>
    <div class="bottom-container">
      <div class="price-container flex py-2 gap-2 px-3">
        <div class="font-semibold text-lg/4 my-auto">{{
          wholesaleProperty.status == PropertyStatus.Closed ? formatPrice(wholesaleProperty.closed_price) :
            formatPrice(wholesaleProperty.list_price)
        }}
        </div>
        <div class="text-xs text-muted-foreground align-bottom my-auto truncate overflow-hidden whitespace-nowrap">
          By
          {{ wholesaleProperty.listing_office }}
        </div>
      </div>
      <div class="address-container py-2 px-2 truncate overflow-hidden whitespace-nowrap">
        <span>{{ wholesaleProperty.geo_address }}</span>
      </div>
      <div class="flex flex-row p5-2 gap-2 px-2">
        <div class=""><span class="font-semibold">{{ wholesaleProperty.beds }}</span> bds</div>
        <div class="">|&nbsp; <span class="font-semibold"> {{ wholesaleProperty.baths }}</span>
          ba
        </div>
        <div class="">|&nbsp; <span class="font-semibold"> {{ formatNumber(wholesaleProperty.total_finished_sqft)
            }}</span> sqft
        </div>
      </div>
      <div class="grid grid-cols-1 pb-2 gap-2 px-2">
        <div class=""><span class="font-semibold"> {{ wholesaleProperty.structure_type }}</span></div>
      </div>
      <div class="grid grid-cols-3 justify-around bg-gray-300 dark:bg-gray-700 py-2 mt-2"
        v-if="wholesaleProperty.status != PropertyStatus.Closed">
        <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
          formatPrice(wholesaleProperty.seller_avg_rent) }}</span>Rent
        </div>
        <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
          formatPrice(wholesaleProperty.seller_avm) }}</span>AVM
        </div>
        <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
          formatPrice(wholesaleProperty.seller_arv) }}</span>ARV
        </div>
      </div>
      <div class="grid grid-cols-2 justify-around text-xs mt-1"
        v-if="wholesaleProperty.status != PropertyStatus.Closed">
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Est Cash Flow</div>
          <div class="text-center">{{ formatPrice(wholesaleProperty.seller_est_cashflow) }}</div>
        </div>
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Est Flip Profit</div>
          <div class="text-center">{{ formatPrice(wholesaleProperty.seller_est_flip_profit) }}</div>
        </div>
      </div>
      <Separator class="w-full my-2" v-if="wholesaleProperty.status == PropertyStatus.Closed" />
      <div class="grid grid-cols-2 justify-around text-xs mt-1"
        v-if="wholesaleProperty.status == PropertyStatus.Closed">
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Listed</div>
          <div class="text-center">{{
            formatPrice(wholesaleProperty.closed_price / wholesaleProperty.total_finished_sqft)
          }}/sqft
          </div>
        </div>
        <div class="grid grid-cols-1">
          <div class="text-center font-medium">Closed</div>
          <div class="text-center">{{
            formatPrice(wholesaleProperty.closed_price / wholesaleProperty.total_finished_sqft)
          }}/sqft
          </div>
        </div>
      </div>
      <div class="p-1 bg-transparent"></div>
    </div>
  </div>
  <ConfirmDialog :dialog="confirmDialog" />
  <Toast />
</template>

<style scoped>
.action-icon {
  width: 25px;
  height: 25px;
}

.wholesale-icon {
  color: orange;
  width: 18px;
  height: 18px;

}
</style>
