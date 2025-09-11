<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import TrainingVideoDialog from '@/components/learn/TrainingVideoDialog.vue';
import { ref } from 'vue';
import PageHeading from '@/components/ui/page-heading/PageHeading.vue';

interface TrainingPost {
  id: number;
  title: string;
  short_description?: string;
  thumbnail?: string;
  completed_percent?: number;
}

const props = defineProps<{ posts: TrainingPost[], slug: string | null }>();

const selectedPost = ref<TrainingPost | null>(null);
const dialogOpen = ref(false);

function openDialog(post: TrainingPost) {
  selectedPost.value = post;
  dialogOpen.value = true;
}

const breadcrumbs = [
  {
    title: 'Training & Education',
    href: route('learn.index'),
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="Training & Education" />
    <div class="p-3">
      <PageHeading title="Training & Education" />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-h-[calc(100dvh-120px)] max-w-7xl mx-auto overflow-y-auto p-6">
      <div v-if="!props.posts.length" class="col-span-full text-center text-gray-500 py-12">
        No records found.
      </div>
      <div v-for="post in props.posts" :key="post.id" class="rounded-xl border cursor-pointer shadow-sm  hover:shadow-lg"
        @click="openDialog(post)">
        <img v-if="post.thumbnail" :src="post.thumbnail" class="w-full h-40 object-cover rounded-t-lg mb-3" />
        <div class="p-4">
          <h2 class="font-semibold text-lg mb-1">{{ post.title }}</h2>
          <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ post.short_description }}</p>
          <div class="h-2 bg-gray-200 rounded">
            <div class="h-2 bg-green-500 rounded" :style="{ width: (post.completed_percent || 0) + '%' }"></div>
          </div>
          <div class="text-xs text-right mt-1">{{ post.completed_percent ? post.completed_percent + '%' : '0%' }}</div>
        </div>
      </div>
    </div>
    <TrainingVideoDialog v-if="selectedPost" v-model="dialogOpen" :post="selectedPost" @close="selectedPost = null" />

  </AppLayout>
</template>