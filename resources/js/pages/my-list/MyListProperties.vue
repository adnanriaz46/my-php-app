<script setup lang="ts">

import {Head, router, useForm} from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";

import {computed, onUpdated, ref, watch} from "vue";
import {BreadcrumbItem, User} from "@/types";

import {getPropertyList} from "@/lib/DBApiUtil";
import {DBApi, DBApiPropertyList} from "@/types/DBApi";
import PropertyDetailSheet from "@/components/property-detail-sheet/PropertyDetailSheet.vue";
import {MyPropertyList} from "@/types/myList";
import PageHeading from "@/components/ui/page-heading/PageHeading.vue";
import SelectBox from "@/components/ui/select-box/SelectBox.vue";
import PropertyThumb from "@/components/property-thumb/PropertyThumb.vue";
import PropertyThumbLoading from "@/components/property-thumb/PropertyThumbLoading.vue";
import {Icon} from '@iconify/vue';
import ConfirmDialog from "@/components/ui/confirm-dialog/ConfirmDialog.vue";
import {useConfirmDialog} from "@/composables/useConfirmDialog";
import axios from "axios";
import Toast from "@/components/ui/toast/Toast.vue";
import {useToast} from "@/composables/useToast";
import {Button} from "@/components/ui/button";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle
} from "@/components/ui/dialog";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import InputError from "@/components/InputError.vue";
import {addPropertyIdToUrl} from "@/lib/zilowAndlocationUtil";

const {showToast} = useToast()

const props = defineProps<{
  list: MyPropertyList[],
  user: User,
  success?: string | null,
  error?: string | null
}>();

const myLists = computed(() =>
    props.list.map((val) => ({
      key: val.id,
      value: val.name + ` (${val.property_ids.length})`,
    }))
);

const selectBoxKey = ref<number>(1);
const selectedListId = ref<number>(null);
const selectedMyList = ref<MyPropertyList>();

const selectedPropertyIds = ref<number[]>([]);
const propertiesLoading = ref<boolean>();
const properties = ref<DBApiPropertyList[]>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My List - Properties',
    href: route('my_list.properties').toString(),
  },
];

onUpdated(() => {
  loadList();
})


const fetchMyListProperties = async () => {
  properties.value = [];
  selectedPropertyIds.value = [];
  if (!selectedListId.value) return;
  selectedMyList.value = props.list.find((v) => v.id == selectedListId.value);
  const propertyIds: number[] = selectedMyList.value?.property_ids || [];
  if (!propertyIds.length) return;
  propertiesLoading.value = true;
  try {
    const listData: DBApi<DBApiPropertyList[]> = await getPropertyList({filter_ids: propertyIds});
    if (listData?.error) return;
    if (listData?.data) {
      properties.value = listData.data;
    }
  } catch (error) {
    console.error('Unexpected fetch error:', error);
  } finally {
    propertiesLoading.value = false;
    selectBoxKey.value++;
  }
};

const editForm = useForm({
  id: selectedListId.value,
  name: '',
});

watch(selectedListId, () => {
  loadList();
})


const loadList = async () => {
  await fetchMyListProperties();
  editForm.id = selectedMyList.value?.id;
  editForm.name = selectedMyList.value?.name;
}

const editNameDialogOpen = ref<boolean>(false);
const confirmDialog = useConfirmDialog()
const onHandlingConfirmation = async (message: string, type: string, id?: number) => {
  const confirmed = await confirmDialog.openConfirm(message, 'Delete')
  if (confirmed) {
    if (type == 'delete') {
      try {
        const response = await axios.post(route('my_list.properties.delete'), {id: id})
        if (response.data?.error) {
          showToast('Delete Property List', response.data?.error, 'error')
          return;
        }
        router.visit(route('my_list.properties'), {
          replace: true,
          preserveScroll: true,
        })
        showToast('Delete Property List', response.data?.success ?? 'List has been deleted', 'success');
      } catch (err) {
        showToast('Delete Property List', 'Error: ' + err?.toString(), 'error')
      } finally {

      }
    } else if (type == 'update-property-ids') {
      try {
        const response = await axios.post(route('my_list.properties.update_property_ids'), {id: selectedListId.value, property_ids: selectedPropertyIds.value})
        if (response.data?.success) {
          await router.reload()
          showToast('My List Properties', response.data.success, 'success');
        }
      } catch (err) {
        showToast('My List Properties', err?.message ?? err ?? 'Something went wrong!', 'error');
      } finally {

      }
    }
  }
}

