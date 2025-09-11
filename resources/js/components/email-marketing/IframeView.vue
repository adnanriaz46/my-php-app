<template>
    <iframe
        ref="iframeRef"
        :class="props.class"
        :sandbox="props.sandbox"
        frameborder="0"
        @load="handleIframeLoad"
    ></iframe>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';

interface Props {
    content?: string;
    class?: string;
    sandbox?: string;
}

const props = withDefaults(defineProps<Props>(), {
    content: '',
    class: '',
    sandbox: 'allow-same-origin allow-scripts'
});

const iframeRef = ref<HTMLIFrameElement | null>(null);

const handleIframeLoad = () => {
    if (iframeRef.value?.contentDocument) {
        updateIframeContent();
    }
};

const updateIframeContent = () => {
    if (!iframeRef.value?.contentDocument) return;

    const doc = iframeRef.value.contentDocument;
    doc.open();
    doc.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    margin: 0;
                    padding: 20px;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background: #fff;
                }
                img {
                    max-width: 100%;
                    height: auto;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #fff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .email-header {
                    background: #f8f9fa;
                    padding: 20px;
                    border-bottom: 1px solid #e9ecef;
                }
                .email-body {
                    padding: 20px;
                }
                .email-footer {
                    background: #f8f9fa;
                    padding: 15px 20px;
                    border-top: 1px solid #e9ecef;
                    font-size: 12px;
                    color: #6c757d;
                }
                .property-card {
                    border: 1px solid #e9ecef;
                    border-radius: 8px;
                    overflow: hidden;
                    margin: 20px 0;
                }
                .property-image {
                    width: 100%;
                    height: 200px;
                    object-fit: cover;
                }
                .property-details {
                    padding: 15px;
                }
                .property-price {
                    font-size: 18px;
                    font-weight: bold;
                    color: #28a745;
                }
                .property-address {
                    font-weight: 500;
                    margin: 5px 0;
                }
                .property-stats {
                    display: flex;
                    gap: 15px;
                    margin-top: 10px;
                    font-size: 14px;
                    color: #6c757d;
                }
                .stat-item {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }
                .cta-button {
                    display: inline-block;
                    background: #007bff;
                    color: white;
                    padding: 12px 24px;
                    text-decoration: none;
                    border-radius: 6px;
                    font-weight: 500;
                    margin-top: 15px;
                }
                .cta-button:hover {
                    background: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                ${props.content}
            </div>
        </body>
        </html>
    `);
    doc.close();
};

watch(() => props.content, updateIframeContent, { immediate: true });

onMounted(() => {
    if (iframeRef.value) {
        updateIframeContent();
    }
});
</script>
