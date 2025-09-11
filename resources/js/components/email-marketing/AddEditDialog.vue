<script setup lang="ts">
import { ref, watch } from 'vue'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { Contact } from '@/types/emailMarketing'
import Combobox from '@/components/ui/combobox/Combobox.vue'
import CheckboxLoop from '@/components/ui/checkbox-loop/CheckboxLoop.vue'
import { router, usePage } from '@inertiajs/vue3'

interface Props {
    open: boolean
    contact?: Contact | null
    isEditing?: boolean,
    counties: string[],
    zipCodes: string[],
    dealTypes: string[],
    tags: string[]
}

interface FormData {
    contactId: number
    first_name: string
    last_name: string
    email: string
    phone: string
    price_range: string[]
    tags: string[]
    counties: string[]
    zip: string[]
    deal_type: string[]
    email_type: string
}

interface FormErrors {
    contactId?: string
    first_name?: string
    last_name?: string
    email?: string
    phone?: string
    price_range?: string
    tags?: string
    counties?: string
    zip?: string
    deal_type?: string
    email_type?: string
}

const props = withDefaults(defineProps<Props>(), {
    isEditing: false
})

const page = usePage()

const emit = defineEmits<{
    'update:open': [value: boolean]
}>()

// Form state
const form = ref<FormData>({
    contactId: 0,
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    price_range: [],
    tags: [],
    counties: [],
    zip: [],
    deal_type: [],
    email_type: ''
})

const errors = ref<FormErrors>({})
const isSubmitting = ref(false)


// Validation function
const validateForm = (): boolean => {
    errors.value = {}

    if (!form.value.contactId && props.isEditing) {
        errors.value.contactId = 'Contact ID is required'
    }

    if (!form.value.first_name.trim()) {
        errors.value.first_name = 'First name is required'
    }

    if (!form.value.last_name.trim()) {
        errors.value.last_name = 'Last name is required'
    }

    if (!form.value.email.trim()) {
        errors.value.email = 'Email is required'
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
        errors.value.email = 'Please enter a valid email address'
    }

    if (form.value.phone && !/^[\+]?[1-9][\d]{0,15}$/.test(form.value.phone.replace(/[\s\-\(\)]/g, ''))) {
        errors.value.phone = 'Please enter a valid phone number'
    }

    if (form.value.tags.length === 0) {
        errors.value.tags = 'At least one tag is required'
    }


    return Object.keys(errors.value).length === 0
}

// Handle form submission
const handleSubmit = async () => {
    if (!validateForm()) {
        return
    }

    isSubmitting.value = true

    try {
        const pageQueryParams = page.url.split('?')[1] ?? '';
        const formData = new FormData();
        formData.append('contactId', form.value.contactId.toString());
        formData.append('first_name', form.value.first_name);
        formData.append('last_name', form.value.last_name);
        formData.append('email', form.value.email);
        formData.append('phone', form.value.phone);
        form.value.tags.forEach(tag => formData.append('tags[]', tag));
        form.value.counties.forEach(county => formData.append('counties[]', county));
        form.value.zip.forEach(zip => formData.append('zip[]', zip));
        form.value.deal_type.forEach(deal => formData.append('deal_type[]', deal));
        formData.append('page_query_params', pageQueryParams);

        router.post(route('email-marketing.contacts.save-contact'), formData, {
            onSuccess: (data) => {
                emit('update:open', false)
                const pageQueryParamsArray = Object.fromEntries(new URLSearchParams(pageQueryParams))
                console.log(data?.props?.success ?? 'success')
                router.visit(route('email-marketing.contacts', pageQueryParamsArray), {
                    preserveScroll: true,
                    preserveState: false,
                })
            },
            onError: (data: Record<string, string>) => {
                for (const key in data) {
                    errors.value[key as keyof FormErrors] = data[key]
                }
                console.log('Error submitting form:', data)
            }
        })

    } catch (error: any) {
        console.error('Error submitting form:', error)
    } finally {
        isSubmitting.value = false
    }
}

// Reset form when dialog opens/closes
const resetForm = () => {
    form.value = {
        contactId: 0,
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        price_range: [],
        tags: [],
        counties: [],
        zip: [],
        deal_type: [],
        email_type: ''
    }
    errors.value = {}
}

// Function to populate form with contact data
const populateForm = (contact: Contact) => {
    form.value = {
        contactId: contact.id,
        first_name: contact.first_name || '',
        last_name: contact.last_name || '',
        email: contact.email || '',
        phone: contact.phone || '',
        price_range: Array.isArray(contact.price_range) ? contact.price_range : [],
        tags: Array.isArray(contact.tags) ? contact.tags : [],
        counties: Array.isArray(contact.counties) ? contact.counties : [],
        zip: Array.isArray(contact.zip) ? contact.zip : [],
        deal_type: Array.isArray(contact.deal_type) ? contact.deal_type : [],
        email_type: contact.email_type || ''
    }
}

