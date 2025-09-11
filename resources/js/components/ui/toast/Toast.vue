<script setup lang="ts">
import {ToastProvider, ToastRoot, ToastTitle, ToastDescription, ToastViewport, ToastClose} from 'reka-ui'
import {useToast} from '@/composables/useToast'

const {open, title, message, type, duration} = useToast()

const a = ()=> {
  open.value = false
}

</script>

<template>
  <ToastProvider :duration="duration">
    <ToastRoot @click="a"
        v-model:open="open"
        :class="[
                'rounded-lg shadow-sm border p-4 flex flex-col gap-1 transition-all ',
                type === 'success' && 'bg-green-50 border-green-300 text-green-800',
                type === 'error' && 'bg-red-50 border-red-300 text-red-800',
                type === 'info' && 'bg-blue-50 border-blue-300 text-blue-800',
                type === 'warning' && 'bg-yellow-50 border-yellow-300 text-yellow-800'
            ]"
    >
      <ToastTitle class="font-semibold text-sm">
        {{ title }}
      </ToastTitle>
      <ToastDescription class="text-xs">
        {{ message }}
      </ToastDescription>
    </ToastRoot>
    <ToastViewport class="fixed top-0 right-0 flex flex-col p-6 gap-3 w-[360px] max-w-full z-[999999]"/>
  </ToastProvider>
</template>
