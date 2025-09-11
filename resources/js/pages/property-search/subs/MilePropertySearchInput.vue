<script setup lang="ts">
import { ref, watch, onMounted, nextTick, computed } from 'vue'
// import axios from 'axios'
import { useDebounceFn } from '@vueuse/core'
import { Icon } from '@iconify/vue'
import Input from "@/components/ui/input/Input.vue";

import {
  getPlaceAutocomplete,
  getZillowPlaceLocationData,
  PlacePredictionSuggestion,
  ZillowPlaceLatLng
} from "@/lib/zilowAndlocationUtil";
import { MileSearchAddress } from "@/pages/property-search/subs/filterHelper";
// Props
const props = defineProps<{
  defaultValue: MileSearchAddress | string | null
}>()

const emit = defineEmits<{
  (e: 'update:selected', value: MileSearchAddress): void
}>()

// Internal state
const search = ref('')
const suggestions = ref<PlacePredictionSuggestion[]>([]);
const error = ref<string>('');
const emitValue = ref<MileSearchAddress | null>(null);
const isFocused = ref(false)
const highlightedIndex = ref<number>(-1)
const itemRefs = ref<HTMLElement[]>([])

const loading = ref(false)
// Load default value if provided
onMounted(() => {
  let parsedValue: MileSearchAddress | null = null

  if (typeof props.defaultValue === 'string') {
    try {
      parsedValue = JSON.parse(props.defaultValue) as MileSearchAddress // Convert JSON string to object
    } catch (e) {
      console.error('Invalid JSON:', e)
    }
  } else if (props.defaultValue && typeof props.defaultValue === 'object') {
    parsedValue = props.defaultValue as MileSearchAddress
  }

  if (parsedValue?.address) {
    search.value = parsedValue.address
  }
  emitValue.value = parsedValue
})


const fetchPlaceAddressSuggestions = useDebounceFn(
  async () => {
    error.value = '';
    if (!search.value) return;
    loading.value = true
    const data = await getPlaceAutocomplete(search.value);

    if (data) {
      isFocused.value = true
      suggestions.value = data.predictions.map((prediction: PlacePredictionSuggestion) => prediction);
    }

    loading.value = false;
  }
);

// Debounced search
const fetchLatLong = useDebounceFn(async (query) => {
  if (!query) {
    return
  }
  loading.value = true;
  const data: ZillowPlaceLatLng = await getZillowPlaceLocationData(query)
  if (data.lat) {
    emitValue.value = { address: query, lat: data.lat, lng: data.lng };
  } else {
    error.value = "Selected address doesn't have a location information";
  }
  loading.value = false;
}, 300)

watch(search, fetchPlaceAddressSuggestions)

// Selection handler
async function selectItem(item: PlacePredictionSuggestion) {
  search.value = '';
  suggestions.value = [];
  error.value = '';
  await fetchLatLong(item.description)
  if (emitValue.value) {
    emit('update:selected', emitValue.value)
  }
}

const handleBlur = () => {
  setTimeout(() => (isFocused.value = false), 250)
}

const filteredSuggestions = computed(() => suggestions.value)

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
    if (choice) selectItem(choice)
  }
}

const onEscape = () => {
  isFocused.value = false
}
</script>

<template>
  <div v-if="emitValue" class="relative text-sm ">
    <div class="rounded-lg border border-input px-2 py-1 flex">
      <div class="flex-auto">{{ emitValue.address }}
        <br>
        <span class="text-xs text-muted-foreground ">
          <Icon icon="tabler:map-pin" class="inline align-sub" style="height: 14px; width: 14px" /> {{
            emitValue.lat + " | " + emitValue.lng
          }}
        </span>
      </div>
      <Icon icon="tabler:square-rounded-x" style="width: 25px; height: 25px" class="w-fit my-auto cursor-pointer"
        @click="emitValue = null" />
    </div>
  </div>
  <div v-else class="relative text-sm">
    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <Icon icon="tabler:search" class="text-gray-400 w-5 h-5" />
    </span>
    <Input v-model="search" type="text" placeholder="Search address..." class="pl-10 " @focus="isFocused = true"
      @blur="handleBlur" @keydown.down.prevent="onArrowDown" @keydown.up.prevent="onArrowUp"
      @keydown.enter.prevent="onEnter" @keydown.esc.prevent="onEscape" />
    <ul v-if="isFocused && filteredSuggestions.length"
      class="absolute z-10 bg-white border w-full mt-1 max-h-60 overflow-y-auto rounded-lg" role="listbox">
      <li v-for="(item, index) in filteredSuggestions" :key="item.place_id" :id="`mile-addr-opt-${index}`" role="option"
        :aria-selected="highlightedIndex === index" :class="[
          'px-3 py-2 cursor-pointer',
          highlightedIndex === index
            ? 'bg-gray-100 dark:bg-gray-700'
            : 'bg-input hover:bg-gray-100 dark:hover:bg-gray-700'
        ]" @click="selectItem(item)" @mouseover="highlightedIndex = index"
        :ref="el => (itemRefs[index] = el as unknown as HTMLElement)">
        {{ item.description }}
      </li>
    </ul>
    <div v-if="loading" class="absolute right-3 top-2 text-xs text-gray-500">
      Loading...
    </div>
  </div>
  <span v-if="error" class="text-sm text-red-600">{{ error }}</span>
</template>
