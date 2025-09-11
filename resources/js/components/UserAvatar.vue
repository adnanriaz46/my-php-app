<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';


interface Props {
  user: User;
}

const props = defineProps<Props>();

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => props.user.profile_picture && props.user.profile_picture !== '');
</script>

<template>
  <Avatar class="h-8 w-8 overflow-hidden rounded-full border border-gray-200 dark:border-white/40">
    <AvatarImage v-if="showAvatar" :src="user.profile_picture" :alt="user.name" />
    <AvatarFallback class="rounded-full text-black dark:text-white">
      {{ getInitials(user.name) }}
    </AvatarFallback>
  </Avatar>

</template>
