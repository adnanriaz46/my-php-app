<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger, useSidebar } from '@/components/ui/sidebar';
import type { BreadcrumbItemType, SharedData, User } from '@/types';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { usePage } from '@inertiajs/vue3';
import UserMenuContent from './UserMenuContent.vue';
import SidebarMenuButton from './ui/sidebar/SidebarMenuButton.vue';
import UserAvatar from './UserAvatar.vue';
import UserTypeBadge from './ui/user-type-badge/UserTypeBadge.vue';
import NotificationTray from './ui/notification-tray/notification-tray.vue';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { isMobile } = useSidebar();

withDefaults(defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>(), {
    breadcrumbs: () => []
});

const isAdmin = computed(() => {
    return route().current('admin.*');
});

</script>

<template>
    <header :class="isAdmin ? 'bg-red-500/50' : ''"
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4 justify-between">
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="!isMobile && breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
            <template v-else>
                <AppLogo size="large" class="w-full p-2" />
            </template>
        </div>
        <div class="flex gap-2">
            <NotificationTray class="w-10 my-auto h-full" />
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground w-12">
                        <div class="relative">
                            <UserAvatar :user="user" />
                            <UserTypeBadge :icon-only="true" :user-type="user.user_type"
                                class="absolute bottom-0 right-0 p-0.5" />
                        </div>
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg" :align="'end'">
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
