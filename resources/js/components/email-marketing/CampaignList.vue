<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Icon } from '@iconify/vue';
import { useDateFormat } from '@/composables/useFormat';
import ViewCampaignRecordsDialog from './ViewCampaignRecordsDialog.vue';
import ViewCampaignContentDialog from './ViewCampaignContentDialog.vue';
import ViewCampaignStatesDialog from './ViewCampaignStatesDialog.vue';
import type { Campaign } from '@/types/emailMarketing';
import axios from 'axios';

const { formatDateTime } = useDateFormat();

interface Props {
    campaigns: Campaign[];
}

const props = defineProps<Props>();

// Dialog state
const showRecordsDialog = ref(false);
const showContentDialog = ref(false);
const selectedCampaign = ref<Campaign | null>(null);

const openRecordsDialog = (campaign: Campaign) => {
    selectedCampaign.value = campaign;
    showRecordsDialog.value = true;
};

const openContentDialog = (campaign: Campaign) => {
    selectedCampaign.value = campaign;
    showContentDialog.value = true;
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'sent':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'scheduled':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'failed':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'draft':
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'sent':
            return 'tabler:check';
        case 'scheduled':
            return 'tabler:clock';
        case 'failed':
            return 'tabler:x';
        case 'draft':
        default:
            return 'tabler:edit';
    }
};

onMounted(() => {
    props.campaigns.forEach(async (campaign) => {
        await fetchCampaignStats(campaign.id);
        campaign.additional_data = await fetchCampaignStats(campaign.id);
    });

    console.log(props.campaigns);
});

const fetchCampaignStats = async (campaignId: number) => {
    const response = await axios.get(route('email-marketing.campaign.stats', { id: campaignId }));
    return response.data;
};

const showStatesDialog = ref(false);

const openStatesDialog = (campaign: Campaign) => {
    selectedCampaign.value = campaign;
    showStatesDialog.value = true;
};

</script>

<template>
    <div class="space-y-4">
        <div v-if="props.campaigns.length === 0" class="text-center py-12">
            <Icon icon="tabler:mail-off" class="mx-auto h-12 w-12 text-muted-foreground" />
            <h3 class="mt-2 text-sm font-medium text-muted-foreground">No campaigns yet</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Get started by creating your first email campaign.
            </p>
        </div>

        <div v-else class="grid gap-4 max-h-[calc(100dvh-345px)] md:max-h-[calc(100dvh-320px)] overflow-auto">
            <Card v-for="campaign in props.campaigns" :key="campaign.id" class="hover:shadow-md transition-shadow">
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <CardTitle class="text-lg">{{ campaign.name }}</CardTitle>
                            <CardDescription>{{ campaign.subject }}</CardDescription>
                        </div>
                        <div class="flex flex-col gap-1 items-end">
                            <div
                                :class="['px-2 py-1 rounded-full text-xs font-medium flex items-center gap-1 w-fit text-right', getStatusColor(campaign.status)]">
                                <Icon :icon="getStatusIcon(campaign.status)" class="mr-1 h-3 w-3" />
                                {{ campaign.status }}
                            </div>
                            <div class="text-xs text-muted-foreground flex items-center gap-1"
                                v-if="campaign.status === 'scheduled' && campaign.scheduled_at">
                                <Icon icon="tabler:calendar-time" class="mr-1 min-h-4 min-w-4" />{{
                                    formatDateTime(campaign.scheduled_at) }}
                            </div>
                            <div class="text-xs text-muted-foreground flex items-center gap-1"
                                v-if="campaign.status === 'sent' && campaign.sent_at">
                                <Icon icon="tabler:mail-check" class="mr-1 min-h-4 min-w-4" />{{
                                    formatDateTime(campaign.sent_at) }}
                            </div>
                        </div>

                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <div class="font-medium text-muted-foreground">Recipients</div>
                            <div class="text-lg font-semibold">{{ campaign.recipients_count || 0 }}</div>
                        </div>
                        <div>
                            <div class="font-medium text-muted-foreground">Sent</div>
                            <div class="text-lg font-semibold text-green-600">{{ campaign.sent_count || 0 }}</div>
                        </div>
                        <div>
                            <div class="font-medium text-muted-foreground">Open Rate</div>
                            <div class="text-lg font-semibold">{{ campaign.additional_data?.open_rate || 0 }}%</div>
                        </div>
                        <div>
                            <div class="font-medium text-muted-foreground">Click Rate</div>
                            <div class="text-lg font-semibold">{{ campaign.additional_data?.click_rate || 0 }}%</div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-between mt-4 pt-4 border-t">
                        <div class="text-xs text-muted-foreground ml-auto flex gap-4 pb-2">
                            <span v-if="campaign.scheduled_at" class="flex gap-1">
                                <Icon icon="tabler:calendar-time" class="min-h-4 min-w-4 text-primary" /> {{
                                    formatDateTime(campaign.scheduled_at) }}
                            </span>
                            <span v-if="campaign.sent_at" class="flex gap-1">
                                <Icon icon="tabler:mail-check" class="min-h-4 min-w-4 text-green-600" /> {{
                                    formatDateTime(campaign.sent_at) }}
                            </span>
                            <span class="flex gap-1">
                                <Icon icon="tabler:pencil-plus" class="min-h-4 min-w-4 text-muted-foreground" /> {{
                                    formatDateTime(campaign.created_at) }}
                            </span>
                        </div>
                        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                            <Button variant="outline" size="sm" @click="openRecordsDialog(campaign)">
                                <Icon icon="tabler:eye" class="mr-1 h-4 w-4" />
                                View Recipients
                            </Button>
                            <Button variant="outline" size="sm" @click="openContentDialog(campaign)">
                                <Icon icon="tabler:copy" class="mr-1 h-4 w-4" />
                                View Content
                            </Button>
                            <Button variant="outline" size="sm" @click="openStatesDialog(campaign)">
                                <Icon icon="tabler:chart-bar" class="mr-1 h-4 w-4" />
                                View States
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>

    <!-- View Campaign Records Dialog -->
    <ViewCampaignRecordsDialog v-model:open="showRecordsDialog" :campaign="selectedCampaign" />

    <!-- View Campaign Content Dialog -->
    <ViewCampaignContentDialog v-model:open="showContentDialog" :campaign="selectedCampaign" />

    <!-- View Campaign States Dialog -->
    <ViewCampaignStatesDialog v-model:open="showStatesDialog" :campaign="selectedCampaign" />
</template>