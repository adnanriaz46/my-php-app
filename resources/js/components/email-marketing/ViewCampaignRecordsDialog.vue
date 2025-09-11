<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import Badge from '@/components/ui/badge/Badge.vue'
import { Icon } from '@iconify/vue'
import { useDateFormat } from '@/composables/useFormat'
import type { Campaign, CampaignRecipient } from '@/types/emailMarketing'

interface Props {
    open: boolean
    campaign: Campaign | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
    'update:open': [value: boolean]
}>()

const { formatDateTime } = useDateFormat()

// State
const recipients = ref<CampaignRecipient[]>([])
const loading = ref(false)
const searchTerm = ref('')
const statusFilter = ref<string>('all')
const currentPage = ref(1)
const itemsPerPage = ref(20)

// Computed
const filteredRecipients = computed(() => {
    let filtered = recipients.value

    // Apply search filter
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase()
        filtered = filtered.filter(recipient =>
            recipient.name.toLowerCase().includes(term) ||
            recipient.email.toLowerCase().includes(term)
        )
    }

    // Apply status filter
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(recipient => recipient.status === statusFilter.value)
    }

    return filtered
})

const paginatedRecipients = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return filteredRecipients.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(filteredRecipients.value.length / itemsPerPage.value))

const statusCounts = computed(() => {
    const counts = {
        pending: 0,
        sent: 0,
        failed: 0,
        unsubscribed: 0
    }

    recipients.value.forEach(recipient => {
        counts[recipient.status as keyof typeof counts]++
    })

    return counts
})

// Methods
const getStatusColor = (status: string) => {
    switch (status) {
        case 'sent':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
        case 'opened':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
        case 'clicked':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
        case 'failed':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
        case 'pending':
        case 'unsubscribed':
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
    }
}

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'sent':
            return 'tabler:check'
        case 'opened':
            return 'tabler:eye'
        case 'clicked':
            return 'tabler:mouse'
        case 'failed':
            return 'tabler:x'
        case 'unsubscribed':
            return 'tabler:mail-off'
        case 'pending':
        default:
            return 'tabler:clock'
    }
}

const loadRecipients = async () => {
    if (!props.campaign) return

    loading.value = true
    try {
        const response = await fetch(route('email-marketing.campaign.recipients', props.campaign.id), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })

        if (response.ok) {
            const data = await response.json()
            recipients.value = data.recipients || []
        } else {
            console.error('Failed to load recipients')
        }
    } catch (error) {
        console.error('Failed to load recipients:', error)
    } finally {
        loading.value = false
    }
}

const exportRecipients = () => {
    if (!props.campaign) return

    const csvContent = [
        ['Name', 'Email', 'Status', 'Sent At', 'Opened At', 'Clicked At'].join(','),
        ...recipients.value.map(recipient => [
            `"${recipient.name}"`,
            `"${recipient.email}"`,
            recipient.status,
            recipient.sent_at ? formatDateTime(recipient.sent_at) : '',
            recipient.opened_at ? formatDateTime(recipient.opened_at) : '',
            recipient.clicked_at ? formatDateTime(recipient.clicked_at) : ''
        ].join(','))
    ].join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `campaign-${props.campaign.id}-recipients.csv`
    a.click()
    window.URL.revokeObjectURL(url)
}

// Watchers
watch(() => props.open, (isOpen) => {
    if (isOpen && props.campaign) {
        loadRecipients()
    }
})

watch([searchTerm, statusFilter], () => {
    currentPage.value = 1
})
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-w-6xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <Icon icon="tabler:mail" class="h-5 w-5" />
                    Campaign Records
                    <span v-if="campaign" class="text-muted-foreground font-normal">
                        - {{ campaign.name }}
                    </span>
                </DialogTitle>
                <DialogDescription>
                    View campaign recipients and their delivery status
                </DialogDescription>
            </DialogHeader>

            <!-- Stats Summary -->
            <div v-if="campaign" class="grid grid-cols-3 md:grid-cols-4 gap-4 p-4 bg-muted/50 rounded-lg">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ statusCounts.pending }}</div>
                    <div class="text-sm text-muted-foreground">Pending</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ statusCounts.sent }}</div>
                    <div class="text-sm text-muted-foreground">Sent</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ statusCounts.unsubscribed }}</div>
                    <div class="text-sm text-muted-foreground">Unsubscribed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ statusCounts.failed }}</div>
                    <div class="text-sm text-muted-foreground">Failed</div>
                </div>

            </div>

            <!-- Filters -->
            <div class="flex flex-row gap-2 items-start justify-between">
                <div class="flex flex-col gap-2 flex-1">
                    <div class="relative flex-1 max-w-sm">
                        <Icon icon="tabler:search"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4" />
                        <Input v-model="searchTerm" placeholder="Search by name or email..." class="pl-10" />
                    </div>
                </div>
                <Button variant="outline" @click="exportRecipients">
                    <Icon icon="tabler:download" class="mr-1 h-4 w-4" />
                </Button>
            </div>

            <!-- Recipients Table -->
            <div class="flex-1 overflow-auto">
                <div v-if="loading" class="flex items-center justify-center py-8">
                    <Icon icon="tabler:loader-2" class="h-6 w-6 animate-spin text-muted-foreground" />
                    <span class="ml-2 text-muted-foreground">Loading recipients...</span>
                </div>

                <div v-else-if="recipients.length === 0" class="text-center py-8">
                    <Icon icon="tabler:mail-off" class="mx-auto h-12 w-12 text-muted-foreground" />
                    <h3 class="mt-2 text-sm font-medium text-muted-foreground">No recipients found</h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        This campaign doesn't have any recipients yet.
                    </p>
                </div>

                <div v-else class="border rounded-lg">
                    <div class="overflow-x-auto max-h-[calc(100dvh-425px)] md:max-h-[calc(100dvh-400px)] rounded-t-lg">
                        <table class="w-full">
                            <thead class="bg-muted sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Recipient
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Status
                                    </th>

                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="recipient in paginatedRecipients" :key="recipient.id"
                                    class="hover:bg-muted/50">
                                    <td class="px-4 py-3">
                                        <div>
                                            <div class="font-medium">{{ recipient.name }}</div>
                                            <div class="text-sm text-muted-foreground">{{ recipient.email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge :class="getStatusColor(recipient.status)"
                                            class="flex items-center gap-1 w-fit">
                                            <Icon :icon="getStatusIcon(recipient.status)" class="h-3 w-3" />
                                            {{ recipient.status }}
                                        </Badge>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination -->
                <div v-if="totalPages > 1" class="flex items-center justify-between mt-4">
                    <div class="text-sm text-muted-foreground">
                        Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
                        {{ Math.min(currentPage * itemsPerPage, filteredRecipients.length) }} of
                        {{ filteredRecipients.length }} recipients
                    </div>
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" :disabled="currentPage === 1" @click="currentPage--">
                            <Icon icon="tabler:chevron-left" class="h-4 w-4" />
                            Previous
                        </Button>
                        <Button variant="outline" size="sm" :disabled="currentPage === totalPages"
                            @click="currentPage++">
                            Next
                            <Icon icon="tabler:chevron-right" class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>