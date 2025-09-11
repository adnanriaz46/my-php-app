<template>
  <div class="container mx-auto p-6">
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">HTTP Error Monitor</h1>
      <p class="text-gray-600 dark:text-gray-400 mt-2">Monitor HTTP errors and API failures in real-time</p>
    </div>

    <!-- Error Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
            <Icon icon="tabler:alert-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Errors</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ errorStats.total }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
            <Icon icon="tabler:api" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">API Errors</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ errorStats.api }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
            <Icon icon="tabler:clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Timeouts</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ errorStats.timeout }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
            <Icon icon="tabler:network" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Network Errors</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ errorStats.network }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
      <div class="flex flex-wrap gap-4 items-center">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Range</label>
          <SelectBox
            v-model="selectedTimeRange"
            :options="timeRangeOptions"
            class="w-40"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Error Type</label>
          <SelectBox
            v-model="selectedErrorType"
            :options="errorTypeOptions"
            class="w-40"
          />
        </div>
        <div class="ml-auto">
          <Button @click="refreshErrors" :loading="loading">
            <Icon icon="tabler:refresh" class="w-4 h-4 mr-2" />
            Refresh
          </Button>
        </div>
      </div>
    </div>

    <!-- Error List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent HTTP Errors</h3>
      </div>
      
      <div v-if="loading" class="p-6">
        <div class="flex items-center justify-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600 dark:text-gray-400">Loading errors...</span>
        </div>
      </div>

      <div v-else-if="errors.length === 0" class="p-6 text-center">
        <Icon icon="tabler:check-circle" class="w-12 h-12 text-green-500 mx-auto mb-4" />
        <p class="text-gray-600 dark:text-gray-400">No HTTP errors found in the selected time range.</p>
      </div>

      <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
        <div
          v-for="error in errors"
          :key="error.id"
          class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center mb-2">
                <Badge :variant="getErrorVariant(error.type)" class="mr-3">
                  {{ error.type }}
                </Badge>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  {{ formatTime(error.time) }}
                </span>
              </div>
              
              <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                {{ error.message }}
              </h4>
              
              <div v-if="error.details" class="text-sm text-gray-600 dark:text-gray-400">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-if="error.details.url">
                    <strong>URL:</strong> {{ error.details.url }}
                  </div>
                  <div v-if="error.details.status">
                    <strong>Status:</strong> {{ error.details.status }}
                  </div>
                  <div v-if="error.details.params">
                    <strong>Params:</strong> 
                    <pre class="text-xs bg-gray-100 dark:bg-gray-700 p-2 rounded mt-1 overflow-x-auto">{{ JSON.stringify(error.details.params, null, 2) }}</pre>
                  </div>
                  <div v-if="error.details.body">
                    <strong>Response:</strong>
                    <pre class="text-xs bg-gray-100 dark:bg-gray-700 p-2 rounded mt-1 overflow-x-auto">{{ error.details.body }}</pre>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Icon } from '@iconify/vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { SelectBox } from '@/components/ui/select-box'

interface HttpError {
  id: string
  time: string
  type: string
  message: string
  details?: {
    url?: string
    status?: number
    params?: any
    body?: string
  }
}

const loading = ref(false)
const errors = ref<HttpError[]>([])
const selectedTimeRange = ref('24')
const selectedErrorType = ref('all')

const timeRangeOptions = [
  { value: '1', label: 'Last Hour' },
  { value: '6', label: 'Last 6 Hours' },
  { value: '24', label: 'Last 24 Hours' },
  { value: '168', label: 'Last Week' }
]

const errorTypeOptions = [
  { value: 'all', label: 'All Errors' },
  { value: 'api', label: 'API Errors' },
  { value: 'network', label: 'Network Errors' },
  { value: 'timeout', label: 'Timeouts' }
]

const errorStats = computed(() => {
  const stats = {
    total: errors.value.length,
    api: errors.value.filter(e => e.type === 'API Request').length,
    timeout: errors.value.filter(e => e.type === 'Timeout').length,
    network: errors.value.filter(e => e.type === 'Network').length
  }
  return stats
})

const getErrorVariant = (type: string) => {
  switch (type) {
    case 'API Request': return 'destructive'
    case 'Network': return 'secondary'
    case 'Timeout': return 'outline'
    default: return 'default'
  }
}

const formatTime = (time: string) => {
  return new Date(time).toLocaleString()
}

const fetchErrors = async () => {
  loading.value = true
  try {
    const response = await fetch(`/admin/http-errors?hours=${selectedTimeRange.value}&type=${selectedErrorType.value}`)
    const data = await response.json()
    errors.value = data.errors || []
  } catch (error) {
    console.error('Failed to fetch HTTP errors:', error)
  } finally {
    loading.value = false
  }
}

const refreshErrors = () => {
  fetchErrors()
}

onMounted(() => {
  fetchErrors()
})
</script> 