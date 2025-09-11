<script setup lang="ts">


import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter, DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

import { usePage } from "@inertiajs/vue3";
import { nextTick, readonly, ref, watch } from "vue";
import { useToast } from '@/composables/useToast';
import { DBApiAverageCompsProperty, DBApiCalValData } from "@/types/DBApi";
import { GetInAppData } from "@/types";

import { useStorage } from '@vueuse/core'
import axios from 'axios'
import { Icon } from '@iconify/vue';
import { getAskAiProperty } from "@/lib/DBApiUtil";
import { getAskAiPropertySettingsMessage, getAskAiUnlistedPropertyDetail } from "@/lib/DataUtil";
import Markdown from "@/components/ui/markdown/Markdown.vue";
import { Tooltip, TooltipContent, TooltipTrigger } from "@/components/ui/tooltip";
import cloneDeep from 'lodash.clonedeep'
import { getZillowAIMessage } from "@/components/property-dialogs";
import { ZillowPListingMain } from "@/lib/zilowAndlocationUtil";

const { showToast } = useToast()

const page = usePage();
const input = ref('')
const loading = ref(false)
const generating = ref<boolean>(false);
const initial = ref<boolean>(true);


const props = defineProps<{
  listingType: "Listed" | "Unlisted" | "Other";
  propertyId: number | string;
  propertyAddress: string;
  // propertyData: DBApiPropertyFull;
  calculatedData?: DBApiCalValData | null
  averageCompsData?: DBApiAverageCompsProperty | null
  open: boolean;
  previousId: number | string | null;
  aiInitialMessagePrompt?: string;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
  (e: 'update:previous-id', value: number | string | null): void
}>();

const dialogOpen = ref<boolean>(props.open);

// const user = page.props.auth.user as User;
const propertyId = props.propertyId;
const propertyAddress = props.propertyAddress;

const chatKey = ref<string>(propertyId + "_" + props.listingType)

const initialMessage = readonly([
  { role: 'system', content: 'Hi, Ask me about the property', hidden: false }
]);

const sampleMessages = [
  'How much cash flow could this generate as a rental?',
  'How much would this house cost to renovate?',
  'What\'s the least amount I could put on a down payment on this property?'
]

const messages = useStorage('chat-messages_' + chatKey.value, cloneDeep(initialMessage)) as any
const clearChat = async (newId: number | string | null) => {
  initial.value = true;
  emit('update:previous-id', newId);
  messages.value = [];
  messages.value = cloneDeep(initialMessage);
  await initialDataLoad();
}


const initialDataLoad = async () => {
  const iniSettingData = await getAskAiPropertySettingsMessage(props.listingType);
  let data = { error: null, data: null };
  if (props.listingType == 'Listed') {
    data = await getAskAiProperty({ id: propertyId });
  } else if (props.listingType == 'Unlisted') {
    const rawData: GetInAppData<ZillowPListingMain> = await getAskAiUnlistedPropertyDetail(propertyAddress);
    data.data = getZillowAIMessage(rawData.data, props.calculatedData, props.averageCompsData);
  } else {
    data.data = props.aiInitialMessagePrompt ?? '';
  }
  if (data.error) {
    messages.value.push({ role: 'assistant', content: 'Error communicating with server.', hidden: false })
    return;
  }
  messages.value.push({ role: 'user', content: iniSettingData.data + " \n" + data.data, hidden: true })
}

const sendMessage = async () => {
  if (!input.value.trim()) return

  initial.value = false;
  messages.value.push({ role: 'user', content: input.value, hidden: false })
  input.value = ''
  loading.value = true
  generating.value = true;
  await nextTick();
  scrollToBottom();

  try {
    // Add empty assistant message to start streaming
    const assistantMessageIndex = messages.value.length;
    messages.value.push({ role: 'assistant', content: '', hidden: false })

    // Get CSRF token from page props or meta tag
    const csrfToken = page.props.csrf_token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
    // console.log('Page props CSRF:', page.props.csrf_token ? 'Found' : 'Not found');
    // console.log('Meta tag CSRF:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ? 'Found' : 'Not found');

    // Prepare headers
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'text/plain',
    };

    // Add CSRF token if available
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }

    // Make streaming request
    const response = await fetch(route('property.request.ask_ai'), {
      method: 'POST',
      headers,
      body: JSON.stringify({
        messages: messages.value.slice(0, -1), // Exclude the empty assistant message
        stream: true
      })
    });

    if (!response.ok) {
      const errorText = await response.text();
      let errorMessage = `HTTP error! status: ${response.status}`;

      try {
        const errorData = JSON.parse(errorText);
        if (errorData.error) {
          errorMessage = errorData.error;
        }
      } catch (e) {
        console.log("AI - Parsing error:", e);
        if (errorText) {
          errorMessage = errorText;
        }
      }

      throw new Error(errorMessage);
    }

    const reader = response.body?.getReader();
    if (!reader) {
      throw new Error('No reader available');
    }

    const decoder = new TextDecoder();
    let buffer = '';

    while (true) {
      const { done, value } = await reader.read();

      if (done) break;

      buffer += decoder.decode(value, { stream: true });
      const lines = buffer.split('\n');
      buffer = lines.pop() || ''; // Keep incomplete line in buffer

      for (const line of lines) {
        if (line.startsWith('data: ')) {
          const data = line.slice(6);

          if (data === '[DONE]') {
            generating.value = false;
            return;
          }

          try {
            const parsed = JSON.parse(data);
            if (parsed.choices && parsed.choices[0]?.delta?.content) {
              const content = parsed.choices[0].delta.content;
              messages.value[assistantMessageIndex].content += content;
              await nextTick();
              scrollToBottom();
            }
          } catch (e) {
            console.log("AI - Parsing error:", e);
            // Ignore parsing errors for incomplete JSON
          }
        }
      }
    }
  } catch (err) {
    console.log("ERR:", err);

    // Remove the empty assistant message if it exists
    if (messages.value.length > 0 && messages.value[messages.value.length - 1].role === 'assistant' && messages.value[messages.value.length - 1].content === '') {
      messages.value.pop();
    }

    // Try fallback to non-streaming request
    try {
      console.log("Trying fallback to non-streaming request...");
      const fallbackResponse = await axios.post(route('property.request.ask_ai'), {
        messages: messages.value,
        stream: false
      });
      const reply = fallbackResponse.data.choices[0].message;
      messages.value.push(reply);
    } catch (fallbackErr) {
      console.log("Fallback also failed:", fallbackErr);
      messages.value.push({ role: 'assistant', content: 'Error communicating with server. Please try again.', hidden: false });
    }
  } finally {
    loading.value = false
    generating.value = false
  }
  await nextTick();
  scrollToBottom();
}


