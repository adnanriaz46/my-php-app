<script setup lang="ts">
import { computed } from 'vue'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import Badge from '@/components/ui/badge/Badge.vue'
import { Icon } from '@iconify/vue'
import { useDateFormat } from '@/composables/useFormat'
import { useNumber } from '@/composables/useFormat'
import type { Campaign } from '@/types/emailMarketing'
import type { DBApiPropertyFull } from '@/types/DBApi'
import PropertyStatusBadge from '../ui/proprety-status-badge/PropertyStatusBadge.vue'

interface Props {
    open: boolean
    campaign: Campaign | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
    'update:open': [value: boolean]
}>()

const { formatDateTime } = useDateFormat()
const { formatPrice, formatNumber } = useNumber()

// Computed
const propertyData = computed(() => {
    if (!props.campaign?.property_data) return null
    return props.campaign.property_data as DBApiPropertyFull
})


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

const getPropertyImage = (property: DBApiPropertyFull) => {
    if (property.full_location && property.full_location.split(',').length > 0) {
        const firstImage = property.full_location.split(',')[0]
        if (firstImage && firstImage.trim() !== '') {
            return firstImage
        }
    }
    return undefined
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)" class="min-w-full sm:min-w-auto">
        <DialogContent class="max-w-4xl md:min-w-[700px] max-h-[98dvh] overflow-hidden ">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <Icon icon="tabler:mail" class="h-5 w-5" />
                    Campaign Content
                    <span v-if="campaign" class="text-muted-foreground font-normal text-sm">
                        - {{ campaign.name }}
                    </span>
                </DialogTitle>
                <DialogDescription>
                    View campaign details, property information, and email content
                </DialogDescription>
            </DialogHeader>

            <div class="flex-1 overflow-auto space-y-6">
                <!-- Campaign Overview -->
                <div v-if="campaign" class="space-y-4 text-base">
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

                    <!-- Campaign Stats
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-muted/50 rounded-lg">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ campaign.recipients_count || 0 }}</div>
                            <div class="text-sm text-muted-foreground">Recipients</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ campaign.sent_count || 0 }}</div>
                            <div class="text-sm text-muted-foreground">Sent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ campaign.open_rate || 0 }}%</div>
                            <div class="text-sm text-muted-foreground">Open Rate</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ campaign.click_rate || 0 }}%</div>
                            <div class="text-sm text-muted-foreground">Click Rate</div>
                        </div>
                    </div> -->
                </div>
                <div class="max-h-[calc(100dvh-220px)] md:max-h-[calc(100dvh-265px)] overflow-auto">
                    <!-- Campaign Timeline -->
                    <div class="space-y-2" v-if="campaign">
                        <h4 class="font-medium">Campaign Timeline</h4>
                        <div class="space-y-1 text-sm text-muted-foreground">
                            <div><span class="font-semibold">Created:</span> {{ formatDateTime(campaign.created_at) }}</div>
                            <div v-if="campaign.scheduled_at">
                                <span class="font-semibold">Scheduled:</span> {{ formatDateTime(campaign.scheduled_at) }}
                            </div>
                            <div v-if="campaign.sent_at">
                                <span class="font-semibold">Sent:</span> {{ formatDateTime(campaign.sent_at) }}
                            </div>
                        </div>
                    </div>



                    <!-- Property Information -->
                    <div v-if="propertyData" class="space-y-4 mt-4">
                        <h3 class="text-lg font-semibold">Property Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Property Image -->
                            <div class="space-y-4">
                                <div class="aspect-video rounded-lg overflow-hidden bg-muted">
                                    <img v-if="getPropertyImage(propertyData)" :src="getPropertyImage(propertyData)"
                                        :alt="propertyData.geo_address" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <Icon icon="tabler:photo-off" class="w-12 h-12 text-muted-foreground" />
                                    </div>
                                </div>
                            </div>

                            <!-- Property Details -->
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-medium text-lg">{{ propertyData.geo_address }}</h4>
                                    <div class="text-2xl font-bold text-primary flex items-center gap-2">
                                        {{ formatPrice(propertyData.list_price) }}
                                        <PropertyStatusBadge :status="propertyData.status">
                                            {{ propertyData.status }}
                                        </PropertyStatusBadge>
                                    </div>

                                </div>


                            </div>
                            <!-- Property Stats -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-lg font-semibold">{{ formatNumber(propertyData.bedrooms_count)
                                        }}</div>
                                    <div class="text-sm text-muted-foreground">Bedrooms</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-semibold">{{
                                        formatNumber(propertyData.bathrooms_total_count) }}</div>
                                    <div class="text-sm text-muted-foreground">Bathrooms</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-semibold">{{
                                        formatNumber(propertyData.total_finished_sqft) }}</div>
                                    <div class="text-sm text-muted-foreground">Sq Ft</div>
                                </div>
                            </div>

                            <!-- Additional Property Details -->
                            <div class="space-y-2 text-sm">
                                <div v-if="propertyData.structure_type" class="flex justify-between">
                                    <span class="text-muted-foreground">Structure Type:</span>
                                    <span>{{ propertyData.structure_type }}</span>
                                </div>
                                <div v-if="propertyData.year_built" class="flex justify-between">
                                    <span class="text-muted-foreground">Year Built:</span>
                                    <span>{{ propertyData.year_built }}</span>
                                </div>
                                <div v-if="propertyData.lot_sqft" class="flex justify-between">
                                    <span class="text-muted-foreground">Lot Size:</span>
                                    <span>{{ formatNumber(propertyData.lot_sqft) }} sq ft</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Content -->
                    <div v-if="campaign?.email_header || campaign?.email_description" class="space-y-4 mt-4 text-base">
                        <h3 class="text-lg font-semibold">Email Content</h3>

                        <!-- Email Subject -->
                        <div v-if="campaign?.subject" class="space-y-2">
                            <h4 class="font-medium">Subject Line</h4>
                            <div class="p-3 bg-muted/50 rounded-lg text-sm">
                                {{ campaign.subject }}
                            </div>
                        </div>

                        <!-- Email Header -->
                        <div v-if="campaign?.email_header" class="space-y-2">
                            <h4 class="font-medium">Header Text</h4>
                            <div class="p-3 bg-muted/50 rounded-lg text-sm">
                                {{ campaign.email_header }}
                            </div>
                        </div>

                        <!-- Email Description/Content -->
                        <div v-if="campaign?.email_description" class="space-y-2">
                            <h4 class="font-medium">Email Content</h4>
                            <div class="p-4 bg-muted/50 rounded-lg text-sm prose dark:prose-invert max-w-none">
                                <div v-html="campaign.email_description"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>