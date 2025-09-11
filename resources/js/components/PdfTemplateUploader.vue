<template>
  <div class="pdf-template-uploader">
    <h2>Upload PDF Template</h2>
    <form @submit.prevent="uploadPdf">
      <input type="file" accept="application/pdf" @change="onFileChange" />
      <button type="submit" :disabled="!pdfFile || uploading">
        {{ uploading ? 'Uploading...' : 'Upload' }}
      </button>
    </form>
    <div v-if="uploadSuccess" class="success">Template uploaded! Template ID: {{ templateId }}</div>
    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const pdfFile = ref(null);
const uploading = ref(false);
const uploadSuccess = ref(false);
const templateId = ref(null);
const error = ref('');
const emit = defineEmits(['uploaded']);

function onFileChange(e) {
  const file = e.target.files[0];
  if (file && file.type === 'application/pdf') {
    pdfFile.value = file;
    error.value = '';
  } else {
    pdfFile.value = null;
    error.value = 'Please select a valid PDF file.';
  }
}

async function uploadPdf() {
  if (!pdfFile.value) return;
  uploading.value = true;
  error.value = '';
  uploadSuccess.value = false;
  templateId.value = null;
  try {
    const formData = new FormData();
    formData.append('pdf', pdfFile.value);
    const response = await axios.post('http://localhost:8000/api/templates/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    uploadSuccess.value = true;
    templateId.value = response.data.template.id;
    emit('uploaded', response.data.template.id);
  } catch (err) {
    error.value = err.response?.data?.message || 'Upload failed.';
  } finally {
    uploading.value = false;
  }
}
</script>

<style scoped>
.pdf-template-uploader {
  max-width: 400px;
  margin: 2rem auto;
  padding: 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: #fafafa;
}
.success { color: green; margin-top: 1rem; }
.error { color: red; margin-top: 1rem; }
</style> 