<script setup lang="ts">
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { UserReferralW9 } from "@/types";
import { ref, watch } from "vue";
import { ExternalLink, FileText, CheckCircle, Clock } from "lucide-vue-next";

const props = defineProps<{
  open: boolean;
  w9s: UserReferralW9[];
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const dialogOpen = ref<boolean>(props.open);

// Sync child state back to parent
watch(dialogOpen, (newVal) => {
  emit('update:open', newVal);
});

// Sync prop changes to local state
watch(() => props.open, (newVal) => {
  dialogOpen.value = newVal;
});

const getStatusIcon = (approved: boolean) => {
  if (approved) {
    return CheckCircle;
  }
  return Clock;
};

const getStatusColor = (approved: boolean) => {
  if (approved) {
    return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
  }
  return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
};

const getStatusText = (approved: boolean) => {
  if (approved) {
    return 'Approved';
  }
  return 'Pending Review';
};

const openFile = (fileUrl: string) => {
  window.open(fileUrl, '_blank');
};
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogContent class="max-w-4xl max-h-[80vh]">
      <DialogHeader class="space-y-3">
        <DialogTitle class="flex items-center gap-2">
          <FileText class="h-5 w-5" />
          W9 Documents
        </DialogTitle>
        <DialogDescription>
          View and manage your uploaded W9 tax documents
        </DialogDescription>
      </DialogHeader>
      
      <div class="w-full max-h-[60vh] overflow-y-auto">
        <div v-if="w9s.length === 0" class="text-center py-8 text-gray-500">
          <FileText class="h-12 w-12 mx-auto mb-4 text-gray-300" />
          <p>No W9 documents uploaded yet.</p>
        </div>
        
        <div v-else class="space-y-4">
          <div 
            v-for="w9 in w9s" 
            :key="w9.id" 
            class="border rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <FileText class="h-5 w-5 text-blue-500" />
                  <h3 class="font-medium text-gray-900 dark:text-gray-100">
                    {{ w9.name }}
                  </h3>
                  <Badge :class="getStatusColor(w9.approved)">
                    <component :is="getStatusIcon(w9.approved)" class="h-3 w-3 mr-1" />
                    {{ getStatusText(w9.approved) }}
                  </Badge>
                </div>
                
                <div v-if="w9.remarks" class="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-md">
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Remarks:</span> {{ w9.remarks }}
                  </p>
                </div>
                
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                  Uploaded on {{ new Date(w9.created_at).toLocaleDateString() }}
                </div>
              </div>
              
              <div class="flex items-center gap-2 ml-4">
                <Button 
                  variant="outline" 
                  size="sm"
                  @click="openFile(w9.file_url)"
                  class="flex items-center gap-1"
                >
                  <ExternalLink class="h-3 w-3" />
                  View
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <DialogFooter>
        <DialogClose as-child>
          <Button variant="secondary">
            Close
          </Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template> 