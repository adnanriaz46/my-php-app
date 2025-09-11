<script setup lang="ts">


import {Head} from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import {BreadcrumbItem} from "@/types";
import {PropertyViewHistory} from "@/types/property";
import PageHeading from "@/components/ui/page-heading/PageHeading.vue";
import {Icon} from "@iconify/vue";


import {useDateFormat, useNumber, useTextFormat} from "@/composables/useFormat";
import CustomTooltip from "@/components/ui/custom-tooltip/CustomTooltip.vue";
//
// const route = useRoute();
// const {showToast} = useToast()
const {formatPhone} = useNumber()
const {formatDateTime} = useDateFormat()
const {formatUserAgent} = useTextFormat()

const props = defineProps<{
  id: number | string,
  data: PropertyViewHistory[] | null,
  count: number | null,
  agreed: boolean,
  success?: string | null,
  error?: string | null
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My Properties',
    href: route('my_properties').toString(),
  },
  {
    title: 'View History',
  },
];
</script>

<template>
  <!--  <Toast/>-->
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="View History - My Properties"/>
    <PageHeading :title="`View History ` + `(${props.count})`" sub-title="Most recent 100 views of property details."/>
    <div
        class="flex flex-col items-center md:items-start md:flex-row md:justify-center px-4 py-2 h-[calc(100dvh-150px)] overflow-auto gap-4 relative">
      <div class="grid grid-cols-1 gap-2 w-full">
        <div v-for="(d, index) in data" class="w-full max-w-xl mx-auto px-3 py-2 shadow-md border rounded-lg"
             :key="index">
          <div class="">{{ d.user?.name }}</div>
          <div class="flex justify-between">{{ d.user?.email }}
            <div class="text-muted-foreground flex">
              <CustomTooltip v-if="!d.is_mobile" text="Desktop">
                <Icon icon="solar:monitor-bold-duotone" class="size-6"/>
              </CustomTooltip>
              <CustomTooltip text="Mobile" v-else>
                <Icon icon="solar:iphone-line-duotone" class="size-6"/>
              </CustomTooltip>
              <CustomTooltip v-if="d.is_locked" text="User lacks access to view this property.">
                <Icon icon="solar:lock-keyhole-bold-duotone" class="size-6"/>
              </CustomTooltip>
            </div>
          </div>
          <div class="flex justify-between text-sm">{{ formatPhone(d.user?.phone_number) }}
            <div class="text-muted-foreground text-sm">At
              {{ formatDateTime(d.created_at) }}
            </div>
          </div>
          <div class="flex flex-col md:flex-row justify-between text-sm">{{ d.ip_city }} {{ d.ip_region }}
            {{ d.ip_country }}
            <div class="text-muted-foreground text-xs">
              {{ formatUserAgent(d.useragent) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>

</style>
