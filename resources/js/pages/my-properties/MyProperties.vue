<script setup lang="ts">

import {Head, router} from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import {Button} from "@/components/ui/button";
import {Icon} from "@iconify/vue";
import WholesaleAgreementDialog from "@/pages/my-properties/subs/WholesaleAgreementDialog.vue";
import { onMounted, ref} from "vue";
import {BreadcrumbItem} from "@/types";
import {WholesaleProperty} from "@/types/wholesale";
import WholesalePropertyThumb from "@/components/property-thumb/WholesalePropertyThumb.vue";
import {getPropertyList} from "@/lib/DBApiUtil";
import {DBApi, DBApiPropertyList} from "@/types/DBApi";
import PropertyDetailSheet from "@/components/property-detail-sheet/PropertyDetailSheet.vue";

// const page = usePage<SharedData>();
const props = defineProps<{
  list: WholesaleProperty[],
  agreed: boolean,
  success?: string | null,
  error?: string | null
}>();

const countProperties: number = props.list.length;

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My Properties',
    href: route('my_properties').toString(),
  },
];

const openAgreement = ref<boolean>(false);

const liveIds = ref<number[]>([]);

onMounted(() => {
  fetchWholesaleStatus();
})

const fetchWholesaleStatus = async () => {
  if (props.list?.length) {
    // Filter valid IDs only
    const propertyIds: number[] = props.list
        .filter(val => !!val.database_id) // Explicit boolean check
        .map(val => Number(val.database_id));

    if (propertyIds.length > 0) {
      try {
        const data: DBApi<DBApiPropertyList[]> = await getPropertyList({filter_ids: propertyIds});
        if (data?.error) {
          return;
        }
        if (data?.data) {
          liveIds.value = data.data.map((value) => Number(value.id));
        }
      } catch (error) {
        console.error('Unexpected fetch error:', error);
      }
    }
  }
}

const isLive = (id: number | null): boolean => {
  return id !== null && liveIds.value.includes(Number(id));
};

const onOpenAgreement = () => {
  openAgreement.value = true;
}

const onOpenAddProperty = () => {
  if (!props.agreed) {
    onOpenAgreement();
    return false;
  }
  router.visit(route('my_properties.upload_property'), {
    replace: false,
    preserveScroll: false,
  })
}

const onEditWholesale = (id: number) => {
  router.visit(route('my_properties.edit_property', id), {
    replace: false,
    preserveScroll: false,
  })
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="My Properties"/>
    <div class="px-4 py-2">
      <div class="top-section flex justify-between">
        <Button @click="onOpenAgreement()" class="border" variant="link">Review Agreement</Button>
        <Button @click="onOpenAddProperty()">
          <Icon icon="tabler:home-plus" class="size-5"/>
          Upload Property
        </Button>
      </div>
      <div class="mb-1 mt-4 font-semibold">
        <span v-if="countProperties">
           Your {{ countProperties }} Uploaded Properties
        </span>
        <span v-if="!countProperties">
         You haven't uploaded any properties yet.
        </span>
      </div>
    </div>
    <div
        class="px-4 py-2 h-[calc(100dvh-170px)] overflow-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
      <div v-for="(p, index) in props.list" :key="index" class="px-2 py-2">
        <WholesalePropertyThumb @click="onEditWholesale(p.id)" :is-live="isLive(p.database_id)" :wholesale-property="p"/>
      </div>
    </div>
    <WholesaleAgreementDialog v-model:open="openAgreement" :agreed="props.agreed"/>
    <PropertyDetailSheet/>
  </AppLayout>
</template>

<style scoped>

</style>
