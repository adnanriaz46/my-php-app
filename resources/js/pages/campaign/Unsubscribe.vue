<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { TextArea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Mail, Shield, AlertTriangle, CheckCircle } from 'lucide-vue-next';

interface Props {
    email: string;
    encryptedEmail: string;
    campaignId?: string;
    isUnsubscribed: boolean;
    status?: string;
    error?: string;
}

const props = defineProps<Props>();

const form = useForm({
    reason: '',
    confirm: false,
    campaign_id: props.campaignId,
});

const submit = () => {
    form.post(route('campaign.unsubscribe.process', props.encryptedEmail));
};

const resubscribe = () => {
    form.post(route('campaign.resubscribe', props.encryptedEmail));
};
</script>

<template>
    <Head title="Email Unsubscription" />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <Mail class="w-6 h-6 text-red-600" />
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Email Unsubscription
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Manage your email preferences
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Shield class="w-5 h-5 text-blue-600" />
                        Email Address
                    </CardTitle>
                    <CardDescription>
                        {{ email }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <!-- Status Messages -->
                    <div v-if="status" class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <CheckCircle class="h-5 w-5 text-green-400" />
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ status }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <AlertTriangle class="h-5 w-5 text-red-400" />
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ error }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Already Unsubscribed -->
                    <div v-if="isUnsubscribed" class="text-center space-y-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <CheckCircle class="w-8 h-8 text-green-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                Already Unsubscribed
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                This email address is already unsubscribed from our campaigns.
                            </p>
                        </div>
                        <Button @click="resubscribe" variant="outline" class="w-full">
                            Resubscribe to Emails
                        </Button>
                    </div>

                    <!-- Unsubscription Form -->
                    <form v-else @submit.prevent="submit" class="space-y-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <AlertTriangle class="h-5 w-5 text-yellow-400" />
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Confirm Unsubscription
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>
                                            You're about to unsubscribe from all marketing emails from our platform. 
                                            This action will stop all future campaign emails to this address.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <Checkbox
                                    id="confirm"
                                    v-model="form.confirm"
                                    :disabled="form.processing"
                                />
                                <div class="grid gap-1.5 leading-none">
                                    <Label for="confirm" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                        I confirm that I want to unsubscribe
                                    </Label>
                                    <p class="text-sm text-muted-foreground">
                                        By checking this box, you confirm that you want to stop receiving all marketing emails from us.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="reason" class="text-sm font-medium">
                                Reason for unsubscribing (optional)
                            </Label>
                            <TextArea
                                id="reason"
                                v-model="form.reason"
                                placeholder="Please let us know why you're unsubscribing..."
                                :disabled="form.processing"
                                rows="3"
                            />
                        </div>

                        <div class="flex gap-3">
                            <Button
                                type="submit"
                                :disabled="!form.confirm || form.processing"
                                class="flex-1"
                                variant="destructive"
                            >
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>Unsubscribe</span>
                            </Button>
                        </div>
                    </form>

                    <!-- Footer -->
                    <div class="text-center text-xs text-gray-500 mt-6">
                        <p>
                            If you have any questions, please contact our support team.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template> 