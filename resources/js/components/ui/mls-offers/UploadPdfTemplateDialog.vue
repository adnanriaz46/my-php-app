<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-w-md bg-white dark:bg-gray-900">
            <DialogHeader>
                <DialogTitle>Upload Template</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div>
                    <Label for="template_name" :line-break="true">Template Name
                        <Input id="template_name" v-model="form.template_name" placeholder="Type here..." required
                         />
                        <InputError :message="form.errors.template_name" />
                    </Label>
                </div>
                <div>
                    <Label for="pdf_file" :line-break="true">Template PDF File
                        <input id="pdf_file" type="file" accept="application/pdf" @change="onFileChange" required
                            class="block w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-gray-100" />
                        <InputError :message="form.errors.pdf_file" />
                    </Label>
                </div>
                <div class="text-xs text-muted-foreground dark:text-gray-400">
                    * The uploaded template will be processed to generate inputs for the fillable fields. If you have
                    additional details/requests regarding the generation, please provide them below.
                </div>
                <div>
                    <Label for="additional_requests" :line-break="true">Additional Request/Detail
                        <TextArea id="additional_requests" v-model="form.additional_requests" placeholder="Type here..."
                            class="w-full px-3 py-2"
                            rows="3" />
                        <InputError :message="form.errors.additional_requests" />
                    </Label>
                </div>
                <DialogFooter>
                    <Button type="submit" :disabled="form.processing">
                        <Icon icon="tabler:upload" class="mr-1" />
                        Upload
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
import TextArea from '../textarea/TextArea.vue';

const props = defineProps<{ open: boolean }>();
const emit = defineEmits(['update:open', 'uploaded']);

const form = useForm({
    template_name: '',
    pdf_file: null as File | null,
    additional_requests: '',
});

watch(() => props.open, (val) => {
    if (val) resetForm();
});

function resetForm() {
    form.reset();
    form.clearErrors();
}

function onFileChange(e: Event) {
    const files = (e.target as HTMLInputElement).files;
    form.pdf_file = files && files[0] ? files[0] : null;
}

function handleSubmit() {
    if (!form.template_name || !form.pdf_file) return;
    form.post(route('mls-offer.pdf-templates.store'), {
        forceFormData: true,
        onSuccess: () => {
            emit('update:open', false);
            emit('uploaded');
        },
    });
}
</script>