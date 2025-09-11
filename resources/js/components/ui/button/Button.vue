<script setup lang="ts">
import type {HTMLAttributes} from 'vue'
import {cn} from '@/lib/utils'
import {Primitive, type PrimitiveProps} from 'reka-ui'
import {type ButtonVariants, buttonVariants} from '.'
import { Icon } from '@iconify/vue'

interface Props extends PrimitiveProps {
    variant?: ButtonVariants['variant']
    size?: ButtonVariants['size']
    class?: HTMLAttributes['class']
    loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    as: 'button',
})

</script>

<template>
    <Primitive
        data-slot="button"
        :as="as"
        :as-child="asChild"
        :class="cn(buttonVariants({ variant, size }), props.class, 'cursor-pointer')"
    >
        <Icon icon="tabler:loader-2" class="animate-spin text-2xl" v-if="loading" />
        <slot/>
    </Primitive>
</template>
