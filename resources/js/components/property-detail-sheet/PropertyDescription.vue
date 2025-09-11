<script setup lang="ts">
import {ref, computed} from "vue";

const isExpanded = ref(false);

const props = defineProps({
  propertyDescription: String
});

const isTruncatable = computed(() => props.propertyDescription?.length > 150);

const truncatedText = computed(() =>
    isTruncatable.value
        ? props.propertyDescription.slice(0, 150) + '...'
        : props.propertyDescription
);
</script>

<template>
  <div >
    {{ isExpanded ? propertyDescription : truncatedText }}
    <button
        v-if="isTruncatable"
        @click="isExpanded = !isExpanded"
        class="text-primary-strong ml-2 underline cursor-pointer"
    >
      {{ isExpanded ? 'Show less' : 'Show more' }}
    </button>
  </div>
</template>

<style scoped>

</style>
