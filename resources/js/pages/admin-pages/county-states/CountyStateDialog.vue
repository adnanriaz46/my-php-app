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

const { showToast } = useToast();

const props = defineProps<{
    modelValue: boolean;
    countyStateId: number | null;
}>();

const emit = defineEmits(['update:modelValue']);

const open = ref(props.modelValue);
const loading = ref(false);
const countyState = ref<any>(null);

const stateOptions = [
    { key: 'AL', value: 'Alabama' },
    { key: 'AK', value: 'Alaska' },
    { key: 'AZ', value: 'Arizona' },
    { key: 'AR', value: 'Arkansas' },
    { key: 'CA', value: 'California' },
    { key: 'CO', value: 'Colorado' },
    { key: 'CT', value: 'Connecticut' },
    { key: 'DE', value: 'Delaware' },
    { key: 'FL', value: 'Florida' },
    { key: 'GA', value: 'Georgia' },
    { key: 'HI', value: 'Hawaii' },
    { key: 'ID', value: 'Idaho' },
    { key: 'IL', value: 'Illinois' },
    { key: 'IN', value: 'Indiana' },
    { key: 'IA', value: 'Iowa' },
    { key: 'KS', value: 'Kansas' },
    { key: 'KY', value: 'Kentucky' },
    { key: 'LA', value: 'Louisiana' },
    { key: 'ME', value: 'Maine' },
    { key: 'MD', value: 'Maryland' },
    { key: 'MA', value: 'Massachusetts' },
    { key: 'MI', value: 'Michigan' },
    { key: 'MN', value: 'Minnesota' },
    { key: 'MS', value: 'Mississippi' },
    { key: 'MO', value: 'Missouri' },
    { key: 'MT', value: 'Montana' },
    { key: 'NE', value: 'Nebraska' },
    { key: 'NV', value: 'Nevada' },
    { key: 'NH', value: 'New Hampshire' },
    { key: 'NJ', value: 'New Jersey' },
    { key: 'NM', value: 'New Mexico' },
    { key: 'NY', value: 'New York' },
    { key: 'NC', value: 'North Carolina' },
    { key: 'ND', value: 'North Dakota' },
    { key: 'OH', value: 'Ohio' },
    { key: 'OK', value: 'Oklahoma' },
    { key: 'OR', value: 'Oregon' },
    { key: 'PA', value: 'Pennsylvania' },
    { key: 'RI', value: 'Rhode Island' },
    { key: 'SC', value: 'South Carolina' },
    { key: 'SD', value: 'South Dakota' },
    { key: 'TN', value: 'Tennessee' },
    { key: 'TX', value: 'Texas' },
    { key: 'UT', value: 'Utah' },
    { key: 'VT', value: 'Vermont' },
    { key: 'VA', value: 'Virginia' },
    { key: 'WA', value: 'Washington' },
    { key: 'WV', value: 'West Virginia' },
    { key: 'WI', value: 'Wisconsin' },
    { key: 'WY', value: 'Wyoming' },
];

const form = useForm({
    county: '',
    display: '',
    fips: '',
    state: '',
    slug: '',
});

// Watch for changes in modelValue and handle dialog state
watch(() => props.modelValue, (val) => {
    open.value = val;
    // Handle data fetching when dialog opens
    if (val && props.countyStateId) {
        fetchCountyStateDetail(props.countyStateId);
    } else if (val && !props.countyStateId) {
        resetForm();
    }
}, { immediate: true });

// Watch for changes in open state and emit updates
watch(open, (val) => {
    emit('update:modelValue', val);
});

async function fetchCountyStateDetail(countyStateId: number) {
    try {
        loading.value = true;
        const response = await axios.get(route('admin.county-states.show', countyStateId));
        countyState.value = response.data.countyState;
        
        // Populate form with existing data
        form.county = countyState.value.county;
        form.display = countyState.value.display;
        form.fips = countyState.value.fips || '';
        form.state = countyState.value.state;
        form.slug = countyState.value.slug;
    } catch (error) {
        console.error('Error fetching county state:', error);
        showToast('Error loading county state details', 'error');
    } finally {
        loading.value = false;
    }
}

function resetForm() {
    form.reset();
    countyState.value = null;
}

function generateSlug() {
    if (form.county && form.state) {
        const countySlug = form.county.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        const stateSlug = form.state.toLowerCase();
        form.slug = `${countySlug}-${stateSlug}`;
    }
}

function submit() {
    if (props.countyStateId) {
        // Update existing county state
        form.put(route('admin.county-states.update', props.countyStateId), {
            onSuccess: () => {
                showToast('County state updated successfully', 'success');
                open.value = false;
                // router.visit(route('admin.county-states'));
            },
            onError: (errors) => {
                console.log('Update County State Error',errors);
                showToast('Error updating county state', 'error');
            }
        });
    } else {
        // Create new county state
        form.post(route('admin.county-states.store'), {
            onSuccess: () => {
                showToast('County state created successfully', 'success');
                open.value = false;
               // router.visit(route('admin.county-states'));
            },
            onError: (errors) => {
                console.log('Create County State Error',errors);
                showToast('Error creating county state', 'error');
            }
        });
    }
}

function deleteCountyState() {
    if (!props.countyStateId) return;
    
    if (confirm('Are you sure you want to delete this county state?')) {
        form.delete(route('admin.county-states.destroy', props.countyStateId), {
            onSuccess: () => {
                showToast('County state deleted successfully', 'success');
                open.value = false;
                // router.visit(route('admin.county-states'));
            },
            onError: (errors) => {
                console.log('Delete County State Error',errors);
                showToast('Error deleting county state', 'error');
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
                    {{ countyStateId ? 'Edit County State' : 'Add New County State' }}
                </DialogTitle>
            </DialogHeader>
            
            <div class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="county">County</Label>
                    <Input id="county" v-model="form.county" placeholder="Enter county name" @blur="generateSlug" />
                    <InputError :message="form.errors.county" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="display">Display Name <span class="text-xs text-muted-foreground">(County, ST)</span></Label>
                    <Input id="display" v-model="form.display" placeholder="Enter display name (Delaware, PA)" />
                    <InputError :message="form.errors.display" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="state">State</Label>
                    <SelectBox v-model="form.state" :options="stateOptions" placeholder="Select state" @update:modelValue="generateSlug" />
                    <InputError :message="form.errors.state" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="fips">FIPS Code</Label>
                    <Input id="fips" required v-model="form.fips" placeholder="Enter FIPS code" />
                    <InputError :message="form.errors.fips" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="slug">Slug</Label>
                    <Input id="slug" v-model="form.slug" placeholder="Enter slug or leave empty to auto-generate" />
                    <InputError :message="form.errors.slug" />
                </div>
            </div>
            
            <DialogFooter>
                <div class="flex justify-between w-full">
                    <Button v-if="countyStateId" @click="deleteCountyState" variant="destructive" :disabled="loading">
                        Delete
                    </Button>
                    <div class="flex gap-2">
                        <DialogClose as-child>
                            <Button variant="outline" :disabled="loading">Cancel</Button>
                        </DialogClose>
                        <Button @click="submit" :disabled="loading || form.processing">
                            {{ loading ? 'Loading...' : (countyStateId ? 'Update' : 'Create') }}
                        </Button>
                    </div>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    
    <Toast />
</template> 