const onClickThumb = (id: number) => {
  addPropertyIdToUrl(id)
}

const onEditSubmit = async () => {
  editForm.post(route('my_list.properties.update'), {
    preserveScroll: true,
    onSuccess: (d: ShareData) => {
      editNameDialogOpen.value = false;
      showToast('My List Properties', d.props?.success ?? "Your list has been updated!", 'success')
      selectBoxKey.value++;
    },
    onError: (errors) => {
      console.error('Validation Errors:', errors)
    }
  })
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="My List Properties"/>
    <PageHeading title="My List - Properties"/>
    <div class="px-4 py-2 flex-col items-start justify-start">
      <div class="top-section flex my-auto">
        <SelectBox
            :key="selectBoxKey"
            class="mt-1" placeholder="Choose a list" v-model="selectedListId" v-model:options="myLists"
            :not-allow-empty-in-list="true"/>
        <div class="flex my-auto px-2 gap-2" v-if="selectedListId">
          <Icon @click.stop="editNameDialogOpen = true" as="button" class="flex-1 size-8 p-1 hover:text-primary cursor-pointer"
                icon="solar:pen-new-square-linear"/>
          <Icon @click.stop="onHandlingConfirmation('Do you want to delete the list?', 'delete', selectedListId)"
                as="button" class="flex-1 size-8 p-1 hover:text-primary cursor-pointer"
                icon="solar:trash-bin-2-linear"/>
        </div>
      </div>
      <div class="mb-1 mt-4 font-semibold flex justify-between" v-if="!propertiesLoading">
        <div class="my-auto" v-if="properties?.length">
          {{ properties?.length }} Properties Found.
        </div>
        <div class="my-auto" v-else>
          You haven't add any properties yet.
        </div>
        <div v-if="selectedPropertyIds.length"
             class="pl-2 bg-primary text-black flex flex-shrink gap-1 items-center rounded-lg shadow-md">
          <span class="">{{ selectedPropertyIds.length }}</span>
          <Icon class="size-8 p-1 hover:bg-gray-600 rounded-lg" icon="tabler:trash-x"
                @click.stop="onHandlingConfirmation('Do you want to remove properties from your list?', 'update-property-ids')"/>
        </div>
      </div>
      <div v-else class="h-[30px] animate-pulse bg-input rounded-lg"></div>
    </div>
    <div
        class="px-4 py-2 h-[calc(100dvh-230px)] overflow-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
      <template v-if="!propertiesLoading">
        <div v-for="(p, index) in properties" :key="index" class="px-2 py-2">
          <PropertyThumb class="border" @click="onClickThumb(p.id)" :property-list-data="p" :show-selection="true"
                         v-model:selection-ids="selectedPropertyIds"/>
        </div>
      </template>
      <template v-else>
        <PropertyThumbLoading/>
        <PropertyThumbLoading/>
        <PropertyThumbLoading/>
        <PropertyThumbLoading/>
        <PropertyThumbLoading/>
        <PropertyThumbLoading/>
      </template>
    </div>
    <ConfirmDialog :dialog="confirmDialog"/>

    <Dialog v-model:open="editNameDialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Edit My List</DialogTitle>
        </DialogHeader>
        <div class="w-full">
          <Label :line-break="true">
            <Input :default-value="selectedMyList?.name" v-model="editForm.name" placeholder="Enter New List Name"/>
            <InputError :message="editForm.errors.name"/>
          </Label>
        </div>
        <DialogFooter class="gap-2">
          <DialogClose as-child>
            <Button variant="secondary"> Cancel</Button>
          </DialogClose>
          <Button variant="default" @click="onEditSubmit()" :disabled="editForm.processing">
            Update
          </Button>
        </DialogFooter>

      </DialogContent>
    </Dialog>

    <Toast/>
    <PropertyDetailSheet/>
  </AppLayout>
</template>

<style scoped>

</style>
