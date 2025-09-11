<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Icon } from '@iconify/vue';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import Label from "@/components/ui/label/Label.vue";
import Input from "@/components/ui/input/Input.vue";
import Textarea from "@/components/ui/textarea/TextArea.vue";
import { useToast } from "@/composables/useToast";
import { User } from '@/types';
import axios from 'axios';

const props = defineProps<{
    user: User | null;
}>();

const { showToast } = useToast();
const processing = ref(false);

const form = useForm({
    first_name: props.user?.first_name || '',
    last_name: props.user?.last_name || '',
    email: props.user?.email || '',
    phone: props.user?.phone_number || '',
    message: ''
});

const submit = async () => {
    if (!form.first_name || !form.last_name || !form.email || !form.message) {
        showToast('Validation Error', 'Please fill in all required fields', 'error');
        return;
    }

    processing.value = true;
    try {
        await axios.post(route('guest.contact_us.post'), form.data());

        showToast('Success', 'Your message has been sent successfully! We\'ll get back to you within 1 business day.', 'success');
        form.reset();
    } catch (error) {
        console.log(error);
        showToast('Error', 'Failed to send message. Please try again.', 'error');
    } finally {
        processing.value = false;
    }
};

const commonReasons = [
    'Questions about your free or paid plan',
    'Help comping deals or setting up your buy box',
    'Feature requests or bug reports',
    'Interested in partnering or investing in Revamp365.ai'
];
</script>

<template>
    <GuestAppLayout>
        <!-- Hero Section -->
        <div class="relative bg-gray-50 dark:bg-gray-900 py-16 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                        Let's Connect
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Got questions? Ready to scale your real estate game? We're here to help.
                    </p>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
                    <!-- Left Section - Information and Contact Options -->
                    <div class="space-y-8">
                        <!-- Introduction -->
                        <div>
                            <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                                We're investors too, and we know how valuable your time is. That's why we've built
                                Revamp365.ai
                                to help you find deals faster and make better investment decisions. We'd love to hear
                                from you
                                about how we can help you succeed.
                            </p>
                        </div>

                        <!-- Talk to a Real Human -->
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-full flex items-center justify-center">
                                    <Icon icon="tabler:phone" class="text-pink-600 dark:text-pink-400 text-xl" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    Talk to a Real Human
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-3">
                                    No bots. No outsourced support. Just real people who know the real estate game—and
                                    how to help you win at it.
                                </p>
                                <p class="text-gray-600 dark:text-gray-300">
                                    Need AI Robot support?
                                    <a :href="route('guest.ai_onboarding')"
                                        class="text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                                        click here
                                    </a>
                                    <span class="text-gray-500 dark:text-gray-400">(It's pretty awesome)</span>
                                </p>
                            </div>
                        </div>

                        <!-- Drop Us a Line -->
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                    <Icon icon="tabler:mail" class="text-blue-600 dark:text-blue-400 text-xl" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    Drop Us a Line
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300">
                                    Fill out the form or email us directly at
                                    <a :href="`mailto:team@revamp365.ai`"
                                        class="text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                                        team@revamp365.ai
                                    </a>.
                                    We'll get back to you within 1 business day (usually sooner).
                                </p>
                            </div>
                        </div>

                        <!-- Common Reasons -->
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                                    <Icon icon="tabler:bulb" class="text-yellow-600 dark:text-yellow-400 text-xl" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                    Common Reasons People Reach Out:
                                </h3>
                                <ul class="space-y-2">
                                    <li v-for="reason in commonReasons" :key="reason"
                                        class="flex items-start gap-2 text-gray-600 dark:text-gray-300">
                                        <span
                                            class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full mt-2 flex-shrink-0"></span>
                                        <span>{{ reason }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section - Contact Form -->
                    <div>
                        <Card class="p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-8">
                                Get In Touch With Us
                            </h2>

                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- First Name -->
                                <div>
                                    <Label for="first_name"
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        First Name <span class="text-red-500">*</span>
                                    </Label>
                                    <Input id="first_name" v-model="form.first_name" type="text" class="mt-1 w-full"
                                        placeholder="Enter your first name" required />
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <Label for="last_name" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Last Name <span class="text-red-500">*</span>
                                    </Label>
                                    <Input id="last_name" v-model="form.last_name" type="text" class="mt-1 w-full"
                                        placeholder="Enter your last name" required />
                                </div>

                                <!-- Email -->
                                <div>
                                    <Label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Email <span class="text-red-500">*</span>
                                    </Label>
                                    <Input id="email" v-model="form.email" type="email" class="mt-1 w-full"
                                        placeholder="Enter your email address" required />
                                </div>

                                <!-- Phone -->
                                <div>
                                    <Label for="phone" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Phone
                                    </Label>
                                    <Input id="phone" v-model="form.phone" type="tel" class="mt-1 w-full"
                                        placeholder="Enter your phone number" />
                                </div>

                                <!-- Message -->
                                <div>
                                    <Label for="message" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Your message to us <span class="text-red-500">*</span>
                                    </Label>
                                    <Textarea id="message" v-model="form.message" class="mt-1 w-full min-h-[120px]"
                                        placeholder="Your message" required />
                                </div>

                                <!-- Submit Button -->
                                <Button type="submit" variant="default"
                                    class="w-full bg-primary hover:bg-primary/90 text-white py-3 px-6 rounded-lg transition-all duration-200 cursor-pointer"
                                    :disabled="processing">
                                    <Icon v-if="processing" icon="tabler:loader-2"
                                        class="loading-icon text-base mr-2" />
                                    {{ processing ? 'Sending...' : 'Contact Us' }}
                                </Button>
                            </form>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </GuestAppLayout>
</template>

<style scoped>
.loading-icon {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>