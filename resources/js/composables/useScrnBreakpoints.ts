import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'
import { computed, Ref } from "vue";
import { PropertyViewType, PropertyViewTypes } from "@/types/property";

export function useGridItems(viewType: Ref<PropertyViewType>) {
    const breakpoints = useBreakpoints(breakpointsTailwind)


    const gridPropertyItems = computed(() => {

        if (viewType.value == PropertyViewTypes.Map) {
            if (breakpoints.greater('2xl').value) return 2
            if (breakpoints.greater('xl').value) return 2
            if (breakpoints.greater('lg').value) return 1
            if (breakpoints.greater('md').value) return 1
            return 1
        } else if (viewType.value == PropertyViewTypes.Grid) {
            if (breakpoints.greater('2xl').value) return 4
            if (breakpoints.greater('xl').value) return 3
            if (breakpoints.greater('lg').value) return 3
            if (breakpoints.greater('md').value) return 2
            if (breakpoints.greater('sm').value) return 1
            return 1
        }
        return 1

    })
    const gridPropertyItemHeight = computed(() => {

        if (breakpoints.greater('2xl').value) return 400
        if (breakpoints.greater('xl').value) return 400
        if (breakpoints.greater('lg').value) return 400
        if (breakpoints.greater('md').value) return 400
        return 400
    })

    return { gridPropertyItems, gridPropertyItemHeight }
}
