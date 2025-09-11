<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { ref, watch, computed } from 'vue'
import { useVModel } from '@vueuse/core'
import { Icon } from '@iconify/vue'
import {
  ComboboxRoot, ComboboxAnchor, ComboboxTrigger, ComboboxInput,
  ComboboxContent, ComboboxGroup, ComboboxItem, ComboboxItemIndicator,
  ComboboxViewport, TagsInputRoot, TagsInputItem, TagsInputItemText,
  TagsInputItemDelete, TagsInputInput, useFilter
} from 'reka-ui'

const { contains } = useFilter({ sensitivity: 'base' })

const props = defineProps<{
  optionValues: string[] // You can change to array of objects if needed
  modelValue?: string[]
  class?: HTMLAttributes['class'],
  tagFontSize?: number | string | null | undefined,
  allowCustom?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string[]): void
}>()

const values = useVModel(props, 'modelValue', emit, { passive: true })

const query = ref('')

const filteredOptions = ref<string[]>([]);

// const filteredOptions = computed(() => {
//   let options = props.optionValues || [];

//   // Filter existing options by query.value 
//   const filteredExistingOptions = options.filter(
//     (option) =>
//       contains(option, query.value) &&
//       !values.value?.includes(option)
//   );

//   // If allowCustom is true and there's a query, add the query value
//   // but only if it's not already in the existing options and not already selected
//   if (props.allowCustom && query.value &&
//     !options.includes(query.value) &&
//     !values.value?.includes(query.value)) {
//     return [...filteredExistingOptions, query.value];
//   }

//   console.log(options, filteredExistingOptions);

//   return filteredExistingOptions;
// });

watch([() => props.optionValues, query, values], () => {
  let options = [...(props.optionValues || [])];

  if (props.allowCustom && query.value && !options.includes(query.value)) {
    options.push(query.value);
  }

  filteredOptions.value = options.filter(option =>
    contains(option, query.value) && !values.value?.includes(option)
  );
}, { immediate: true });


watch(values, () => {
  query.value = ''
}, { deep: true })
</script>

<template>
  <ComboboxRoot v-model="values" multiple :class="['my-1 relative', props.class]" :ignore-filter="true">
    <ComboboxTrigger class="w-full">
      <ComboboxAnchor
        class="w-full inline-flex items-center justify-between rounded-lg p-2 text-[13px] leading-none gap-[5px] bg-white text-grass11 shadow hover:bg-mauve3 focus:shadow focus:outline-none border dark:bg-input/30 border-input">

        <TagsInputRoot v-model="values" delimiter="" class="flex gap-2 items-center rounded-lg flex-wrap">
          <TagsInputItem v-for="item in values" :key="item" :value="item"
            class="flex items-center justify-center gap-2 text-white bg-primary rounded-lg px-2 py-1">
            <TagsInputItemText :class="tagFontSize ? `text-[${tagFontSize}px]` : 'text-sm'" />
            <TagsInputItemDelete>
              <Icon icon="lucide:x" />
            </TagsInputItemDelete>
          </TagsInputItem>

          <ComboboxInput v-model="query" as-child class="dark:bg-input/30">
            <TagsInputInput placeholder="Search..."
              class="focus:outline-none flex-1 rounded bg-transparent placeholder:text-mauve10 px-1"
              @keydown.enter.prevent />
          </ComboboxInput>
        </TagsInputRoot>
        <Icon icon="radix-icons:chevron-down" class="h-4 w-4 min-w-[15px] max-w-[15px]" />
      </ComboboxAnchor>
    </ComboboxTrigger>
    <ComboboxContent
      class="absolute z-20 w-full mt-2 bg-white dark:bg-input border overflow-hidden rounded-lg shadow max-h-32">
      <ComboboxViewport class="p-[5px]">
        <ComboboxGroup v-if="filteredOptions?.length">
          <ComboboxItem v-for="(option, index) in filteredOptions" :key="index" :value="option"
            class="text-[13px] leading-none text-black dark:text-white rounded-md flex items-center h-[25px] pr-[35px] pl-[25px] relative select-none data-[highlighted]:bg-primary">
            <ComboboxItemIndicator class="absolute left-0 w-[25px] inline-flex items-center justify-center">
              <Icon icon="radix-icons:check" />
            </ComboboxItemIndicator>
            <span>{{ option }}</span>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxViewport>
    </ComboboxContent>

  </ComboboxRoot>
</template>
