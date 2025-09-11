<script setup lang="ts">
import {ref, watch} from 'vue'
import axios from 'axios'
import {useDebounceFn} from '@vueuse/core'
import {Icon} from "@iconify/vue";
import Input from "@/components/ui/input/Input.vue";

// Props
const props = defineProps<{
  modelValue: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

// Internal state
const search = ref(props.modelValue || '')
const results = ref<string[]>([])
const loading = ref(false)

// Sync with modelValue
watch(() => props.modelValue, (val) => {
  if (val !== search.value) {
    search.value = val
  }
})

// Debounced search
const fetchSuggestions = useDebounceFn(async () => {
  if (!search.value.trim()) {
    results.value = []
    return
  }

  loading.value = true
  try {
    const response = await axios.get(`/api/agent/search?q=${search.value}`)
    results.value = response.data
  } catch (err) {
    console.error('Search error:', err)
  } finally {
    loading.value = false
  }
}, 300)

watch(search, (val) => {
  emit('update:modelValue', val)
  fetchSuggestions()
})

// Selection handler
function selectItem(item: string) {
  emit('update:modelValue', item)
  results.value = []
}
</script>

<template>
  <div class="relative text-sm">
    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <Icon icon="tabler:search" class="text-gray-400 w-5 h-5"/>
    </span>
    <Input
        v-model="search"
        type="text"
        placeholder="Search listing agent..."
        class="pl-10"
    />
    <ul
        v-if="results.length"
        class="absolute z-10 bg-white border w-full mt-1 max-h-60 overflow-y-auto">
      <li
          v-for="item in results"
          :key="item"
          @click="selectItem(item)"
          class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
      >
        {{ item }}
      </li>
    </ul>

    <div v-if="loading" class="absolute right-3 top-2 text-xs text-gray-500">
      Loading...
    </div>
  </div>
</template>
