<script setup lang="ts">
import { ref, watch, computed, PropType } from 'vue';
import FullScreenDialog from '@/components/ui/full-screen-dialog/FullScreenDialog.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import { nextTick } from 'vue';

interface TrainingVideo {
    id: number;
    topic?: string;
    summary_html?: string;
    summary?: string;
    youtube_video_id?: string;
    embed_code?: string;
    url?: string;
    transcript?: string;
}

const props = defineProps({
    modelValue: Boolean,
    post: Object as PropType<{ id: number; title?: string; description?: string } | null>,
});
const emit = defineEmits(['update:modelValue', 'close']);

const open = ref(props.modelValue);
watch(() => props.modelValue, v => open.value = v);
watch(open, v => emit('update:modelValue', v));

const videos = ref<TrainingVideo[]>([]);
const completed = ref<number[]>([]);
const loading = ref(false);
const selectedIndex = ref(0);
const markLoading = ref(false);
const markSuccess = ref(false);

const percent = computed(() => {
    if (!videos.value.length) return 0;
    return Math.round((completed.value.length / videos.value.length) * 100);
});

async function fetchDetails() {
    if (!props.post) return;
    loading.value = true;
    const res = await fetch(route('learn.getData', props.post.id));
    const data = await res.json();
    videos.value = data.post.videos;
    completed.value = data.completed_videos;
    selectedIndex.value = 0;
    loading.value = false;
}

watch(() => props.post, fetchDetails, { immediate: true });

async function markComplete(videoId: number) {
    if (completed.value.includes(videoId) || markLoading.value) return;
    markLoading.value = true;
    markSuccess.value = false;
    try {
        await axios.post(route('learn.completeVideo', videoId), {});
        if (!completed.value.includes(videoId)) completed.value.push(videoId);
        markSuccess.value = true;
        await nextTick();
        setTimeout(() => { markSuccess.value = false; }, 1200);
    } finally {
        markLoading.value = false;
        router.reload();
    }
}

function selectVideo(idx: number) {
    selectedIndex.value = idx;
}

const showTranscript = ref(false);


</script>

