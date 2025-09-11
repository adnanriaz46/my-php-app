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
import { LogOut, Settings } from 'lucide-vue-next';
import { UserTypes } from "@/types/userTypes";
import { Icon } from '@iconify/vue'
// import { upgradeDialog } from '@/stores/DialogStore';
import { computed, ref, onMounted } from 'vue';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import axios from 'axios';
import { useToast } from "@/composables/useToast";
import Toast from '@/components/ui/toast/Toast.vue';
import { usePage } from '@inertiajs/vue3';
import { getInitials } from '@/composables/useInitials';
import Separator from '../separator/Separator.vue';
import Avatar from '../avatar/Avatar.vue';
import AvatarImage from '../avatar/AvatarImage.vue';
import AvatarFallback from '../avatar/AvatarFallback.vue';

interface Props {
    user: User;
    hoveredMenu: string | null;
    handleMouseEnter: (menu: string) => void;
    handleMouseLeave: () => void;
}

const handleLogout = () => {
    router.flushAll();
};

const props = defineProps<Props>();
const { showToast } = useToast();
const page = usePage();

// const openUpgrade = () => {
//     upgradeDialog.user = props.user;
//     upgradeDialog.upgradeDialogOpen = true;
// }

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
    <div v-if="hoveredMenu === 'user'"
        class="absolute right-0 top-full z-50 mt-0 w-65 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-2 shadow-lg"
        @mouseenter="handleMouseEnter('user')" @mouseleave="handleMouseLeave()" @click="handleMouseLeave()">
        <div class="grid gap-1">
            <!-- User Info -->
            <div class="flex items-center gap-3 rounded-md px-3 py-2 text-sm">
                <Avatar class="h-8 w-8 overflow-hidden rounded-full">
                    <AvatarImage v-if="user.profile_picture" :src="user.profile_picture" :alt="user.name" />
                    <AvatarFallback
                        class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                        {{ getInitials(user?.name) }}
                    </AvatarFallback>
                </Avatar>
                <div class="flex-1">
                    <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ user.name }}
                    </div>
                    <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ user.email }}
                    </div>
                </div>
            </div>
            <Separator />
            <!-- Menu Items -->
            <Link :href="route('dashboard')"
                class="flex items-start gap-3 rounded-md px-3 py-2 text-sm transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800">
            <div class="mt-0.5 flex h-5 w-5 items-center justify-center">
                <Icon icon="tabler:user-screen" class="size-5 text-neutral-500 dark:text-neutral-400" />
            </div>
            <div class="flex-1">
                <div class="font-medium text-neutral-900 dark:text-neutral-100">Dashboard</div>
            </div>
            </Link>

            <Link :href="route('profile.edit')"
                class="flex items-start gap-3 rounded-md px-3 py-2 text-sm transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800">
            <div class="mt-0.5 flex h-5 w-5 items-center justify-center">
                <Icon icon="tabler:user-edit" class="size-5 text-neutral-500 dark:text-neutral-400" />
            </div>
            <div class="flex-1">
                <div class="font-medium text-neutral-900 dark:text-neutral-100">My Profile</div>
            </div>
            </Link>

            <button v-if="isImpersonating" @click="stopImpersonation" 
                class="flex items-start gap-3 rounded-md px-3 py-2 text-sm transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800 cursor-pointer font-medium">
                <Icon icon="tabler:user-off" class="size-5 text-neutral-500 dark:text-neutral-400" />
                Stop Impersonation
            </button>

            <Link v-if="user.user_type == UserTypes.ADMIN" :href="route('admin.index')"
                class="flex items-start gap-3 rounded-md px-3 py-2 text-sm transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800">
            <div class="mt-0.5 flex h-5 w-5 items-center justify-center">
                <Icon icon="tabler:shield-lock" class="size-5 text-neutral-500 dark:text-neutral-400" />
            </div>
            <div class="flex-1">
                <div class="font-medium text-neutral-900 dark:text-neutral-100">Admin Dashboard
                </div>
            </div>
            </Link>

            <!-- Appearance Tabs -->
            <div class="px-3 py-2">
                <AppearanceTabs :hide-label="true" class="w-full justify-center" />
            </div>

            <!-- Logout -->
            <Link method="post" :href="route('logout')" @click="handleLogout"
                class="flex items-start gap-3 justify-start text-left rounded-md px-3 py-2 text-sm transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800">
            <div class="mt-0.5 flex h-5 w-5">
                <Icon icon="tabler:logout" class="size-5 text-neutral-500 dark:text-neutral-400" />
            </div>
            <div class="flex-1">
                <div class="font-medium text-neutral-900 dark:text-neutral-100">Log out</div>
            </div>
            </Link>
        </div>
    </div>
</template>