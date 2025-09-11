<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import Button from '@/components/ui/button/Button.vue';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import InputError from '@/components/InputError.vue';
import { useToast } from "@/composables/useToast";
import Toast from '@/components/ui/toast/Toast.vue';
import axios from 'axios';
import TextArea from '@/components/ui/textarea/TextArea.vue';

const { showToast } = useToast();

const props = defineProps<{
    modelValue: boolean;
    testimonialId: number | null;
}>();

const emit = defineEmits(['update:modelValue']);

const open = ref(props.modelValue);
const loading = ref(false);
const testimonial = ref<any>(null);

const ratingOptions = [
    { key: 1, value: '1 Star' },
    { key: 2, value: '2 Stars' },
    { key: 3, value: '3 Stars' },
    { key: 4, value: '4 Stars' },
    { key: 5, value: '5 Stars' },
];

const form = useForm({
    description: '',
    email: '',
    name: '',
    profile_image: '',
    published_date: '',
    rate: 5,
    title: '',
});

// Watch for changes in modelValue and handle dialog state
watch(() => props.modelValue, (val) => {
    open.value = val;
}, { immediate: true });

watch(open, (val) => {
    emit('update:modelValue', val);
    if (val && props.testimonialId) {
        fetchTestimonialDetail(props.testimonialId);
    } else if (val && !props.testimonialId) {
        resetForm();
    }
}, { immediate: true });

async function fetchTestimonialDetail(testimonialId: number) {
    try {
        loading.value = true;
        const response = await axios.get(route('admin.testimonials.show', testimonialId));
        testimonial.value = response.data.testimonial;
        
        // Populate form with existing data
        form.description = testimonial.value.description;
        form.email = testimonial.value.email;
        form.name = testimonial.value.name;
        form.profile_image = testimonial.value.profile_image || '';
        form.published_date = testimonial.value.published_date ? testimonial.value.published_date.split('T')[0] : '';
        form.rate = testimonial.value.rate;
        form.title = testimonial.value.title;
    } catch (error) {
        console.error('Error fetching testimonial:', error);
        showToast('Error loading testimonial details', 'error');
    } finally {
        loading.value = false;
    }
}

function resetForm() {
    form.reset();
    form.rate = 5;
    form.published_date = '';
    testimonial.value = null;
}

function submit() {
    if (props.testimonialId) {
        // Update existing testimonial
        form.put(route('admin.testimonials.update', props.testimonialId), {
            onSuccess: () => {
                showToast('Testimonial updated successfully', 'success');
                open.value = false;
            },
            onError: (errors) => {
                console.log('Update Testimonial Error', errors);
                showToast('Error updating testimonial', 'error');
            }
        });
    } else {
        // Create new testimonial
        form.post(route('admin.testimonials.store'), {
            onSuccess: () => {
                showToast('Testimonial created successfully', 'success');
                open.value = false;
            },
            onError: (errors) => {
                console.log('Create New Testimonial Error', errors);
                showToast('Error creating testimonial', 'error');
            }
        });
    }
}

function deleteTestimonial() {
    if (!props.testimonialId) return;
    
    if (confirm('Are you sure you want to delete this testimonial?')) {
        form.delete(route('admin.testimonials.destroy', props.testimonialId), {
            onSuccess: () => {
                showToast('Testimonial deleted successfully', 'success');
                open.value = false;
            },
            onError: (errors) => {
                console.log('Delete Testimonial Error', errors);
                showToast('Error deleting testimonial', 'error');
            }
        });
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-[700px]">
            <DialogHeader>
                <DialogTitle>
                    {{ testimonialId ? 'Edit Testimonial' : 'Add New Testimonial' }}
                </DialogTitle>
            </DialogHeader>
            
            <div class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" v-model="form.name" placeholder="Enter customer name" />
                    <InputError :message="form.errors.name" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" placeholder="Enter customer email" />
                    <InputError :message="form.errors.email" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="title">Title (Optional)</Label>
                    <Input id="title" v-model="form.title" placeholder="Enter testimonial title" />
                    <InputError :message="form.errors.title" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <TextArea id="description" v-model="form.description" placeholder="Enter testimonial content" rows="4" />
                    <InputError :message="form.errors.description" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="rate">Rating</Label>
                    <SelectBox v-model="form.rate" :options="ratingOptions" placeholder="Select rating" />
                    <InputError :message="form.errors.rate" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="profile_image">Profile Image URL (Optional)</Label>
                    <Input id="profile_image" v-model="form.profile_image" placeholder="Enter profile image URL" />
                    <InputError :message="form.errors.profile_image" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="published_date">Published Date (Optional)</Label>
                    <Input id="published_date" v-model="form.published_date" type="date" />
                    <InputError :message="form.errors.published_date" />
                </div>
            </div>
            
            <DialogFooter>
                <div class="flex justify-between w-full">
                    <Button v-if="testimonialId" @click="deleteTestimonial" variant="destructive" :disabled="loading">
                        Delete
                    </Button>
                    <div class="flex gap-2">
                        <DialogClose as-child>
                            <Button variant="outline" :disabled="loading">Cancel</Button>
                        </DialogClose>
                        <Button @click="submit" :disabled="loading || form.processing">
                            {{ loading ? 'Loading...' : (testimonialId ? 'Update' : 'Create') }}
                        </Button>
                    </div>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    
    <Toast />
</template> 