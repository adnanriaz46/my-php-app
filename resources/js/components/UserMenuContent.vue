<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator
} from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut } from 'lucide-vue-next';
import { UserTypes } from "@/types/userTypes";
import { Icon } from '@iconify/vue'
import { upgradeDialog } from '@/stores/DialogStore';
import { useSidebar } from "@/components/ui/sidebar";
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from "@/composables/useToast";
import Toast from '@/components/ui/toast/Toast.vue';
// import { usePage } from '@inertiajs/vue3';

const { isMobile, toggleSidebar } = useSidebar();
const { showToast } = useToast();
// const page = usePage();

interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

const props = defineProps<Props>();

const openUpgrade = () => {
    upgradeDialog.user = props.user;
    upgradeDialog.upgradeDialogOpen = true;
    if (isMobile.value) {
        toggleSidebar();
    }
}

const isAdmin = computed(() => {
    return route().current('admin.*');
});

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

async function stopImpersonation() {
    try {
        const response = await axios.post(route('stop-impersonation'));

        if (response.data.success) {
            showToast('Success', response.data.message, 'success');
            // Redirect to admin dashboard after a short delay
            setTimeout(() => {
                window.location.href = response.data.redirect_url;
            }, 1000);
        }
    } catch (error: any) {
        const errorMessage = error.response?.data?.error || 'Failed to stop impersonation';
        showToast('Error', errorMessage, 'error');
    }
}


</script>

<template>
    <Toast />
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <span class="block w-full" @click="openUpgrade">
                <Icon icon="tabler:device-imac-up" :height="16" />&nbsp;
                Upgrade Account
            </span>
        </DropdownMenuItem>

        <!-- <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="route('learn.index')" prefetch as="button">
            <Icon icon="tabler:book" :height="16" />&nbsp;
            Learn
            </Link>
        </DropdownMenuItem> 
        -->
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="route('profile.edit')" prefetch as="button">
            <Icon icon="tabler:user-edit" :height="16" />&nbsp;
            My Profile
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="route('referrals')" :disabled="user.user_type == UserTypes.FREE" prefetch
                as="button">
            <Icon icon="tabler:link-plus" :height="16" />&nbsp;
            Referrals
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem :as-child="true" v-if="!isAdmin && user.user_type == UserTypes.ADMIN">
            <Link class="block w-full" :href="route('admin.index')" prefetch as="button">
            <Icon icon="tabler:shield-lock" :height="16" />&nbsp;
            Admin Dashboard
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem :as-child="true" v-if="isAdmin">
            <Link class="block w-full" :href="route('dashboard')" prefetch as="button">
            <Icon icon="tabler:user-screen" :height="16" />&nbsp;
            User Dashboard
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem v-if="isImpersonating" @click="stopImpersonation" class="cursor-pointer">
            <Icon icon="tabler:user-off" :height="16" />&nbsp;
            Stop Impersonation
        </DropdownMenuItem>
    </DropdownMenuGroup>

    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link class="block w-full" method="post" :href="route('logout')" @click="handleLogout" as="button">
        <LogOut class="mr-2 h-4 w-4" />
        Log out
        </Link>
    </DropdownMenuItem>
</template>
