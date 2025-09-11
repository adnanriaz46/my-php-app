<template>
  <div v-if="pdfUrl" class="pdf-template-designer">
    <h2>Design Fields</h2>
    <div ref="designerContainer" style="height: 700px; border: 1px solid #ccc;"></div>
    <button @click="saveFields" :disabled="saving" style="margin-top: 1rem;">
      {{ saving ? 'Saving...' : 'Save Field Layout' }}
    </button>
    <div v-if="saveSuccess" class="success">Fields saved!</div>
    <div v-if="error" class="error">{{ error }}</div>
  </div>
  <div v-else>Loading PDF...</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Designer } from '@pdfme/ui';

const props = defineProps({
  templateId: { type: Number, required: true }
});

const pdfUrl = ref('');
const designerContainer = ref(null);
const designer = ref(null);
const saving = ref(false);
const saveSuccess = ref(false);
const error = ref('');

// Fetch the template info (get the S3 URL)
onMounted(async () => {
  try {
    const { data } = await axios.get(`http://localhost:8000/api/templates`);
    const template = data.find(t => t.id === props.templateId);
    if (!template) throw new Error('Template not found');
    // Get a signed S3 URL or use the public URL
    pdfUrl.value = `https://revamp365-storage.s3.amazonaws.com/${template.pdf_path}`;
    // Fetch the PDF as ArrayBuffer
    const pdfRes = await fetch(pdfUrl.value);
    const pdfArrayBuffer = await pdfRes.arrayBuffer();
    // Initialize @pdfme/ui Designer
    designer.value = new Designer({
      domContainer: designerContainer.value,
      template: {
        basePdf: pdfArrayBuffer,
        schemas: [{}],
      },
      options: { height: 700 },
    });
  } catch (err) {
    error.value = err.message || 'Failed to load PDF.';
  }
});

async function saveFields() {
  saving.value = true;
  error.value = '';
  saveSuccess.value = false;
  try {
    const fieldsJson = designer.value.getTemplate().schemas[0];
    await axios.post(`http://localhost:8000/api/templates/${props.templateId}/fields`, {
      fields_json: fieldsJson
    });
    saveSuccess.value = true;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save fields.';
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
.pdf-template-designer {
  max-width: 900px;
  margin: 2rem auto;
  padding: 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: #fafafa;
}
.success { color: green; margin-top: 1rem; }
.error { color: red; margin-top: 1rem; }
</style> 