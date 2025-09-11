<script setup lang="ts">
import {Head} from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import {type BreadcrumbItem} from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import Label from "@/components/ui/label/Label.vue";
import Input from "@/components/ui/input/Input.vue";
import Button from "@/components/ui/button/Button.vue";
import {ref} from "vue";
import axios from 'axios';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Appearance settings',
    href: '/settings/appearance',
  },
];

const ResponseUrl = ref('');
const path = ref('');
const file = ref<File | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);
const upload = async () => {
  success.value = null;
  error.value = null;
  const formData = new FormData();
  formData.append('path', path.value);
  if (file.value) {
    formData.append('file', file.value);
  }
  loading.value = true;
  try {
    const response = await axios.post(route('assets.upload'), formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    loading.value = false;
    ResponseUrl.value = response.data.path;
    if (response.data.path) {
      success.value = 'Upload successful';
    }

  } catch (error) {
    console.error('Upload failed', error);
    error.value = error  as string;
  } finally {
    loading.value = false;
  }
};
</script>
<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Appearance settings"/>
    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall title="Upload Assets" description="Upload Assets"/>
        <label class="text-md">Response URL: </label>
        <div class="mx-auto">
          <pre class="bg-gray-800 text-white p-3 rounded-md text-wrap"><code class="text-sm font-mono">{{
              ResponseUrl
            }}</code></pre>
        </div>
        <Label>
          Path*
          <Input type="text" v-model="path"/>
        </Label>
        <Label>
          <Input type="file" @change="e => file = e.target.files[0]"/>
        </Label>
        <Button @click="upload" :disabled="loading">Upload</Button>
        <p v-if="error" class="text-red-500">{{ error }}</p>
        <p v-if="success" class="text-green-500">{{ success }}</p>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
