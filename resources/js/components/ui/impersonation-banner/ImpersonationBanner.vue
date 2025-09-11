<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';

const isImpersonating = ref(false);

const checkImpersonationStatus = async () => {
    try {
        const response = await axios.get(route('check-impersonation'));
        isImpersonating.value = response.data.is_impersonating;
    } catch (error) {
        console.error('Error checking impersonation status:', error);
        isImpersonating.value = false;
    }
};

onMounted(() => {
    checkImpersonationStatus();
});
</script>

<template>
    <!-- Impersonation Banner -->
    <div v-if="isImpersonating"
        class="sticky top-0 z-50 bg-yellow-100 dark:bg-yellow-900 border-b border-yellow-300 dark:border-yellow-700 px-4 py-1 text-yellow-800 dark:text-yellow-200 text-xs font-medium flex items-center justify-center gap-2">
        <Icon icon="tabler:user-off" class="w-4 h-4" />
        <span>You are currently impersonating a user</span>
    </div> 
</template>