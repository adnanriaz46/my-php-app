<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog'

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import Badge from '@/components/ui/badge/Badge.vue'
import { Icon } from '@iconify/vue'

import type { Campaign, CampaignAllStates } from '@/types/emailMarketing'
import axios from 'axios'

interface Props {
    open: boolean
    campaign: Campaign | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
    'update:open': [value: boolean]
}>()


// State
const campaignStates = ref<CampaignAllStates | null>(null)
const loading = ref(false)

// Helper functions for chart colors
const getColorForChart = (colorClass: string) => {
    const colorMap: Record<string, string> = {
        'bg-blue-500': '#3b82f6',
        'bg-green-500': '#10b981',
        'bg-purple-500': '#8b5cf6',
        'bg-orange-500': '#f97316',
        'bg-red-500': '#ef4444',
        'bg-gray-500': '#6b7280',
        'bg-yellow-500': '#eab308',
        'bg-pink-500': '#ec4899',
    }
    return colorMap[colorClass] || '#6b7280'
}

const getStrokeDashoffset = (index: number) => {
    // Calculate cumulative offset for pie chart segments
    let offset = 0
    for (let i = 0; i < index; i++) {
        // This would need to be calculated based on actual data
        // For now, we'll use a simple offset
        offset += 62.8 // 25% of 251.2 (circumference)
    }
    return offset
}

// Computed
const chartData = computed(() => {
    if (!campaignStates.value) return []

    return [
        { name: 'Processed', value: campaignStates.value.processed, color: 'bg-blue-500' },
        { name: 'Delivered', value: campaignStates.value.delivered, color: 'bg-green-500' },
        { name: 'Opened', value: campaignStates.value.open, color: 'bg-purple-500' },
        { name: 'Clicked', value: campaignStates.value.click, color: 'bg-orange-500' },
        { name: 'Bounced', value: campaignStates.value.bounce, color: 'bg-red-500' },
        { name: 'Dropped', value: campaignStates.value.dropped, color: 'bg-gray-500' },
        { name: 'Spam Report', value: campaignStates.value.spam_report, color: 'bg-yellow-500' },
        { name: 'Unsubscribed', value: campaignStates.value.unsubscribe, color: 'bg-pink-500' },
    ].filter(item => item.value > 0)
})

const totalEvents = computed(() => {
    if (!campaignStates.value) return 0
    return Object.values(campaignStates.value).reduce((sum, value) => sum + value, 0)
})

const deliveryRate = computed(() => {
    if (!campaignStates.value || !props.campaign?.recipients_count) return 0
    return ((campaignStates.value.delivered / props.campaign.recipients_count) * 100).toFixed(1)
})

const openRate = computed(() => {
    if (!campaignStates.value || !props.campaign?.recipients_count) return 0
    return ((campaignStates.value.open / props.campaign.recipients_count) * 100).toFixed(1)
})

const clickRate = computed(() => {
    if (!campaignStates.value || !props.campaign?.recipients_count) return 0
    return ((campaignStates.value.click / props.campaign.recipients_count) * 100).toFixed(1)
})

const bounceRate = computed(() => {
    if (!campaignStates.value || !props.campaign?.recipients_count) return 0
    return ((campaignStates.value.bounce / props.campaign.recipients_count) * 100).toFixed(1)
})

// Methods
const loadCampaignStates = async () => {
    if (!props.campaign) return

    loading.value = true
    try {
        const response = await axios.get(route('email-marketing.campaign.states', props.campaign.id))
        campaignStates.value = response.data
    } catch (error) {
        console.error('Failed to load campaign states:', error)
    } finally {
        loading.value = false
    }
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'sent':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
        case 'scheduled':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
        case 'failed':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
        case 'draft':
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
    }
}

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'sent':
            return 'tabler:check'
        case 'scheduled':
            return 'tabler:clock'
        case 'failed':
            return 'tabler:x'
        case 'draft':
        default:
            return 'tabler:edit'
    }
}

