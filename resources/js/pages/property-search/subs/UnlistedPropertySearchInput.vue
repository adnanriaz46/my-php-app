<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { Icon } from '@iconify/vue'
import Input from '@/components/ui/input/Input.vue'
import {
  getPlaceAutocomplete,
  PlacePredictionSuggestion,
  addUnlistedIdToUrl
} from "@/lib/zilowAndlocationUtil";

const loading = ref(false);
const searchQuery = ref('')
const error = ref<string>('')
const isFocused = ref(false)
const suggestions = ref<PlacePredictionSuggestion[]>([])
const highlightedIndex = ref<number>(-1)
const itemRefs = ref<HTMLElement[]>([])

// Debounced API call
const fetchPlaceAddressSuggestions = useDebounceFn(
  async () => {
    error.value = '';
    if (!searchQuery.value) return;
    loading.value = true
    const data = await getPlaceAutocomplete(searchQuery.value);

    if (data) {
      isFocused.value = true
      suggestions.value = data.predictions.map((prediction: PlacePredictionSuggestion) => prediction);
    }

    loading.value = false;
  }
);

const updateSuggestions = () => {
  fetchPlaceAddressSuggestions()
}

const selectSuggestion = async (item: string) => {
  addUnlistedIdToUrl(item);
  searchQuery.value = '';
  suggestions.value = [];
  isFocused.value = false
}

const handleBlur = () => {
  setTimeout(() => (isFocused.value = false), 250)
}

const filteredSuggestions = computed(() => suggestions.value) // no longer filtering locally

watch(
  () => suggestions.value.length,
  () => {
    highlightedIndex.value = -1
    itemRefs.value = []
  }
)

watch(highlightedIndex, async (newIndex) => {
  await nextTick()
  const el = itemRefs.value[newIndex]
  if (el) el.scrollIntoView({ block: 'nearest' })
})

const onArrowDown = () => {
  if (!isFocused.value) isFocused.value = true
  const total = filteredSuggestions.value.length
  if (!total) return
  highlightedIndex.value = (highlightedIndex.value + 1) % total
}

const onArrowUp = () => {
  if (!isFocused.value) isFocused.value = true
  const total = filteredSuggestions.value.length
  if (!total) return
  highlightedIndex.value = highlightedIndex.value <= 0 ? total - 1 : highlightedIndex.value - 1
}

const onEnter = () => {
  if (isFocused.value && filteredSuggestions.value.length && highlightedIndex.value >= 0) {
    const choice = filteredSuggestions.value[highlightedIndex.value]
    if (choice?.description) selectSuggestion(choice.description)
  }
}

const onEscape = () => {
  isFocused.value = false
}
</script>
<template>
  <div class="relative w-full max-w-sm">
    <!-- Search icon -->
    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <Icon icon="tabler:search" class="text-gray-400 w-5 h-5" />
    </span>

    <!-- Search input -->
    <Input
      v-model="searchQuery"
      @focus="isFocused = true"
      @blur="handleBlur"
      @input="updateSuggestions"
      @keydown.down.prevent="onArrowDown"
      @keydown.up.prevent="onArrowUp"
      @keydown.enter.prevent="onEnter"
      @keydown.esc.prevent="onEscape"
      placeholder="Unlisted Search"
      class="pl-10 border-gray-300 shadow-sm dark:border-gray-700 h-full"
    />

    <!-- Suggestion dropdown -->
    <ul
      v-if="isFocused && filteredSuggestions.length"
      class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-900 rounded-md shadow-md max-h-60 overflow-auto"
      role="listbox"
    >
      <li
        v-for="(item, index) in filteredSuggestions"
        :key="index"
        :id="`unlisted-addr-opt-${index}`"
        role="option"
        :aria-selected="highlightedIndex === index"
        :class="[
          'px-4 py-2 cursor-pointer text-sm',
          highlightedIndex === index
            ? 'bg-gray-100 dark:bg-gray-600'
            : 'hover:bg-gray-100 dark:hover:bg-gray-600'
        ]"
        @click="selectSuggestion(item.description)"
        @mouseover="highlightedIndex = index"
        :ref="el => (itemRefs[index] = el as unknown as HTMLElement)"
      >
        {{ item.description }}
      </li>
    </ul>

    <div v-if="loading" class="absolute right-3 top-2 text-xs text-gray-500">
      Loading...
    </div>
  </div>
</template>
