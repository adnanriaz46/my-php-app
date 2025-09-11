import { onMounted, ref } from 'vue'

export function useVersionChecker(interval = 60 * 1000) {
    const hasNewVersion = ref(false)

    const checkVersion = async () => {
        try {
            const response = await fetch('/version.json', {
                cache: 'no-cache'
            });
            const { version } = await response.json();

            if (window.__APP_VERSION__ && version != window.__APP_VERSION__.version) {
                hasNewVersion.value = true
            }
        } catch (e) {
            console.warn('Version check failed', e)
        }
    }

    onMounted(() => {
        setInterval(checkVersion, interval);
    })

    return { hasNewVersion }
}
