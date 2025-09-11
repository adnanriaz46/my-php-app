import { ref, onMounted, onUnmounted } from 'vue'

const isMobile = ref(false)

function updateScreen() {
    isMobile.value = window.innerWidth < 768
}

// Ensure it's updated and reactive
export function useScreen() {
    onMounted(() => {
        updateScreen()
        window.addEventListener('resize', updateScreen)
    })

    onUnmounted(() => {
        window.removeEventListener('resize', updateScreen)
    })

    return {
        isMobile,
    }
}
