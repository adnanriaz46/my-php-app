<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/composables/useToast';
import { Icon } from '@iconify/vue';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import TextStyle from '@tiptap/extension-text-style';
import Color from '@tiptap/extension-color';
import axios from 'axios';

const { showToast } = useToast();

interface Props {
    open: boolean;
    recipientEmail: string;
    recipientName: string;
    defaultSubject: string;
    defaultContent: string;
    requestType: 'showing' | 'question' | 'offer' | 'contact';
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:open': [value: boolean];
    'email-sent': [];
}>();

const isLoading = ref(false);
const subject = ref(props.defaultSubject);
const content = ref(props.defaultContent);

const editor = useEditor({
    extensions: [
        StarterKit.configure({
            bulletList: false,
            orderedList: false,
            listItem: false,
        }),
        Underline,
        TextStyle,
        Color
    ],
    content: '',
});

const applyColor = (color: string) => {
    editor.value?.chain().focus().setColor(color).run();
};

// Watch for editor content changes
watch(
    () => editor.value?.getHTML(),
    (newHTML) => {
        if (newHTML) {
            content.value = newHTML;
        }
    }, 
    { immediate: true }
);

// Watch for dialog open to initialize content
watch(() => props.open, (isOpen) => {
    if (isOpen) {
        nextTick(() => {
            editor.value?.commands.setContent(props.defaultContent);
        });
    }
}, { immediate: true });

const sendEmail = async () => {
    if (!subject.value.trim() || !content.value.trim()) {
        showToast('Error', 'Please fill in both subject and content', 'error');
        return;
    }

    isLoading.value = true;
    try {
        const response = await axios.post(route('admin.requests.send-email'), {
            recipient_email: props.recipientEmail,
            recipient_name: props.recipientName,
            subject: subject.value,
            content: content.value,
            request_type: props.requestType,
        });

        if (response.data.success) {
            showToast('Success', 'Email sent successfully!', 'success');
            emit('email-sent');
            emit('update:open', false);
        } else {
            showToast('Error', response.data.message || 'Failed to send email', 'error');
        }
    } catch (error: any) {
        console.error('Email send error:', error);
        if (error.response?.data?.message) {
            showToast('Error', error.response.data.message, 'error');
        } else {
            showToast('Error', 'Failed to send email', 'error');
        }
    } finally {
        isLoading.value = false;
    }
};

const closeDialog = () => {
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="closeDialog">
        <DialogContent class="max-w-4xl max-h-[90vh]">
            <DialogHeader>
                <DialogTitle>Reply by Email</DialogTitle>
                <DialogDescription>
                    Send a reply email to {{ recipientName }} ({{ recipientEmail }})
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-6 max-h-[calc(90vh-200px)] overflow-y-auto">
                <!-- Recipient Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label>To:</Label>
                        <Input :model-value="recipientEmail" disabled class="bg-gray-50 dark:bg-gray-800" />
                    </div>
                    <div class="space-y-2">
                        <Label>Recipient Name:</Label>
                        <Input :model-value="recipientName" disabled class="bg-gray-50 dark:bg-gray-800" />
                    </div>
                </div>

                <!-- Subject -->
                <div class="space-y-2">
                    <Label for="email-subject">Subject:</Label>
                    <Input 
                        id="email-subject" 
                        v-model="subject" 
                        placeholder="Enter email subject"
                        class="w-full"
                    />
                </div>

                <!-- Content Editor -->
                <div class="space-y-2">
                    <Label>Message Content:</Label>
                    <div class="border rounded-xl p-1 bg-white dark:bg-stone-900 shadow-sm">
                        <!-- Toolbar -->
                        <div class="flex flex-wrap gap-2 items-center bg-stone-100 dark:bg-stone-800 p-2 rounded-xl shadow-sm">
                            <button 
                                @click="editor?.chain().focus().toggleBold().run()"
                                :class="editor?.isActive('bold') ? 'bg-stone-700 text-white' : 'bg-white text-stone-700'"
                                class="px-3 py-1 rounded text-sm font-medium shadow-sm"
                            >
                                B
                            </button>

                            <button 
                                @click="editor?.chain().focus().toggleItalic().run()"
                                :class="editor?.isActive('italic') ? 'bg-stone-700 text-white' : 'bg-white text-stone-700'"
                                class="px-3 py-1 rounded text-sm font-medium italic shadow-sm"
                            >
                                I
                            </button>

                            <button 
                                @click="editor?.chain().focus().toggleUnderline().run()"
                                :class="editor?.isActive('underline') ? 'bg-stone-700 text-white' : 'bg-white text-stone-700'"
                                class="px-3 py-1 rounded text-sm font-medium underline shadow-sm"
                            >
                                U
                            </button>

                            <input 
                                type="color"
                                @input="e => applyColor((e.target as HTMLInputElement)?.value)"
                                class="ml-4 w-6 h-6 cursor-pointer border-none bg-transparent" 
                            />
                        </div>
                        
                        <EditorContent 
                            :editor="editor"
                            class="text-sm prose dark:prose-invert max-w-none border p-4 rounded-xl bg-white dark:bg-stone-900 min-h-[200px]" 
                        />
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button variant="outline" @click="closeDialog" :disabled="isLoading">
                    Cancel
                </Button>
                <Button @click="sendEmail" :disabled="isLoading">
                    <Icon v-if="isLoading" icon="tabler:loader-2" class="mr-2 animate-spin" />
                    <Icon v-else icon="tabler:send" class="mr-2" />
                    Send Email
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 