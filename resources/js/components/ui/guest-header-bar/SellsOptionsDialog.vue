<script setup lang="ts">
import { ref, watch } from 'vue';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Icon } from '@iconify/vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
  open: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const dialogOpen = ref<boolean>(props.open);
const selectedOption = ref<string>('cash_offer');

// Sync child state back to parent
watch(dialogOpen, (newVal) => {
  emit('update:open', newVal);
});

// Sync prop changes to local state
watch(() => props.open, (newVal) => {
  dialogOpen.value = newVal;
});

const options = [
  {
    label: 'I want a cash offer on my house now.',
    value: 'cash_offer',
    icon: 'tabler:cash',
    description: 'Get a quick cash offer for your property'
  },
  {
    label: 'I want to list and market a property I own or have equitable interest in.',
    value: 'list_market',
    icon: 'tabler:home-share',
    description: 'List your property for sale with marketing support'
  }
];

const selectOption = (value: string) => {
  selectedOption.value = value;
};

const handleSubmit = () => {
  // Handle the form submission based on selected option
  console.log('Selected option:', selectedOption.value);

  // Close the dialog
  dialogOpen.value = false;

  // You can add navigation logic here based on the selection
  if (selectedOption.value === 'cash_offer') {
    window.open('https://www.revamp365.net/home43487624', '_blank');
  } else {
    router.visit(route('my_properties'));
  }
};
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogContent class="sm:max-w-md">
      <DialogHeader class="space-y-3">
        <DialogTitle class="text-center text-2xl">Sell Your Property</DialogTitle>
      </DialogHeader>

      <div class="w-full p-4">
        <div class="space-y-4">
          <div v-for="option in options" :key="option.value" @click="selectOption(option.value)"
            class="flex items-start gap-4 p-4 rounded-lg border-2 cursor-pointer transition-all duration-200 hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/10"
            :class="{
              'border-amber-500 bg-amber-50 dark:bg-amber-900/20': selectedOption === option.value,
              'border-gray-200 dark:border-gray-700': selectedOption !== option.value
            }">
            <!-- Selection indicator -->
            <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 mt-1 transition-all duration-200"
              :class="{
                'border-amber-500 bg-amber-500': selectedOption === option.value,
                'border-gray-300 dark:border-gray-600': selectedOption !== option.value
              }">
              <div v-if="selectedOption === option.value" class="w-2.5 h-2.5 rounded-full bg-white"></div>
            </div>

            <!-- Icon and content -->
            <div class="flex-1">
              <div class="flex items-start gap-3 mb-2">
                <Icon :icon="option.icon" class="size-6 text-amber-600 dark:text-amber-400 min-w-6 min-h-6" />
                <h3 class="font-medium text-gray-900 dark:text-white">
                  {{ option.label }}
                </h3>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-400 ml-9">
                {{ option.description }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex justify-center pt-6">
          <Button @click="handleSubmit"
            class="w-full max-w-xs bg-amber-500 hover:bg-amber-600 text-black font-semibold py-3 px-8 rounded-lg transition-colors">
            Submit
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>

<!-- <style scoped>
/* Custom styles for the radio buttons to match the design */
:deep(.radio-group) {
  @apply space-y-4;
}

:deep(.radio-item) {
  @apply flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer;
}

:deep(.radio-item[data-state="checked"]) {
  @apply border-amber-500 bg-amber-50 dark:bg-amber-900/20;
}

:deep(.radio-indicator) {
  @apply flex items-center justify-center w-5 h-5 rounded-full border-2 border-gray-300 data-[state=checked]:border-amber-500 data-[state=checked]:bg-amber-500;
}

:deep(.radio-indicator::after) {
  @apply content-[''] block w-2.5 h-2.5 rounded-full bg-white;
}
</style>  -->