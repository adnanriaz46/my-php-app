<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { open as openEmbed, type PlayAIEvent } from '@play-ai/agent-web-sdk';
import { upgradeDialog } from '@/stores/DialogStore';
import { getUser } from '@/composables/useUser';

const showPricing = () => {
    // Navigate to pricing or show pricing modal
    router.visit(route('register'));
};

const loading = ref(false);
const showLoader = (value = true) => {
    loading.value = value;
};

// PlayAI Configuration
const webEmbedId = 'f-Hh4HJd26jmt-NNh8U4F';

// Custom events for PlayAI (optional)
const events = [
    {
        name: "show-pricing",
        when: "The user asks to see pricing or wants to know about costs",
        data: {
            action: { type: "string", description: "Action to perform" },
        },
    },
    {
        name: "start-setup",
        when: "The user wants to start the AI setup process",
        data: {
            action: { type: "string", description: "Action to perform" },
        },
    },
] as const;

// Event handler for PlayAI events
const onEvent = (event: PlayAIEvent) => {
    console.log("PlayAI Event: ", event);
    if (event.name === "show-pricing") {
        showPricing();
    } else if (event.name === "start-setup") {
        router.visit(route('register'));
    }
};

// Initialize PlayAI and handle widget placement
const initializePlayAI = async () => {
    try {
        showLoader(true);
        // Open PlayAI embed
        await openEmbed(webEmbedId, { events, onEvent });

        // Wait for PlayAI to load and then move it to the button position
        setTimeout(async () => {
            await nextTick();
            // let playAIElement = document.querySelector('[id^="play-ai"]'); // Auto-generated ID
            // let targetButton = document.querySelector('.ai-start-btn'); // Target button class
            // if (playAIElement && targetButton) {
            //     // Replace the button with PlayAI widget
            //     targetButton.replaceWith(playAIElement);
            //     console.log('PlayAI widget moved to button position');
            // } else {
            //     console.log('PlayAI element or target button not found');
            // }

            showLoader(false);
        }, 3000); // Ensure PlayAI loads before moving

    } catch (error) {
        console.error('Error initializing PlayAI:', error);
        showLoader(false);
    }
};

// Cleanup PlayAI when component unmounts
const cleanupPlayAI = () => {
    try {
        // Remove PlayAI widget from DOM
        const playAIWidgets = document.querySelectorAll('[id^="play-ai"]');
        playAIWidgets.forEach(widget => {
            widget.remove();
        });

        // Remove any PlayAI iframes
        const playAIIframes = document.querySelectorAll('iframe[src*="play.ai"]');
        playAIIframes.forEach(iframe => {
            iframe.remove();
        });

        // Remove any PlayAI scripts
        const playAIScripts = document.querySelectorAll('script[src*="play.ai"]');
        playAIScripts.forEach(script => {
            script.remove();
        });

        // Remove any PlayAI styles
        const playAIStyles = document.querySelectorAll('style[data-play-ai]');
        playAIStyles.forEach(style => {
            style.remove();
        });

        // Clear any PlayAI global variables
        if (typeof window !== 'undefined') {
            // @ts-expect-error - PlayAI global variables not typed
            if (window.PlayAI) {
                // @ts-expect-error - PlayAI global variables not typed
                delete window.PlayAI;
            }
            // @ts-expect-error - PlayAI global variables not typed
            if (window.playAI) {
                // @ts-expect-error - PlayAI global variables not typed
                delete window.playAI;
            }
        }



        console.log('PlayAI cleanup completed');
    } catch (error) {
        console.error('Error during PlayAI cleanup:', error);
    }
};

onMounted(() => {
    initializePlayAI();
});

onUnmounted(() => {
    cleanupPlayAI();
});

const openUpgrade = () => {
    // if (!getUser()) {
    //     router.visit(route('login'));
    //     showToast('Login Required', 'You should logged in to proceed!', 'warning')
    //     return;
    // }
    upgradeDialog.user = getUser()!;
    upgradeDialog.upgradeDialogOpen = true;
}

</script>

<template>
    <GuestAppLayout class="bg-black">

        <!-- Hero Section -->
        <div class="relative flex flex-col justify-center items-center min-h-[calc(100dvh-65px)] bg-black">
            <!-- Main Content -->
            <div class="text-center max-w-4xl mx-auto px-4">
                <!-- Main Heading -->
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    Meet Your AI Advisor
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-2xl text-white/90 mb-12 max-w-3xl mx-auto leading-relaxed">
                    The Revamp 365 AI advisor is here to show you how to get the most out of your account.
                </p>

                <!-- Show Pricing Button -->
                <Button @click="openUpgrade" variant="default">
                    Show Pricing
                </Button>

                <!-- Bottom CTA -->
                <div class="mt-20">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-8">
                        Click Below to Start
                    </h2>

                    <!-- Target button for PlayAI replacement -->

                    <div v-if="loading" class="flex items-center justify-center gap-2  mx-auto">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                fill="none"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Loading AI...
                    </div>

                </div>
            </div>

        </div>
    </GuestAppLayout>
</template>

<style scoped>
/* Custom styles for the AI Onboarding page */
.min-h-screen {
    min-height: 100vh;
}

/* Ensure proper z-index layering */
.z-10 {
    z-index: 10;
}

/* Responsive text sizing */
@media (max-width: 768px) {
    h1 {
        font-size: 2.5rem;
        line-height: 1.2;
    }

    h2 {
        font-size: 2rem;
        line-height: 1.2;
    }
}

/* Button hover effects */
button {
    transform: translateY(0);
    transition: all 0.2s ease-in-out;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

/* Background gradient animation */
@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.bg-gradient-to-br {
    background-size: 200% 200%;
    animation: gradientShift 15s ease infinite;
}
</style>