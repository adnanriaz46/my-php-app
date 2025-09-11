import {ref} from 'vue';

export function useConfirmDialog() {
    const isOpen = ref(false)
    const message = ref('')
    const resolveFn = ref<((value: unknown) => void) | null>(null)
    const _confirmBtnTxt = ref<string>('Confirm');

    const openConfirm = (msg: string, confirmBtnTxt = '') => {
        message.value = msg 
        isOpen.value = true
        _confirmBtnTxt.value = confirmBtnTxt;
        return new Promise((resolve) => {
            resolveFn.value = resolve
        })
    }

    const confirm = () => {
        isOpen.value = false
        resolveFn.value?.(true)
    }

    const cancel = () => {
        isOpen.value = false
        resolveFn.value?.(false)
    }

    return {
        isOpen,
        message,
        _confirmBtnTxt,
        openConfirm,
        confirm,
        cancel,
    }
}
