<script setup lang="ts">
import type {HTMLAttributes} from 'vue'
import {useVModel} from '@vueuse/core'
import {Icon} from '@iconify/vue'
import {NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput, NumberFieldRoot} from 'reka-ui'
import {computed, watch} from "vue";

const props = defineProps<{
  defaultValue?: number
  modelValue?: number | string | null
  min?: number,
  max?: number,
  step?: number | null | string,
  class?: HTMLAttributes['class'],
  type?: 'price' | 'percentage' | null,
  align?: 'left' | 'right' | null
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: number | null): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

watch(modelValue, (val) => {
  if (val === '' || val === null || val === undefined || isNaN(Number(val))) {
    emits('update:modelValue', null)
  } else {
    emits('update:modelValue', Number(val))
  }
})

const minValue = computed(() => props.min ?? 0)

</script>
<template>
  <NumberFieldRoot :step="step" :default-value="defaultValue" :min="minValue" :max="max" v-model="modelValue"
                    :class="props.class">
    <div
        class="mt-1 file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-2 py-1 text-base transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/10 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive shadow-sm focus-within:shadow-[0_0_0_1px] focus-within:shadow-stone-800">
      <NumberFieldDecrement class="p-0.5 disabled:opacity-20">
        <Icon icon="radix-icons:minus"/>
      </NumberFieldDecrement>
      <span v-if="type == 'price'" class="px-1 text-muted-foreground my-auto">$</span>
      <NumberFieldInput :class="align == 'left'? 'text-left' : align == 'right' ? 'text-right' : ''  "
                        class="bg-transparent w-full text-center focus:outline-0 p-1"/>
      <span v-if="type == 'percentage'" class="px-1 text-muted-foreground my-auto">%</span>
      <NumberFieldIncrement class="p-0.5 disabled:opacity-20">
        <Icon icon="radix-icons:plus"/>
      </NumberFieldIncrement>
    </div>
  </NumberFieldRoot>
</template>
