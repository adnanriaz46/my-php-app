<script setup lang="ts">
import {DBApiPropertyList} from "@/types/DBApi";
import PropertyThumb from "@/components/property-thumb/PropertyThumb.vue";
import {addPropertyIdToUrl} from "@/lib/zilowAndlocationUtil";
import {computed} from "vue";
import {getUser, isCountySubscribedUser, isFullAccessUser} from "@/composables/useUser";
import {upgradeDialog} from "@/stores/DialogStore";

const props = defineProps<{ property: DBApiPropertyList }>()

const lockedMap: boolean = computed(() => {
  const isFull = isFullAccessUser();
  return isFull
      ? false
      : !isCountySubscribedUser(props.property.county_state);
});

const openPropertyDetail = (propertyId: string, locked: boolean) => {
  if (locked) {
    openUpgrade();
    return false;
  }
  addPropertyIdToUrl(propertyId);
  return true;
}

const openUpgrade = () => {
  upgradeDialog.user = getUser();
  upgradeDialog.upgradeDialogOpen = true;
}

</script>

<template>
  <PropertyThumb :is-map="true" :locked="lockedMap" @click="openPropertyDetail(props.property.id, lockedMap)"
                 :property-list-data="props.property"></PropertyThumb>
</template>
<style scoped>

</style>
