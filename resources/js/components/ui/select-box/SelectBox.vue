<script setup lang="ts">
import {
  SelectRoot,
  SelectTrigger,
  SelectValue,
  SelectPortal,
  SelectContent,
  SelectScrollUpButton,
  SelectViewport,
  SelectGroup,
  SelectItem,
  SelectItemIndicator,
  SelectItemText,
  SelectScrollDownButton
} from 'reka-ui'
import { Icon } from '@iconify/vue'
import { computed, ref, watch } from 'vue'
import { SelectBoxOption } from "@/types/globals";
import { cn } from '@/lib/utils';

const props = defineProps<{
  modelValue: string | number | null
  options: SelectBoxOption[]
  placeholder?: string
  ariaLabel?: string
  notAllowEmptyInList?: boolean
  class?: string
  size?: 'xs' | 'sm' | 'md' | 'lg'
}>()

const emit = defineEmits(['update:modelValue'])
const allowEmpty = ref<boolean>(!props.notAllowEmptyInList);
const selected = ref(String(props.modelValue ?? ''))

watch(selected, (val) => {
  emit('update:modelValue', val === 'none' ? null : val)
}, { deep: true })

watch(() => props.modelValue, (val) => {
  selected.value = val ?? 'none'
})

const formattedOptions = computed(() =>
  props.options.map(option => ({
    ...option,
    key: String(option.key)
  }))
);

</script>
<template>
  <div class="w-full">
    <SelectRoot v-model="selected">
      <SelectTrigger
        class="order-1 md:order-2 inline-flex w-full items-center justify-between rounded-lg px-[15px] text-sm leading-none h-[38px] gap-[5px] border dark:bg-input/30 border-input  shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-black data-[placeholder]:text-green9 outline-none  cursor-pointer"
        :class="cn(props.class, props.size == 'xs' ? 'md:max-w-[80px]' : props.size == 'sm' ? 'md:max-w-[120px]' : props.size == 'md' ? 'md:max-w-[160px]' : 'md:min-w-[160px]')"
        :aria-label="ariaLabel || 'Custom select'">
        <SelectValue :placeholder="placeholder || 'Select an option'" />
        <Icon icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
      </SelectTrigger>

      <SelectPortal>
        <SelectContent class="min-w-[160px] bg-white dark:bg-gray-900 rounded-lg border shadow-sm z-[100]"
          :class="cn(props.size == 'xs' ? 'max-w-[80px]' : props.size == 'sm' ? 'max-w-[120px]' : props.size == 'md' ? 'max-w-[160px]' : 'md:min-w-[160px]')"
          :side-offset="5">
          <SelectScrollUpButton
            class="flex items-center justify-center h-[25px] bg-white dark:bg-gray-900 text-violet11 cursor-default">
            <Icon icon="radix-icons:chevron-up" />
          </SelectScrollUpButton>

          <SelectViewport class="p-[5px]">
            <SelectGroup>
              <SelectItem v-if="allowEmpty" key="empty" value="none"
                class="text-sm leading-none text-grass11 rounded-[3px] flex items-center h-[25px] pr-[35px] pl-[25px] relative select-none data-[disabled]:text-mauve8 data-[disabled]:pointer-events-none data-[highlighted]:outline-none data-[highlighted]:bg-primary">
                <SelectItemIndicator class="absolute left-0 w-[25px] inline-flex items-center justify-center">
                  <Icon icon="radix-icons:check" />
                </SelectItemIndicator>
                <SelectItemText>-- None --</SelectItemText>
              </SelectItem>

              <SelectItem v-for="(option, index) in formattedOptions" :key="index" :value="String(option.key)"
                class="text-sm leading-none text-grass11 rounded-[3px] flex items-center h-[25px] pr-[35px] pl-[25px] relative select-none data-[disabled]:text-mauve8 data-[disabled]:pointer-events-none data-[highlighted]:outline-none data-[highlighted]:bg-primary data-[highlighted]:text-green1">
                <SelectItemIndicator class="absolute left-0 w-[25px] inline-flex items-center justify-center">
                  <Icon icon="radix-icons:check" />
                </SelectItemIndicator>
                <SelectItemText>{{ option.value }}</SelectItemText>
              </SelectItem>

              <SelectItem v-if="props.options.length == 0 && !allowEmpty" key="empty" value="none"
                class="text-sm leading-none text-grass11 rounded-[3px] flex items-center h-[25px] pr-[35px] pl-[25px] relative select-none data-[disabled]:text-mauve8 data-[disabled]:pointer-events-none data-[highlighted]:outline-none data-[highlighted]:bg-primary">
                <SelectItemIndicator class="absolute left-0 w-[25px] inline-flex items-center justify-center">
                  <Icon icon="radix-icons:check" />
                </SelectItemIndicator>
                <SelectItemText>-- No options --</SelectItemText>
              </SelectItem>
            </SelectGroup>
          </SelectViewport>

          <SelectScrollDownButton
            class="flex items-center justify-center h-[25px] bg-white dark:bg-gray-900 text-violet11 cursor-default">
            <Icon icon="radix-icons:chevron-down" />
          </SelectScrollDownButton>
        </SelectContent>
      </SelectPortal>
    </SelectRoot>
  </div>

</template>
