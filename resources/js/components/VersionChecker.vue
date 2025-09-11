<script setup lang="ts">
import { ref, watch } from 'vue'
import { useVersionChecker } from '@/composables/useAppVersion'
// import { useToast } from '@/composables/useToast'
import Toast from '@/components/ui/toast/Toast.vue'

const { hasNewVersion } = useVersionChecker()
// const { showToast } = useToast()

const showBanner = ref(false)

watch(hasNewVersion, (v) => {
  console.log('new version', v)
  if (v) {
    // showToast(
    //   'New Update Available',
    //   'Please refresh the page to load the latest version.',
    //   'info',
    //   3000 * 30
    // )
    showBanner.value = true
  }
})

const refreshPage = () => {
  window.location.reload()
}

const hideBanner = () => {
  showBanner.value = false
}
</script>

<template>
  <Toast />

  <transition name="fade">
    <div
      v-if="showBanner"
      class="fixed top-0 left-0 w-full z-50 bg-blue-600/90 dark:bg-blue-800/90 text-white px-4 py-3 flex justify-between items-center shadow"
    >
      <div class="text-sm font-medium">
        🔄 A new version is available. Please refresh the page.
      </div>
      <div class="space-x-2">
        <button
          @click="refreshPage"
          class="bg-white text-blue-700 dark:bg-gray-100 dark:text-blue-800 font-semibold px-3 py-1 rounded hover:bg-gray-200 transition cursor-pointer"
        >
          Refresh
        </button>
        <button
          @click="hideBanner"
          class="text-white hover:underline text-sm cursor-pointer"
        >
          Hide
        </button>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>