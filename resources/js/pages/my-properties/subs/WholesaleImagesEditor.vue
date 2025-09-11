<script setup lang="ts">
import {ref, watch} from "vue";
import {getMainImage} from "@/types/property";
import axios from "axios";
import draggable from "vuedraggable";
import {Icon} from '@iconify/vue';
import FullScreenImageSlider from "@/components/ui/image-slider/FullScreenImageSlider.vue";

const props = defineProps<{
  id?: number | string,
  bindImages: string[] | null,
}>()
const emit = defineEmits<{
  (e: 'update:bindImages', value: string[] | null): void;
}>();

// const images = computed({
//   get: () => props.bindImages,
//   set: (val) => emit('update:bindImages', val)
// })

const images = ref<string[] | null>(props.bindImages);

watch(images, (val) => {
  emit('update:bindImages', val);
}, {deep: true});

// watch(() => props.bindImages, (newVal) => {
//   console.log('bindImages', newVal)
//   if (JSON.stringify(newVal) !== JSON.stringify(images.value)) {
//     images.value = [...newVal];
//   }
// });

const emptyImage = getMainImage('');
const fileInput = ref<HTMLInputElement | null>(null);
const errors = ref<string[]>([]);
const isDragging = ref(false);
const isUploading = ref(false);
const uploadProgress = ref<number>(0);
const currentQueue = ref<number>(0);
const openFullScreen = ref<boolean>(false);
// Handle multiple files
const uploadImages = async (files: FileList | File[]) => {
  const fileArray = Array.from(files);
  currentQueue.value = fileArray.length;
  errors.value = [];

  if (!images.value) {
    images.value = [];
  }

  for (const file of fileArray) {
    const formData = new FormData();
    formData.append("image", file);

    isUploading.value = true;
    uploadProgress.value = 0;

    try {
      const response = await axios.post(route('my-property.upload_image'), formData, {
        headers: {'Content-Type': 'multipart/form-data'},
        onUploadProgress: progressEvent => {
          if (progressEvent.total) {
            uploadProgress.value = Math.round((progressEvent.loaded / progressEvent.total) * 100);
          }
        }
      });

      if (response.data.image) {
        images.value?.push(response.data.image);
      } else if (response.data.error) {
        errors.value.push(response.data.error);
      } else {
        errors.value.push("Unknown error during upload.");
      }
    } catch (err) {
      errors.value.push("Upload failed: " + (err as any).message || "Unknown error");
    } finally {
      isUploading.value = false;
      uploadProgress.value = 0;
      currentQueue.value--;
    }
  }
  currentQueue.value = 0;
};

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files?.length) {
    uploadImages(target.files);
    target.value = "";
  }
};

const handleDrop = (e: DragEvent) => {
  e.preventDefault();
  isDragging.value = false;
  if (e.dataTransfer?.files?.length) {
    uploadImages(e.dataTransfer.files);
  }
};

const handleDragOver = (e: DragEvent) => {
  e.preventDefault();
  isDragging.value = true;
};

const handleDragLeave = () => {
  isDragging.value = false;
};

const deleteImage = (index: number) => {
  images.value?.splice(index, 1);
};

const currentImageIndex = ref<number>(0);
const onFullscreenOpen = (index: number) => {
  currentImageIndex.value = index;
  openFullScreen.value = true;
}

</script>

<template>
  <div class="w-full space-y-4">

    <!-- Preview Main Image -->
    <div>
      <img
          v-if="!images || !images?.length"
          :src="emptyImage"
          class="rounded-lg w-full aspect-video object-cover"
      />
      <img
          v-else
          :src="images[0]"
          class="rounded-lg w-full aspect-video object-cover"
      />
    </div>


    <!-- Dropzone -->
    <div
        class="border-2 border-dashed dark:border-gray-700 rounded-lg p-6 text-center transition-all duration-200"
        :class="{
        'border-primary dark:border-primary': isDragging,
        'border-gray-300 bg-white/40': !isDragging
      }"
        @click="fileInput?.click()"
        @dragover="handleDragOver"
        @dragleave="handleDragLeave"
        @drop="handleDrop"
    >
      <input
          ref="fileInput"
          type="file"
          accept="image/*"
          multiple
          class="hidden"
          @change="handleFileChange"
      />
      <p class="text-muted-foreground">Drag & Drop images here</p>
      <p class="text-sm text-muted-foreground">or click to upload file</p>
      <div class="" v-if="currentQueue">{{ currentQueue }} {{ currentQueue > 1 ? 'images' : 'image' }} waiting to be
        uploaded.
      </div>
    </div>

    <!-- Progress Bar -->
    <div v-if="isUploading" class="mt-2">
      <div class="h-2 bg-gray-200 rounded">
        <div
            class="h-2 bg-blue-500 rounded transition-all"
            :style="{ width: uploadProgress + '%' }"
        ></div>
      </div>
      <p class="text-sm text-gray-600 mt-1">Uploading: {{ uploadProgress }}%</p>
    </div>

    <!-- Errors -->
    <ul class="list-disc text-red-600 text-sm pl-5" v-if="errors?.length">
      <li v-for="(err, i) in errors" :key="i">{{ err }}</li>
    </ul>

    <FullScreenImageSlider v-if="openFullScreen && images" :index="currentImageIndex" :images="images"
                           v-model:open="openFullScreen"/>
    <!-- Image Gallery -->
    <draggable
        v-if="images"
        v-model="images"
        item-key="gallery"
        class="grid grid-cols-2 xl:grid-cols-3 gap-3"
        :animation="200"
    >
      <template #item="{ element, index }">
        <div class="relative group rounded-lg overflow-hidden border border-gray-200">
          <img :src="element" class="w-full aspect-square object-cover rounded-lg  cursor-move"
               @click.stop="onFullscreenOpen(index)"/>

          <!-- Delete Button -->
          <Icon
              @click="deleteImage(index)"
              icon="tabler:x"
              class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded-full text-xs opacity-0 group-hover:opacity-100 transition size-6 cursor-pointer"
          >
            ✕
          </Icon>

          <div
              class="absolute bottom-1 left-1 text-xs bg-black bg-opacity-50 text-white px-2 py-0.5 rounded transition "
          >
            Drag to reorder
          </div>
        </div>
      </template>
    </draggable>
  </div>
</template>
