<script setup lang="ts">

import InputError from "@/components/InputError.vue";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter, DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Input} from "@/components/ui/input";
import {Label} from "@/components/ui/label";
import {useForm, usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref, watch} from "vue";
import {SwitchRoot, SwitchThumb} from "reka-ui";
import SelectBox from "@/components/ui/select-box/SelectBox.vue";
import {getMyUnlistedList} from "@/lib/myListUtil";
import {useToast} from '@/composables/useToast'
import {UnlistedSavedListTypes, ZillowPListingMain} from "@/lib/zilowAndlocationUtil";

const {showToast} = useToast()

const page = usePage();

watch(() => page.props?.success, (msg) => {
  if (msg) {
    showToast('My List', msg, 'success')
  }
}, {deep: true});

const props = defineProps<{
  zillowData: ZillowPListingMain;
  open: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const dialogOpen = ref<boolean>(props.open);

const addAsNew = ref<boolean>(true);

const myPropertyList = ref<{ key: number, value: string }[]>([])

// Sync child state back to parent
watch(dialogOpen, (newVal) => {
  emit('update:open', newVal);
});

// Sync prop changes to local state
watch(() => props.open, (newVal) => {
  dialogOpen.value = newVal;
});
onMounted(() => {
  fetchInitialData();
})
const fetchInitialData = async () => {
  const res = await getMyUnlistedList();
  if (res?.error) {
    // alert(res.error)
    return;
  }

  myPropertyList.value = res.data?.map(value => {
    return {key: value.id, value: value.name + ` (${value.addresses.length})`}
  });
}

const formZillowData: UnlistedSavedListTypes = computed(() => {
  return {
    zpid: props.zillowData?.zpid,
    list_price: props.zillowData?.price,
    address: props.zillowData?.streetAddress + ", " + props.zillowData?.city + ", " + props.zillowData?.state + ' ' + props.zillowData?.zipcode,
    beds: props.zillowData?.bedrooms,
    baths: props.zillowData?.bathrooms,
    sqft: props.zillowData?.livingArea,
    image: props.zillowData?.imgSrc,
    lat: props.zillowData?.latitude,
    lng: props.zillowData?.longitude,
    zEstimateRent: props.zillowData?.rentZestimate,
    zEstimate: props.zillowData?.zestimate,
    propertyType: props.zillowData?.propertyTypeDimension,
  }
})

const updateForm = useForm({
  zillowData: formZillowData,
  recordId: null,
});
const createForm = useForm({
  zillowData: formZillowData,
  name: '',
});

const submit = () => {
  if (!addAsNew.value) {
    updateForm.zillowData = formZillowData;
    updateForm.post(route('property.my_list.update_unlisted'), {
      onSuccess: () => {
        updateForm.reset()
        fetchInitialData();
        dialogOpen.value = false;
      },
    });
  } else {
    createForm.zillowData = formZillowData;
    createForm.post(route('property.my_list.create_unlisted'), {
      onSuccess: () => {
        createForm.reset()
        fetchInitialData();
        dialogOpen.value = false;
      },
    });
  }
};

</script>
<template>
  <Dialog v-model:open="dialogOpen">
    <DialogContent>
      <DialogHeader class="space-y-3">
        <DialogTitle>Add to My List</DialogTitle>
        <DialogDescription>
          <InputError v-if="updateForm.errors.zillowData" :message="updateForm.errors.zillowData"/>
          <InputError v-if="!props.zillowData" :message="`Empty or invalid data`"/>
        </DialogDescription>
      </DialogHeader>
      <div class="w-full flex justify-center">
        <div class="flex-1 flex justify-center items-center" @click="addAsNew = false">
          <Label class="text-center py-1 px-2" :class="!addAsNew ? `bg-primary text-black rounded-lg`: '' ">
            Add to List
          </Label>
        </div>
        <SwitchRoot
            id="airplane-mode"
            v-model="addAsNew"
            class="w-[32px] h-[20px] shadow-sm flex data-[state=unchecked]:bg-stone-300 data-[state=checked]:bg-stone-800 dark:data-[state=unchecked]:bg-stone-800 dark:data-[state=checked]:bg-stone-700 border border-stone-300 data-[state=checked]:border-stone-700  dark:border-stone-700 rounded-full relative transition-[background] focus-within:outline-none focus-within:shadow-[0_0_0_1px] focus-within:border-stone-800 focus-within:shadow-stone-800"
        >
          <SwitchThumb
              class="w-3.5 h-3.5 my-auto bg-white text-xs flex items-center justify-center shadow-xl rounded-full transition-transform translate-x-0.5 will-change-transform data-[state=checked]:translate-x-full"
          />
        </SwitchRoot>
        <div class="flex-1 flex justify-center items-center" @click="addAsNew = true">
          <Label class="text-center py-1 px-2" :class="addAsNew ? `bg-primary text-black rounded-lg`: '' ">
            Create New List
          </Label>
        </div>
      </div>
      <div class="grid gap-2" v-if="addAsNew">
        <Label for="create-name">Enter list name</Label>
        <Input id="create-name" v-model="createForm.name"
               placeholder="List Name"/>
        <InputError :message="createForm.errors.name"/>
      </div>

      <div class="grid gap-2" v-if="!addAsNew">
        <Label for="create-name">Choose the list</Label>
        <SelectBox :options="myPropertyList" v-model="updateForm.recordId"/>

        <InputError :message="updateForm.errors.recordId"/>
      </div>

      <DialogFooter class="gap-2">
        <DialogClose as-child>
          <Button variant="secondary"> Cancel</Button>
        </DialogClose>

        <Button v-if="props.zillowData" @click="submit" variant="default"
                :disabled="updateForm.processing || createForm.processing">
          Save
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped>

</style>
