<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-w-md bg-white dark:bg-gray-900">
            <DialogHeader>
                <DialogTitle>{{ template ? 'Edit' : 'Add' }} Email Template</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div>
                    <Label :line-break="true" for="name">Template Name
                        <Input id="name" v-model="form.name" placeholder="Name" required
                            class="dark:bg-gray-800 dark:text-gray-100" />
                        <InputError :message="form.errors.name" />
                    </Label>
                </div>
                <div>
                    <Label :line-break="true" for="tags">Tags
                        <DropdownMenu>
                            <DropdownMenuTrigger>
                                <Button>
                                    <Icon icon="tabler:tag" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent>
                                <DropdownMenuItem v-for="tag in props.tags" :key="tag" @click="copyTag(tag)">
                                    <div class="flex items-center gap-2">
                                        <Icon icon="tabler:copy" />
                                        {{ tag }}
                                        <Icon v-if="copied" icon="tabler:check" />
                                    </div>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </Label>
                </div>
                <div>
                    <Label :line-break="true" for="subject">Email Subject <span class="text-xs">(Tags enabled)</span>
                        <Input id="subject" v-model="form.subject" placeholder="Test subject {{Property MLS Number}}"
                            required class="dark:bg-gray-800 dark:text-gray-100" />
                        <InputError :message="form.errors.subject" />
                    </Label>
                </div>
                <div>
                    <Label :line-break="true" for="body">Email Body <span class="text-xs">(Tags enabled)</span>
                        <TextArea id="body" v-model="form.body" placeholder="Dear {{Property Listing Agent Name}},..."
                            rows="6" class="w-full px-3 py-2 dark:bg-gray-800 dark:text-gray-100" required />
                        <InputError :message="form.errors.body" />
                    </Label>
                </div>
                <DialogFooter>
                    <Button type="submit" :disabled="form.processing">
                        <Icon icon="tabler:device-floppy" class="mr-1" />
                        Save
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { watch, computed, ref } from 'vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Icon } from '@iconify/vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextArea from '../textarea/TextArea.vue';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '../dropdown-menu';
import { useClipboardCopy } from '@/composables/useCopy';

const { handleCopy, copied } = useClipboardCopy()

const props = defineProps<{ open: boolean, template?: any | null, tags: string[] }>();
const emit = defineEmits(['update:open', 'saved']);

const form = useForm({
    name: '',
    subject: '',
    body: ''
});

watch(() => props.open, (val) => {
    if (val) {
        if (props.template) {
            form.name = props.template.name;
            form.subject = props.template.subject;
            form.body = props.template.body;

        } else {
            form.reset();
            form.clearErrors();
        }
    }
});

function copyTag(tag: string) {
    handleCopy(`{{${tag}}}`);
}

function handleSubmit() {
    if (props.template) {
        form.put(route('mls-offer.email-templates.update', props.template.id), {
            onSuccess: () => {
                emit('update:open', false);
                emit('saved');
            },
        });
    } else {
        form.post(route('mls-offer.email-templates.store'), {
            onSuccess: () => {
                emit('update:open', false);
                emit('saved');
            },
        });
    }
}
</script>