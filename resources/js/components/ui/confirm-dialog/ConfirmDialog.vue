<script setup lang="ts">
import {computed} from 'vue'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import Button from "@/components/ui/button/Button.vue"

const props = defineProps({
  dialog: {
    type: Object,
    required: true,
  }
})

// Make writable computed so `v-model:open` works properly
const isOpen = computed({
  get: () => props.dialog?.isOpen?.value ?? false,
  set: (val) => {
    if (props.dialog?.isOpen) {
      props.dialog.isOpen.value = val
    }
  }
})

const message = computed(() => props.dialog?.message?.value ?? '')

const confirmBtnTxt = computed(() => props.dialog?._confirmBtnTxt?.value ?? 'Confirm')

const confirm = () => props.dialog?.confirm?.()
const cancel = () => props.dialog?.cancel?.()
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Confirmation</DialogTitle>
        <DialogDescription>{{ message }}</DialogDescription>
      </DialogHeader>

      <div class="flex justify-end gap-2 mt-4">
        <Button variant="ghost" @click="cancel">Cancel</Button>
        <Button variant="default" @click="confirm">{{ confirmBtnTxt }}</Button>
      </div>
    </DialogContent>
  </Dialog>
</template>

