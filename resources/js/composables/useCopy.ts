import {useClipboard} from '@vueuse/core'

export function useClipboardCopy() {
    const {copy, copied, isSupported} = useClipboard()
    const handleCopy = async (text: string) => {
        if(!isSupported.value){
            alert('Error: Copy not supported, SSL not installed!');
        }
        try {
            await copy(text)
        } catch (e) {
            console.error('Copy failed:', e)
            alert('ERROR: ' + e?.toString());
        }
    }

    return {
        handleCopy,
        copied,
        isSupported,
    }
}
