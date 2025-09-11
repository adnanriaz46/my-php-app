<script setup lang="ts">

import { Head, router, useForm } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";

import { computed, onMounted, onUnmounted, onUpdated, ref, watch } from "vue";
import { BreadcrumbItem, SharedData, User } from "@/types";

import PageHeading from "@/components/ui/page-heading/PageHeading.vue";
import SelectBox from "@/components/ui/select-box/SelectBox.vue";
import { Icon } from '@iconify/vue';
import ConfirmDialog from "@/components/ui/confirm-dialog/ConfirmDialog.vue";
import { useConfirmDialog } from "@/composables/useConfirmDialog";
import axios from "axios";
import Toast from "@/components/ui/toast/Toast.vue";
import { useToast } from "@/composables/useToast";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import InputError from "@/components/InputError.vue";
import { UnlistedSavedListTypes } from "@/lib/zilowAndlocationUtil";
import { MyUnlistedList } from "@/types/myList";
import UnlistedPropertyThumb from "@/components/property-thumb/UnlistedPropertyThumb.vue";
import UnlistedPropertySheet from "@/components/unlisted-property-sheet/UnlistedPropertySheet.vue";

const { showToast } = useToast()

const props = defineProps<{
  list: MyUnlistedList[],
  user: User,
  success?: string | null,
  error?: string | null
}>();

const myLists = computed(() =>
  props.list.map((val) => ({
    key: val.id,
    value: val.name + ` (${val.addresses.length})`,
  }))
);

const selectBoxKey = ref<number>(1);
const selectedListId = ref<number | null>(null);
const selectedMyList = ref<MyUnlistedList>();

const selectedPropertyAddresses = ref<UnlistedSavedListTypes[]>([]);
const propertiesLoading = ref<boolean>(true);

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My List - Unlisted Properties',
    href: route('my_list.unlisted').toString(),
  },
];

onMounted(() => {
  loadList();
  propertiesLoading.value = false;
})

onUpdated(() => {
  loadList();
  propertiesLoading.value = false;
})

onUnmounted(() => {
  propertiesLoading.value = true;
})

const editForm = useForm({
  id: selectedListId.value,
  name: '',
});

watch(selectedListId, () => {
  loadList();
})


const loadList = async () => {
  selectedMyList.value = props.list.find((v) => v.id == selectedListId.value);
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
        const response = await axios.post(route('my_list.unlisted.delete'), { id: id })
        if (response.data?.error) {
          showToast('My List Unlisted', response.data?.error, 'error')
          return;
        }
        router.visit(route('my_list.unlisted'), {
          replace: true,
          preserveScroll: true,
        })
        showToast('My List Unlisted', response.data?.success ?? 'List has been deleted', 'success');
      } catch (err) {
        showToast('My List Unlisted', 'Error: ' + err?.toString(), 'error')
      } finally {

      }
    } else if (type == 'update-property-ids') {
      try {
        const response = await axios.post(route('my_list.unlisted.update_addresses'), { id: selectedListId.value, addresses: selectedPropertyAddresses.value })
        if (response.data?.success) {
          await router.reload()
          showToast('My List Unlisted', response.data.success, 'success');
        }
      } catch (err: any) {
        showToast('My List Unlisted', err?.message ?? err ?? 'Something went wrong!', 'error');
      } finally {

      }
    }
  }
}

const onEditSubmit = async () => {
  editForm.post(route('my_list.unlisted.update'), {
    preserveScroll: true,
    onSuccess: (d: SharedData | any) => {
      editNameDialogOpen.value = false;
      showToast('My List Unlisted', d.props?.success ?? "Your list has been updated!", 'success')
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

    <Head title="My List Unlisted" />
    <PageHeading title="My List - Unlisted Properties" />
    <div class="px-4 py-2 flex-col items-start justify-start">
      <div class="top-section flex my-auto">
        <SelectBox :key="selectBoxKey" class="mt-1" placeholder="Choose a list" v-model="selectedListId"
          v-model:options="myLists" :not-allow-empty-in-list="true" />
        <div class="flex my-auto px-2 gap-2" v-if="selectedListId">
          <Icon @click.stop="editNameDialogOpen = true" as="button"
            class="flex-1 size-8 p-1 hover:text-primary cursor-pointer" icon="solar:pen-new-square-linear" />
          <Icon @click.stop="onHandlingConfirmation('Do you want to delete the list?', 'delete', selectedListId)"
            as="button" class="flex-1 size-8 p-1 hover:text-primary cursor-pointer" icon="solar:trash-bin-2-linear" />
        </div>
      </div>
      <div class="mb-1 mt-4 font-semibold flex justify-between" v-if="!propertiesLoading">
        <div class="my-auto" v-if="selectedMyList?.addresses?.length">
          {{ selectedMyList?.addresses?.length }} Unlisted Properties Found.
        </div>
        <div class="my-auto" v-else>
          <span v-if="!myLists.length">You haven't added any unlisted properties yet.</span>
          <span v-else>Choose a list to view unlisted properties.</span>
        </div>
        <div v-if="selectedPropertyAddresses?.length"
          class="pl-2 bg-primary text-black flex flex-shrink gap-1 items-center rounded-lg shadow-md">
          <span class="">{{ selectedPropertyAddresses?.length }}</span>
          <Icon class="size-8 p-1 hover:bg-gray-600 rounded-lg" icon="tabler:trash-x"
            @click.stop="onHandlingConfirmation('Do you want to remove properties from your list?', 'update-property-ids')" />
        </div>
      </div>
      <div v-else class="h-[30px] animate-pulse bg-input rounded-lg"></div>
    </div>
    <div
      class="px-4 py-2 h-[calc(100dvh-230px)] overflow-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
      <template v-if="!propertiesLoading">
        <div v-for="(p, index) in selectedMyList?.addresses" :key="index" class="px-2 py-2">
          <UnlistedPropertyThumb v-model:selection-addresses="selectedPropertyAddresses" :selection-show="true"
            :address="p" />
        </div>
      </template>
    </div>
    <ConfirmDialog :dialog="confirmDialog" />

    <Dialog v-model:open="editNameDialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Edit My List</DialogTitle>
        </DialogHeader>
        <div class="w-full">
          <Label :line-break="true">
            <Input :default-value="selectedMyList?.name" v-model="editForm.name" placeholder="Enter New List Name" />
            <InputError :message="editForm.errors.name" />
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

    <Toast />
    <UnlistedPropertySheet />
  </AppLayout>
</template>

<style scoped></style>
