<script setup lang="ts">
import {RadioGroupRoot, RadioGroupItem, RadioGroupIndicator} from 'reka-ui'
import {computed} from "vue";

// Props
defineProps<{
  modelValue: string | number | null
  options: { label: string; value: string; id?: string }[]
  name?: string
}>()

const emit = defineEmits(['update:modelValue'])

// ✅ Writable computed to bridge v-model
const model = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

function getItemId(index: number, option: any) {
  return option.id ?? `radio-${index}`
}
</script>

<template>
  <RadioGroupRoot
      v-model="modelValue"
      @update:modelValue="val => emit('update:modelValue', val)"
      class="flex flex-col gap-2.5"
      :aria-label="name ?? 'Radio Group'"
  >
    <div
        v-for="(option, index) in options"
        :key="option.value"
        class="flex items-center"
    >
      <RadioGroupItem
          :id="getItemId(index, option)"
          class="bg-white w-[1.125rem] h-[1.125rem] rounded-full border data-[active=true]:border-stone-700 data-[active=true]:bg-stone-700 dark:data-[active=true]:bg-white shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-stone-700 outline-none cursor-pointer"
          :value="option.value"
      >
        <RadioGroupIndicator
            class="flex items-center justify-center w-full h-full relative after:content-[''] after:block after:w-2 after:h-2 after:rounded-[50%] after:bg-white dark:after:bg-stone-700"
        />
      </RadioGroupItem>
      <label
          class="text-stone-700 dark:text-white text-sm leading-none pl-[15px] cursor-pointer"
          :for="getItemId(index, option)"
      >
        {{ option.label }}
      </label>
    </div>
  </RadioGroupRoot>
</template>