// Watch for dialog open state and contact changes
watch([() => props.open, () => props.contact, () => props.isEditing], ([isOpen, contact, isEditing]) => {
    if (isOpen) {
        if (contact && isEditing) {
            populateForm(contact)
        } else {
            resetForm()
        }
    } else {
        resetForm()
    }
}, { immediate: true })
</script>


<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="max-h-[100dvh]">
            <DialogHeader>
                <DialogTitle>{{ isEditing ? 'Edit Contact' : 'Add New Contact' }}</DialogTitle>
                <DialogDescription>
                    {{ isEditing ? 'Update the contact information below.' : 'Fill in the contact information below.' }}
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="handleSubmit" class="">
                <div class="overflow-y-auto h-[calc(100dvh-200px)] space-y-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Basic Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="first_name">First Name</Label>
                                <Input id="first_name" v-model="form.first_name" placeholder="Enter first name"
                                    :class="{ 'border-red-500': errors.first_name }" />
                                <p v-if="errors.first_name" class="text-sm text-red-500">{{ errors.first_name }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="last_name">Last Name</Label>
                                <Input id="last_name" v-model="form.last_name" placeholder="Enter last name"
                                    :class="{ 'border-red-500': errors.last_name }" />
                                <p v-if="errors.last_name" class="text-sm text-red-500">{{ errors.last_name }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="email">Email Address</Label>
                            <Input id="email" v-model="form.email" type="email" placeholder="Enter email address"
                                :class="{ 'border-red-500': errors.email }" />
                            <p v-if="errors.email" class="text-sm text-red-500">{{ errors.email }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="phone">Phone Number</Label>
                            <Input type="tel" id="phone" v-model="form.phone" placeholder="Enter phone number"
                                :class="{ 'border-red-500': errors.phone }" />
                            <p v-if="errors.phone" class="text-sm text-red-500">{{ errors.phone }}</p>
                        </div>
                    </div>
                    <!-- Tags and Categories -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Tags and Categories</h3>

                        <div class="space-y-2">
                            <Label for="tags">Tags</Label>
                            <Combobox :allow-custom="true" v-model="form.tags" :option-values="tags"
                                placeholder="Select tags" />
                            <p v-if="errors.tags" class="text-sm text-red-500">{{ errors.tags }}</p>

                        </div>

                    </div>
                    <!-- Investment Preferences -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Investment Preferences</h3>

                        <div class="space-y-2">
                            <Label for="counties">Counties of Interest</Label>
                            <Combobox :allow-custom="true" v-model="form.counties" :option-values="counties"
                                placeholder="Select counties" />
                            <p v-if="errors.counties" class="text-sm text-red-500">{{ errors.counties }}</p>

                        </div>

                        <div class="space-y-2">
                            <Label for="zip_codes">Zip Codes of Interest</Label>

                            <Combobox :allow-custom="true" v-model="form.zip" :option-values="zipCodes"
                                placeholder="Select zip codes" />
                            <p v-if="errors.zip" class="text-sm text-red-500">{{ errors.zip }}</p>

                        </div>

                        <!-- <div class="space-y-2">
                        <Label for="price_range">Price Range</Label>
                        <Combobox :allow-custom="true" v-model="form.price_range" :option-values="priceRanges" placeholder="Select price ranges" />
                        <p v-if="errors.price_range" class="text-sm text-red-500">{{ errors.price_range }}</p>

                    </div> -->

                        <div class="space-y-2">
                            <Label for="deal_type">Deal Types</Label>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <CheckboxLoop v-model="form.deal_type" :value="'MLS'" id="deal_mls" />
                                    <Label for="deal_mls">MLS</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <CheckboxLoop v-model="form.deal_type" :value="'Wholesale'" id="deal_wholesale" />
                                    <Label for="deal_wholesale">Wholesale</Label>
                                </div>
                            </div>
                            <p v-if="errors.deal_type" class="text-sm text-red-500">{{ errors.deal_type }}</p>
                        </div>
                    </div>


                </div>
                <!-- Form Actions -->
                <DialogFooter class="flex gap-2">
                    <Button type="button" variant="outline" @click="$emit('update:open', false)">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <span v-if="isSubmitting">Saving...</span>
                        <span v-else>{{ isEditing ? 'Update Contact' : 'Add Contact' }}</span>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
