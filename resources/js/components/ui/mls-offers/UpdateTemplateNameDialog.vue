<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-w-md bg-white dark:bg-gray-900">
            <DialogHeader>
                <DialogTitle>Update Template Name</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div>
                    <Label for="template_name" :line-break="true">Template Name
                        <Input 
                            id="template_name" 
                            v-model="form.template_name" 
                            placeholder="Enter new template name..." 
                            required
                        />
                        <InputError :message="form.errors.template_name" />
                    </Label>
                </div>
                <DialogFooter>
                    <Button type="button" variant="secondary" @click="emit('update:open', false)">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Icon icon="tabler:edit" class="mr-1" />
                        Update
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { watch } from 'vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Icon } from '@iconify/vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';

interface Props {
    open: boolean;
    template?: {
        id: number;
        template_name: string;
    } | null;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:open', 'updated']);

const form = useForm({
    template_name: '',
});

watch(() => props.open, (val) => {
    if (val && props.template) {
        form.template_name = props.template.template_name;
        form.clearErrors();
    }
});

function handleSubmit() {
    if (!form.template_name || !props.template) return;
    
    form.put(route('mls-offer.pdf-templates.update', props.template.id), {
        onSuccess: () => {
            emit('update:open', false);
            emit('updated');
        },
    });
}
</script> 