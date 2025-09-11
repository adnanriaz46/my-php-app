<script setup lang="ts">
import {ref, watch} from 'vue'
import {
  DatePickerArrow,
  DatePickerCalendar,
  DatePickerCell,
  DatePickerCellTrigger,
  DatePickerContent,
  DatePickerField,
  DatePickerGrid,
  DatePickerGridBody,
  DatePickerGridHead,
  DatePickerGridRow,
  DatePickerHeadCell,
  DatePickerHeader,
  DatePickerHeading,
  DatePickerInput,
  DatePickerNext,
  DatePickerPrev,
  DatePickerRoot,
  DatePickerTrigger,
  Label,
} from 'reka-ui'
import {Icon} from '@iconify/vue'

const props = defineProps<{
  modelValue: Date | string | null
  label?: string
  id?: string
  isDateUnavailable?: (date: any) => boolean
}>()

const emit = defineEmits(['update:modelValue'])

const selectedDate = ref<Date | string | null>(props.modelValue ?? null)


watch(
    () => props.modelValue,
    (val) => {
      const date = new Date(val);
      if (date) {
        selectedDate.value = {
          calendar: {identifier: "gregory"},
          era: date.getFullYear() >= 0 ? "AD" : "BC",
          year: date.getFullYear(),
          month: date.getMonth() + 1,
          day: date.getDate()
        };
      }

      console.log("VALUE: ", val);
    }
)
watch(
    () => selectedDate.value,
    (val) => {
      if (val?.year) {
        const newVal = new Date(val.year, val.month - 1, val.day).toLocaleDateString();
        emit('update:modelValue', newVal)
      }
    }
)


</script>

<template>
  <div class="flex flex-col gap-2">
    <Label
        v-if="label"
        class="text-sm text-stone-700 dark:text-white"
        :for="id || 'date-picker'"
    >
      {{ label }}
    </Label>
    <DatePickerRoot
        :id="id || 'date-picker'"
        v-model="selectedDate"

        :is-date-unavailable="isDateUnavailable"
    >
      <DatePickerField
          v-slot="{ segments }"
          class="w-full flex select-none bg-default items-center rounded-lg shadow-sm text-center justify-between border p-1 data-[invalid]:border-red-500"
      >
        <div class="flex items-center">
          <template v-for="item in segments" :key="item.part">
            <DatePickerInput
                v-if="item.part === 'literal'"
                :part="item.part"
            >
              {{ item.value }}
            </DatePickerInput>
            <DatePickerInput
                v-else
                :part="item.part"
                class="rounded p-0.5 focus:outline-none focus:shadow-[0_0_0_2px] focus:shadow-black dark:focus:shadow-white data-[placeholder]:text-gray-400"
            >
              {{ item.value }}
            </DatePickerInput>
          </template>
        </div>
        <DatePickerTrigger class="focus:shadow-[0_0_0_2px] rounded p-1 focus:shadow-black dark:focus:shadow-white">
          <Icon icon="radix-icons:calendar" class="text-base"/>
        </DatePickerTrigger>
      </DatePickerField>

      <DatePickerContent
          :side-offset="4"
          class="z-30 rounded-xl bg-default border shadow-sm will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade"
      >
        <DatePickerArrow class="fill-white stroke-gray-300 dark:fill-gray-950 dark:stroke-gray-700"/>
        <DatePickerCalendar
            v-slot="{ weekDays, grid }"
            class="p-4"
        >
          <DatePickerHeader class="flex items-center justify-between">
            <DatePickerPrev
                class="inline-flex items-center cursor-pointer justify-center rounded-md bg-transparent w-7 h-7 hover:bg-stone-50 dark:hover:bg-stone-700  active:scale-98 active:transition-all focus:shadow-[0_0_0_2px] focus:shadow-black"
            >
              <Icon icon="radix-icons:chevron-left" class="w-4 h-4"/>
            </DatePickerPrev>
            <DatePickerHeading class="font-medium"/>
            <DatePickerNext
                class="inline-flex items-center cursor-pointer justify-center rounded-md bg-transparent w-7 h-7 hover:bg-stone-50 dark:hover:bg-stone-700 active:scale-98 active:transition-all focus:shadow-[0_0_0_2px] focus:shadow-black"
            >
              <Icon icon="radix-icons:chevron-right" class="w-4 h-4"/>
            </DatePickerNext>
          </DatePickerHeader>

          <div class="flex flex-col space-y-4 pt-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <DatePickerGrid
                v-for="month in grid"
                :key="month.value.toString()"
                class="w-full border-collapse select-none space-y-1"
            >
              <DatePickerGridHead>
                <DatePickerGridRow class="mb-1 flex w-full justify-between">
                  <DatePickerHeadCell
                      v-for="day in weekDays"
                      :key="day"
                      class="w-8 rounded-md text-xs text-green8"
                  >
                    {{ day }}
                  </DatePickerHeadCell>
                </DatePickerGridRow>
              </DatePickerGridHead>

              <DatePickerGridBody>
                <DatePickerGridRow
                    v-for="(weekDates, index) in month.rows"
                    :key="`weekDate-${index}`"
                    class="flex w-full"
                >
                  <DatePickerCell
                      v-for="weekDate in weekDates"
                      :key="weekDate.toString()"
                      :date="weekDate"
                  >
                    <DatePickerCellTrigger
                        :day="weekDate"
                        :month="month.value"
                        class="relative flex items-center justify-center whitespace-nowrap rounded-[9px] border border-transparent bg-transparent text-sm font-normal  w-8 h-8 outline-none focus:shadow-[0_0_0_2px] focus:shadow-black hover:border-black data-[selected]:bg-black dark:data-[selected]:bg-gray-600 data-[selected]:font-medium data-[outside-view]:text-black/30 dark:data-[outside-view]:text-white/30 data-[selected]:text-white data-[unavailable]:pointer-events-none data-[unavailable]:text-black/30 dark:data-[unavailable]:text-white/30 data-[unavailable]:line-through before:absolute before:top-[5px] before:hidden before:rounded-full before:w-1 before:h-1 before:bg-white data-[today]:before:block data-[today]:before:bg-green9 data-[selected]:before:bg-white"
                    />
                  </DatePickerCell>
                </DatePickerGridRow>
              </DatePickerGridBody>
            </DatePickerGrid>
          </div>
        </DatePickerCalendar>
      </DatePickerContent>
    </DatePickerRoot>
  </div>
</template>
