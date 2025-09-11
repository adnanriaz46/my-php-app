<script setup lang="ts">
import Avatar from "@/components/ui/avatar/Avatar.vue";
import {getInitials} from "@/composables/useInitials";
import {AvatarFallback, AvatarImage} from "@/components/ui/avatar";
import {User} from "@/types";

interface Props {
    title: string;
    description?: string;
    user?: User;
    imageKey?: string;
}

defineProps<Props>();
</script>

<template>
    <header class="flex">
        <div class="w-full">
            <h3 class="mb-0.5 text-base font-medium">{{ title }}</h3>
            <p v-if="description" class="text-sm text-muted-foreground">
                {{ description }}
            </p>
        </div>
        <div v-if="user" class="w-[60px]">
            <Avatar class="size-12 overflow-hidden rounded-full">
                <AvatarImage v-if="!imageKey && user.profile_picture" :src="user.profile_picture" :alt="user.name" />
                <AvatarImage v-if="imageKey && user[imageKey]" :src="user[imageKey]" :alt="user.name" />
                <AvatarFallback class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                    {{ getInitials(user?.name) }}
                </AvatarFallback>
            </Avatar>
        </div>
    </header>
</template>