watch(() => page.props?.success, (msg) => {
  if (msg) {
    showToast('Share Property', msg, 'success')
  }
}, { deep: true });


watch(dialogOpen, (newVal) => {
  emit('update:open', newVal);
});

// Sync prop changes to local state
watch(() => props.open, (newVal) => {
  dialogOpen.value = newVal;
});

watch(
  () => props.propertyId,
  async (newId) => {
    const chatKey = newId + "_" + props.listingType;
    if (chatKey !== props.previousId) {
      loading.value = true
      await clearChat(chatKey)
      loading.value = false
    } else {
      if (messages.value.length > 2) {
        initial.value = false;
      }
    }
  },
  { immediate: true }
)
const messagesContainer = ref<HTMLElement | null>(null)

const scrollToBottom = () => {
  const el = messagesContainer.value
  if (el) {
    el.scrollTop = el.scrollHeight
  }
}

const onClearChat = () => {
  loading.value = true;
  clearChat(null);
  loading.value = false;
}
const setToInput = (message: string) => {
  input.value = message;

}

</script>
<template>
  <Dialog v-model:open="dialogOpen">
    <DialogContent>
      <DialogHeader class="space-y-3">
        <DialogTitle class="flex justify-between">Ask Ai About The Property
          <Tooltip>
            <TooltipTrigger>
              <Icon @click="onClearChat()" icon="tabler:restore" class="my-auto mr-5" />
            </TooltipTrigger>
            <TooltipContent side="bottom" align="end">
              Clear Chat
            </TooltipContent>
          </Tooltip>
        </DialogTitle>
        <DialogDescription>
          {{ propertyAddress }}
        </DialogDescription>
      </DialogHeader>
      <div class="w-full grid grid-cols-1 justify-center gap-4 max-h-[calc(100dvh-200px)] overflow-y-auto"
        ref="messagesContainer">
        <div class="grid grid-cols-1">

          <div v-if="initial" class="mx-auto flex flex-col items-center text-sm justify-center text-center ">
            <Icon class="text-primary-strong size-8" icon="tabler:bulb" />
            <div class="text-base">Examples</div>
            <div v-for="(sMsg, index) in sampleMessages" :key="index" @click="setToInput(sMsg)"
              class="my-2 cursor-pointer rounded-lg px-3 py-2 bg-input border w-fit text-center hover:bg-primary/50">
              {{ sMsg }}
            </div>
          </div>

          <div v-for="(msg, index) in messages" :key="index" class="text-sm">
            <template v-if="!msg.hidden">
              <div v-if="msg.role == 'user'" class="px-3 py-2 bg-primary/20 border rounded-lg w-fit ml-auto mt-3">
                <p>{{ msg.content }}</p>
              </div>
              <div v-if="['system', 'assistant'].includes(msg.role)" class="px-3 py-2 border-1 bg-input/30 rounded-lg  mt-3">
                <Markdown :content="msg.content" />
              </div>
            </template>
          </div>
          <div v-if="generating" class="text-sm px-3 py-1 mt-1 bg-input/30 rounded-lg w-fit animate-pulse">
            <p><Icon icon="svg-spinners:3-dots-scale" width="24" height="24" /></p>
          </div>
        </div>


      </div>
      <DialogFooter class="gap-2">
        <div class="flex items-center space-x-2 w-full relative">
          <Input v-model="input" @keydown.enter="sendMessage" placeholder="Type your message..." class="pr-8" />
          <Button :disabled="!input || loading" @click="sendMessage" variant="ghost" class="absolute right-2">
            <Icon icon="tabler:send" />
          </Button>
        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped></style>