<template>
    <FullScreenDialog v-model="open" :title="props.post?.title" @close="emit('close')">
        <div v-if="loading" class="p-8 text-center h-[calc(100dvh-15px)] items-center">
            <Icon icon="tabler:loader-2" class="size-10 animate-spin m-auto" />
        </div>
        <div v-else class="flex flex-col md:flex-row gap-4 p-5 overflow-auto md:overflow-hidden h-[calc(100dvh)]">
            <!-- Left: Video List -->
            <div class="w-full md:w-1/3 md:max-w-xs rounded-xl p-1
               overflow-y-auto h-[40dvh] md:h-full">
                <div class="mb-4">
                    <div class="font-bold text-lg mb-1 text-gray-900 dark:text-gray-100">{{ props.post?.title }}</div>
                    <div class="text-gray-600 dark:text-gray-300 mb-2 text-sm">{{ props.post?.description }}</div>
                    <div class="h-2 bg-gray-200 dark:bg-stone-700 rounded">
                        <div class="h-2 bg-green-500 dark:bg-green-600 rounded" :style="{ width: percent + '%' }"></div>
                    </div>
                    <div class="text-xs text-right mt-1 text-gray-700 dark:text-gray-300">{{ percent }}% completed</div>
                </div>
                <div class="flex flex-col gap-1 text-base">
                    <div v-for="(video, idx) in videos" :key="video.id"
                        class="flex items-center px-3 py-2 rounded cursor-pointer transition-colors" :class="[
                            selectedIndex === idx ? 'bg-yellow-100 dark:bg-yellow-900/60 text-black dark:text-yellow-200 font-semibold' : 'hover:bg-gray-100 dark:hover:bg-stone-700',
                            completed.includes(video.id) ? 'text-green-600 dark:text-green-400' : '',
                        ]" @click="selectVideo(idx)">
                        <span class="mr-2">
                            <span v-if="completed.includes(video.id)">
                                <Icon icon="tabler:circle-check-filled"
                                    class="size-4 text-green-600 dark:text-green-400" />
                            </span>
                            <span v-else
                                class="inline-block w-4 h-4 border rounded-full border-gray-400 dark:border-gray-500"></span>
                        </span>
                        <span>{{ video.topic || 'Untitled' }}</span>
                    </div>
                </div>
            </div>
            <!-- Right: Video Content -->
            <div class="flex-1 min-w-0 rounded-xl shadow-md border
               overflow-y-auto h-[60dvh] md:h-full">
                <div v-if="videos[selectedIndex]" class="p-2">
                    <div class="font-bold text-xl mb-2 text-gray-900 dark:text-yellow-200">{{
                        videos[selectedIndex].topic || 'Untitled' }}</div>

                    <div class="p-3 max-w-2xl mx-auto ">
                        <div v-if="videos[selectedIndex].youtube_video_id" class="mb-2">
                            <iframe :src="`https://www.youtube.com/embed/${videos[selectedIndex].youtube_video_id}`"
                                frameborder="0" allowfullscreen class="w-full rounded-lg aspect-video"></iframe>
                        </div>
                        <div v-else-if="videos[selectedIndex].embed_code" class="mb-2 aspect-video"
                            v-html="videos[selectedIndex].embed_code"></div>
                        <div v-else-if="videos[selectedIndex].url" class="mb-2 aspect-video">
                            <a :href="videos[selectedIndex].url" target="_blank"
                                class="text-blue-600 dark:text-blue-400 underline">View Resource</a>
                        </div>
                    </div>
                    <div v-if="videos[selectedIndex].transcript"
                        class="mt-2 mb-3 text-xs text-gray-500 dark:text-gray-400">
                        <button
                            class="mb-1 px-2 py-0.5 rounded bg-gray-200 dark:bg-stone-700 text-gray-700 dark:text-gray-200 text-xs font-semibold"
                            @click="showTranscript = !showTranscript" type="button">
                            {{ showTranscript ? 'Hide' : 'Show' }} Transcript
                        </button>
                        <div v-if="showTranscript" class="mt-1">
                            Transcript: {{ videos[selectedIndex].transcript }}
                        </div>
                    </div>

                    <div class="mb-2 text-sm text-gray-700 dark:text-gray-200"
                        v-html="videos[selectedIndex].summary_html || videos[selectedIndex].summary"></div>


                    <div class="mt-4 flex justify-end">
                        <transition name="fade" mode="out-in">
                            <button v-if="!completed.includes(videos[selectedIndex].id)" :key="'mark'"
                                class="cursor-pointer px-3 py-1 rounded-lg bg-green-500 dark:bg-green-700 text-white text-xs font-bold flex items-center gap-2 disabled:bg-gray-300 dark:disabled:bg-stone-700 relative"
                                :disabled="markLoading || completed.includes(videos[selectedIndex].id)"
                                @click="markComplete(videos[selectedIndex].id)" title="Mark this training as complete">
                                <span v-if="markLoading" class="animate-spin mr-1">
                                    <Icon icon="svg-spinners:90-ring-with-bg" class="size-4" />
                                </span>
                                <span v-else>Mark Complete</span>
                            </button>
                            <button v-else :key="'done'"
                                class="cursor-default px-3 py-1 rounded-lg bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-400 text-xs font-bold flex items-center gap-2 relative"
                                disabled title="You have completed this training">
                                <transition name="bounce">
                                    <Icon v-if="markSuccess" icon="tabler:circle-check-filled"
                                        class="size-4 text-green-600 dark:text-green-400 animate-bounce hidden" />
                                    <Icon v-else icon="tabler:circle-check"
                                        class="size-4 text-green-600 dark:text-green-400" />
                                </transition>
                                Completed
                            </button>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </FullScreenDialog>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.bounce-enter-active {
    animation: bounce 0.6s;
}

@keyframes bounce {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.3);
    }
}
</style>