// Watchers
watch(() => props.open, (isOpen) => {
    if (isOpen && props.campaign) {
        loadCampaignStates()
    }
})
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)" class="min-w-full sm:min-w-auto">
        <DialogContent class="max-w-4xl md:min-w-[700px]  max-h-[98dvh] overflow-hidden px-2 md:px-4">
            <DialogHeader>
                <DialogTitle class="flex items-start justify-start text-left gap-2">
                    <Icon icon="tabler:chart-bar" class="h-5 w-5" />
                    <div class="flex flex-col md:flex-row">
                        <span class="mr-3">Campaign States</span>
                        <span v-if="campaign" class="text-muted-foreground font-normal text-sm">
                            {{ campaign.name }}
                        </span>
                    </div>

                </DialogTitle>
                <DialogDescription>
                    View detailed campaign delivery statistics and event tracking
                </DialogDescription>
            </DialogHeader>

            <div class="flex-1 overflow-auto space-y-6 h-[calc(100dvh-150px)] overflow-y-auto">
                <!-- Campaign Overview -->
                <div v-if="campaign" class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <h3 class="text-lg font-semibold">{{ campaign.name }}</h3>
                            <p class="text-muted-foreground">{{ campaign.subject }}</p>
                        </div>
                        <Badge :class="getStatusColor(campaign.status)" class="flex items-center gap-1">
                            <Icon :icon="getStatusIcon(campaign.status)" class="h-3 w-3" />
                            {{ campaign.status }}
                        </Badge>
                    </div>

                   
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="flex items-center justify-center py-12">
                    <div class="flex items-center gap-2">
                        <Icon icon="tabler:loader-2" class="animate-spin h-5 w-5" />
                        <span class="text-sm text-muted-foreground">Loading campaign states...</span>
                    </div>
                </div>

                <!-- Campaign States Data -->
                <div v-else-if="campaignStates" class="space-y-6">
                    <!-- Event Statistics -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Icon icon="tabler:chart-pie" class="h-5 w-5" />
                                Email Event Statistics
                            </CardTitle>
                            <CardDescription>
                                Breakdown of all SendGrid events for this campaign
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-blue-50 dark:bg-blue-950/20 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ campaignStates.processed }}</div>
                                    <div class="text-sm text-muted-foreground">Processed</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 dark:bg-green-950/20 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ campaignStates.delivered }}</div>
                                    <div class="text-sm text-muted-foreground">Delivered</div>
                                </div>
                                <div class="text-center p-4 bg-purple-50 dark:bg-purple-950/20 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ campaignStates.open }}</div>
                                    <div class="text-sm text-muted-foreground">Opened</div>
                                </div>
                                <div class="text-center p-4 bg-orange-50 dark:bg-orange-950/20 rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600">{{ campaignStates.click }}</div>
                                    <div class="text-sm text-muted-foreground">Clicked</div>
                                </div>
                                <div class="text-center p-4 bg-red-50 dark:bg-red-950/20 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">{{ campaignStates.bounce }}</div>
                                    <div class="text-sm text-muted-foreground">Bounced</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-950/20 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-600">{{ campaignStates.dropped }}</div>
                                    <div class="text-sm text-muted-foreground">Dropped</div>
                                </div>
                                <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-950/20 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">{{ campaignStates.spam_report }}
                                    </div>
                                    <div class="text-sm text-muted-foreground">Spam Reports</div>
                                </div>
                                <div class="text-center p-4 bg-pink-50 dark:bg-pink-950/20 rounded-lg">
                                    <div class="text-2xl font-bold text-pink-600">{{ campaignStates.unsubscribe }}</div>
                                    <div class="text-sm text-muted-foreground ">Unsubscribed</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Visual Chart -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Icon icon="tabler:chart-bar" class="h-5 w-5" />
                                Event Distribution
                            </CardTitle>
                            <CardDescription>
                                Visual representation of campaign events
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="chartData.length > 0" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Bar Chart -->
                                    <div class="space-y-3">
                                        <h4 class="font-medium">Event Counts</h4>
                                        <div class="space-y-2">
                                            <div v-for="item in chartData" :key="item.name"
                                                class="flex items-center gap-3">
                                                <div class="w-4 h-4 rounded" :class="item.color"></div>
                                                <span class="text-sm font-medium min-w-[100px]">{{ item.name }}</span>
                                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="h-2 rounded-full transition-all duration-300"
                                                        :class="item.color.replace('bg-', 'bg-')"
                                                        :style="{ width: `${(item.value / Math.max(...chartData.map(d => d.value))) * 100}%` }">
                                                    </div>
                                                </div>
                                                <span class="text-sm font-bold min-w-[40px] text-right">{{ item.value
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pie Chart Representation -->
                                    <div class="space-y-3">
                                        <h4 class="font-medium">Event Distribution</h4>
                                        <div class="relative w-48 h-48 mx-auto">
                                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                                <circle cx="50" cy="50" r="40" fill="none" stroke-width="20"
                                                    stroke="#e5e7eb" class="dark:stroke-gray-700" />
                                                <circle v-for="(item, index) in chartData" :key="item.name" cx="50"
                                                    cy="50" r="40" fill="none" stroke-width="20"
                                                    :stroke="getColorForChart(item.color)"
                                                    :stroke-dasharray="`${(item.value / totalEvents) * 251.2} 251.2`"
                                                    :stroke-dashoffset="getStrokeDashoffset(index)"
                                                    class="transition-all duration-300" />
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold">{{ totalEvents }}</div>
                                                    <div class="text-xs text-muted-foreground">Total Events</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-muted-foreground">
                                <Icon icon="tabler:chart-off" class="mx-auto h-12 w-12 mb-4 opacity-50" />
                                <p>No event data available for this campaign</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Performance Metrics -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Icon icon="tabler:target" class="h-5 w-5" />
                                Performance Metrics
                            </CardTitle>
                            <CardDescription>
                                Key performance indicators for this campaign
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center p-4 border rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ deliveryRate }}%</div>
                                    <div class="text-sm text-muted-foreground">Delivery Rate</div>
                                </div>
                                <div class="text-center p-4 border rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ openRate }}%</div>
                                    <div class="text-sm text-muted-foreground">Open Rate</div>
                                </div>
                                <div class="text-center p-4 border rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600">{{ clickRate }}%</div>
                                    <div class="text-sm text-muted-foreground">Click Rate</div>
                                </div>
                                <div class="text-center p-4 border rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">{{ bounceRate }}%</div>
                                    <div class="text-sm text-muted-foreground">Bounce Rate</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- No Data State -->
                <div v-else class="text-center py-12 text-muted-foreground">
                    <Icon icon="tabler:chart-off" class="mx-auto h-12 w-12 mb-4 opacity-50" />
                    <p>No campaign data available</p>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>