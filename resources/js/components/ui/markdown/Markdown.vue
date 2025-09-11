<!-- MarkdownViewer.vue -->
<template>
  <div v-html="compiledMarkdown" :class="avoidTailwind ? 'prose tw-reset' : 'prose'"></div>
</template>

<script setup>
import { marked } from 'marked'
import { computed } from 'vue'

const props = defineProps({
  content: {
    type: String,
    required: true,
  },
  avoidTailwind: {
    type: Boolean,
    default: true,
  },
})

// Convert markdown to HTML
const compiledMarkdown = computed(() => marked(props.content))
</script>
<style scoped>
strong {
  font-weight: 500 !important;
}

/* Escapes Tailwind layers inside this subtree */
.tw-reset,
.tw-reset * {
  all: revert !important;
  max-width: 100% !important;
  border-radius: 10px !important;
}


/* Optional: re-apply a base font-family/color from your app if needed */
.tw-reset {
  color: inherit !important;
  font-family: inherit !important;
}
</style>
