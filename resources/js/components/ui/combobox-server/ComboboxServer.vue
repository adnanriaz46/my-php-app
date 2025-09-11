combobox
<script setup lang="ts">
import {ref, watch} from 'vue'
import {useVModel} from '@vueuse/core'
import {Icon} from '@iconify/vue'
import {
  ComboboxRoot, ComboboxAnchor, ComboboxTrigger, ComboboxInput,
  ComboboxContent, ComboboxGroup, ComboboxItem, ComboboxItemIndicator,
  ComboboxViewport, TagsInputRoot, TagsInputItem, TagsInputItemText,
  TagsInputItemDelete, TagsInputInput
} from 'reka-ui'
import axios from "axios";

const props = defineProps<{
  modelValue?: string[]
  class?: string
  tagFontSize?: number | string | null | undefined
  route: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string[]): void
}>()

const values = useVModel(props, 'modelValue', emit, {passive: true})

const query = ref('')
const options = ref<string[]>([])
const loading = ref(false)

async function fetchOptions(search: string) {
  loading.value = true
  try {
    const response = await axios.get(props.route, {
      params: {s: search}
    })

    options.value = Array.isArray(response.data) ? response.data : []
  } catch (error) {
    console.error('API fetch error:', error)
    options.value = []
  } finally {
    loading.value = false
  }
}

watch(query, (newQuery) => {
  if (newQuery.length >= 1) {
    fetchOptions(newQuery)
  } else {
    options.value = []
  }
})

watch(values, () => {
  query.value = ''
}, {deep: true})

</script>

<template>
  <ComboboxRoot
      v-model="values"
      multiple
      ignore-filter
      :class="['my-1 relative', props.class]"
  >
    <ComboboxTrigger class="w-full">
      <ComboboxAnchor
          class="w-full inline-flex items-center justify-between rounded-lg p-2 text-[13px] leading-none gap-[5px] bg-white text-grass11 shadow hover:bg-mauve3 focus:shadow focus:outline-none border dark:bg-input/30"
      >
        <TagsInputRoot v-model="values" delimiter="" class="flex gap-2 items-center rounded-lg flex-wrap">
          <TagsInputItem
              v-for="item in values"
              :key="item"
              :value="item"
              class="flex items-center justify-center gap-2 text-white bg-primary rounded-lg px-2 py-1"
          >
            <TagsInputItemText :class="tagFontSize ? `text-[${tagFontSize}px]` : 'text-sm'"/>
            <TagsInputItemDelete>
              <Icon icon="lucide:x"/>
            </TagsInputItemDelete>
          </TagsInputItem>
          <ComboboxInput v-model="query" as-child class="dark:bg-input/30">
            <TagsInputInput
                placeholder="Search..."
                class="focus:outline-none flex-1 rounded bg-transparent placeholder:text-mauve10 px-1"
                @keydown.enter.prevent
            />
          </ComboboxInput>
        </TagsInputRoot>
        <Icon
            icon="radix-icons:chevron-down"
            class="h-4 w-4 min-w-[15px] max-w-[15px]"
        />
      </ComboboxAnchor>
    </ComboboxTrigger>
    <ComboboxContent
        class="absolute z-20 w-full mt-2 bg-white dark:bg-input border overflow-hidden rounded-lg shadow max-h-32"
    >
      <ComboboxViewport class="p-[5px]">
        <div v-if="loading" class="text-sm text-mauve10 p-2">Loading...</div>
        <div v-else-if="options.length === 0 && query.length >= 1" class="text-sm text-mauve10 p-2">No results</div>
        <ComboboxGroup v-else>
          <ComboboxItem
              v-for="(option, index) in options"
              :key="index"
              :value="option"
              class="text-[13px] leading-none text-black dark:text-white rounded-md flex items-center h-[25px] pr-[35px] pl-[25px] relative select-none data-[highlighted]:bg-primary"
          >
            <ComboboxItemIndicator class="absolute left-0 w-[25px] inline-flex items-center justify-center">
              <Icon icon="radix-icons:check"/>
            </ComboboxItemIndicator>
            <span>{{ option }}</span>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxViewport>
    </ComboboxContent>
  </ComboboxRoot>
</template>
