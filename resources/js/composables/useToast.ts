import {ref} from 'vue'

type ToastType = 'success' | 'error' | 'info' | 'warning'

const open = ref(false)
const title = ref('')
const message = ref('')
const type = ref<ToastType>('info')
const toastDuration = ref<number | null>(null)

function showToast(
    customTitle: string,
    customMessage: string,
    customType: ToastType = 'info',
    customDuration: number = 4000
) {
    open.value = false
    type.value = customType
    title.value = customTitle
    message.value = customMessage
    toastDuration.value = customDuration

    setTimeout(() => {
        open.value = true
    }, 100)

    setTimeout(() => {
        open.value = false
    }, customDuration + 100)
}

export function useToast() {
    return {
        open,
        title,
        message,
        type,
        duration: toastDuration,
        showToast,
    }
}
