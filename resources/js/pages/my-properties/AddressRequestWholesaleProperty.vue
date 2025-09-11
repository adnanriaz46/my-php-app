<script setup lang="ts">

//
import {Head, router} from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import {BreadcrumbItem} from "@/types";
import PageHeading from "@/components/ui/page-heading/PageHeading.vue";
import {useToast} from "@/composables/useToast";
import {useDateFormat, useNumber} from "@/composables/useFormat";
import {AddressRequest} from "@/types/property";
import {Icon} from "@iconify/vue";
import CustomTooltip from "@/components/ui/custom-tooltip/CustomTooltip.vue";
import ConfirmDialog from "@/components/ui/confirm-dialog/ConfirmDialog.vue";
import {useConfirmDialog} from "@/composables/useConfirmDialog";
import axios from "axios";

const {showToast} = useToast()
const {formatPhone} = useNumber()
const {formatDateTime} = useDateFormat()

const props = defineProps<{
  id: number | string,
  data: AddressRequest[],
  count: number,
  agreed: boolean,
  success?: string | null,
  error?: string | null
}>();

const confirmDialog = useConfirmDialog()

const onHandlingConfirmation = async (message: string, type: string, id: number, btnText: string = 'Confirm') => {
  const confirmed = await confirmDialog.openConfirm(message, btnText)
  if (confirmed) {
    if (type == 'approved' || type == 'rejected') {
      try {
        const response = await axios.post(route('my_properties.address_requests_process'), {id: id, type: type, wholesale_id: props.id})
        if (response.data?.error) {
          showToast('Address Request Approval', response.data?.error, 'error')
          return;
        }
        router.visit(route('my_properties.address_requests', props.id), {
          replace: true,
          preserveScroll: true,
        })
        showToast('Address Request Approval', response.data?.success ?? 'Request has been processed', 'success');
      } catch (err) {
        showToast('Address Request Approval', 'Error: ' + err?.toString(), 'error')
      } finally {

      }
    }
  } else {
    console.log("Cancelled")
  }
}


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My Properties',
    href: route('my_properties').toString(),
  },
  {
    title: 'Address Requests',
  },
];

const getBorder = (status: number) => {
  let classSet = `border border-primary`
  if (status == 2) {
    classSet = `border border-red-800`
  } else if (status == 1) {
    classSet = `border border-green-800`
  }

  return classSet;
}

</script>

<template>
  <!--  <Toast/>-->
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Upload Property - My Properties"/>
    <PageHeading :title="`Address Requests (${props.count})`" sub-title="All address requests by users"/>
    <div
        class="flex flex-col items-center md:items-start md:flex-row md:justify-center px-4 py-2 h-[calc(100dvh-150px)] overflow-auto gap-4 relative">
      <div class="grid grid-cols-1 gap-2 w-full">
        <div v-for="(d, index) in data" class="w-full max-w-xl mx-auto px-3 py-2 shadow-md border rounded-lg "
             :class="getBorder(d.is_agreed)"
             :key="index">
          <div class="flex justify-between">{{ d.user?.name }}
            <div class="flex">
              <div class="px-2 py-1 text-xs bg-primary-strong flex gap-1 rounded-lg" v-if="!d.is_agreed">
                <Icon icon="tabler:circle-dashed" class="size-4"/>
                Pending
              </div>
              <div class="px-2 py-1 text-xs  bg-green-800 flex gap-1 rounded-lg text-white"
                   v-else-if="d.is_agreed == 1">
                <Icon icon="tabler:circle-check" class="size-4"/>
                Approved
              </div>
              <div class="px-2 py-1 text-xs bg-red-800 flex gap-1 rounded-lg text-white" v-else>
                <Icon icon="tabler:circle-minus" class="size-4"/>
                Rejected
              </div>
            </div>
          </div>
          <div class="flex justify-between">
            <div class="">
              <div class="">{{ d.user?.email }}</div>
              <div class="">{{ formatPhone(d.user?.phone_number) }}</div>
            </div>
            <div class="flex gap-1 my-auto">
              <CustomTooltip text="Approve">
                <div
                    @click="onHandlingConfirmation('Are you sure you want to accept all requests for this property made by this user?' , 'approved', d.id, 'Approve')"
                    class=" cursor-pointer px-1.5 py-1.5 text-white bg-green-800 rounded-full">
                  <Icon icon="tabler:circle-dashed-check" class="size-6"/>
                </div>
              </CustomTooltip>
              <CustomTooltip text="Reject">
                <div
                    @click="onHandlingConfirmation('Are you sure you want to reject all requests for this property made by this user?' , 'rejected', d.id, 'Reject')"
                    class=" cursor-pointer px-1.5 py-1.5 text-white bg-red-800 rounded-full">
                  <Icon icon="tabler:ban" class="size-6"/>
                </div>
              </CustomTooltip>
            </div>
          </div>
          <div class="flex text-sm">
            <div class="text-muted-foreground text-sm">At
              {{ formatDateTime(d.created_at) }}
            </div>
          </div>
          <div class="flex flex-col md:flex-row justify-between text-sm">
            <Icon icon=""/>
          </div>
        </div>
      </div>
    </div>
    <ConfirmDialog :dialog="confirmDialog"/>
  </AppLayout>

</template>

<style scoped>

</style>
