<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import SelectBox from '@/components/ui/select-box/SelectBox.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Button from '@/components/ui/button/Button.vue';
import { Label } from '@/components/ui/label';
import InputNumber from '@/components/ui/input-number/InputNumber.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { UserTypes } from '@/types/userTypes';
import InputError from '@/components/InputError.vue';
import { useToast } from "@/composables/useToast";
import Toast from '@/components/ui/toast/Toast.vue';
import axios from 'axios';

const { showToast } = useToast();

const props = defineProps<{
    modelValue: boolean;
    featureId: number | null;
}>();

const emit = defineEmits(['update:modelValue']);

const open = ref(props.modelValue);
const loading = ref(false);
const feature = ref<any>(null);

const userTypeOptions = [
    { key: UserTypes.FREE, value: 'Free' },
    { key: UserTypes.PREMIUM, value: 'Premium' },
    { key: UserTypes.ADMIN, value: 'Admin' },
];

const form = useForm({
    feature: '',
    description: '',
    group: UserTypes.FREE,
    order: 1,
    no_access: false,
});

// Watch for changes in modelValue and handle dialog state
watch(() => props.modelValue, (val) => {
    open.value = val;
}, { immediate: true });

watch(open, (val) => {
    emit('update:modelValue', val);
    if (val && props.featureId) {
        fetchFeatureDetail(props.featureId);
    } else if (val && !props.featureId) {
        resetForm();
    }
}, { immediate: true });

async function fetchFeatureDetail(featureId: number) {
    try {
        loading.value = true;
        const response = await axios.get(route('admin.upgrade-features.show', featureId));
        feature.value = response.data.feature;
        
        // Populate form with existing data
        form.feature = feature.value.feature;
        form.description = feature.value.description;
        form.group = feature.value.group;
        form.order = feature.value.order;
        form.no_access = feature.value.no_access;
    } catch (error) {
        console.error('Error fetching feature:', error);
        showToast('Error loading feature details', 'error');
    } finally {
        loading.value = false;
    }
}

function resetForm() {
    form.reset();
    form.group = UserTypes.FREE;
    form.order = 1;
    form.no_access = false;
    feature.value = null;
}

function submit() {
    if (props.featureId) {
        // Update existing feature
        form.put(route('admin.upgrade-features.update', props.featureId), {
            onSuccess: () => {
                showToast('Feature updated successfully', 'success');
                open.value = false;
                // Refresh the page to update the list
                // router.visit(route('admin.upgrade-features'));
            },
            onError: (errors) => {
                console.log('Update Feature Error',errors);
                showToast('Error updating feature', 'error');
            }
        });
    } else {
        // Create new feature
        form.post(route('admin.upgrade-features.store'), {
            onSuccess: () => {
                showToast('Feature created successfully', 'success');
                open.value = false;
                // Refresh the page to update the list
                // router.visit(route('admin.upgrade-features'));
            },
            onError: (errors) => {
                console.log('Create New Feature Error',errors);
                showToast('Error creating feature', 'error');
            }
        });
    }
}

function deleteFeature() {
    if (!props.featureId) return;
    
    if (confirm('Are you sure you want to delete this feature?')) {
        form.delete(route('admin.upgrade-features.destroy', props.featureId), {
            onSuccess: () => {
                showToast('Feature deleted successfully', 'success');
                open.value = false;
                // Refresh the page to update the list
                // router.visit(route('admin.upgrade-features'));
            },
            onError: (errors) => {
                console.log('Delete Feature Error',errors);
                showToast('Error deleting feature', 'error');
            }
        });
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-[600px]">
            <DialogHeader>
                <DialogTitle>
                    {{ featureId ? 'Edit Upgrade Feature' : 'Add New Upgrade Feature' }}
                </DialogTitle>
            </DialogHeader>
            
            <div class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="feature">Feature Name</Label>
                    <Input id="feature" v-model="form.feature" placeholder="Enter feature name" />
                    <InputError :message="form.errors.feature" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Input id="description" v-model="form.description" placeholder="Enter feature description" />
                    <InputError :message="form.errors.description" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="group">User Type</Label>
                    <SelectBox v-model="form.group" :options="userTypeOptions" placeholder="Select user type" />
                    <InputError :message="form.errors.group" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="order">Order</Label>
                    <InputNumber id="order" v-model="form.order" placeholder="Enter display order" />
                    <InputError :message="form.errors.order" />
                </div>
                
                <div class="flex items-center space-x-2">
                    <Checkbox id="no_access" v-model="form.no_access" />
                    <Label for="no_access">No Access (Feature is restricted)</Label>
                </div>
                <InputError :message="form.errors.no_access" />
            </div>
            
            <DialogFooter>
                <div class="flex justify-between w-full">
                    <Button v-if="featureId" @click="deleteFeature" variant="destructive" :disabled="loading">
                        Delete
                    </Button>
                    <div class="flex gap-2">
                        <DialogClose as-child>
                            <Button variant="outline" :disabled="loading">Cancel</Button>
                        </DialogClose>
                        <Button @click="submit" :disabled="loading || form.processing">
                            {{ loading ? 'Loading...' : (featureId ? 'Update' : 'Create') }}
                        </Button>
                    </div>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    
    <Toast />
</template> 