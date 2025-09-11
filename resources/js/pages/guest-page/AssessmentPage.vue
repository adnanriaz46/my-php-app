<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import GuestAppLayout from './reusables/GuestAppLayout.vue';
import { GuestFeature } from './vars/vars';
import GuestDyamicFeature from './reusables/GuestDyamicFeature.vue';
import GuestSmartCards from './reusables/GuestSmartCards.vue';
import GuestAssessment from './reusables/GuestAssessment.vue';
import GuestTestimonials from './reusables/GuestTestimonials.vue';



const features: GuestFeature[] = [
    {
        title: '',
        description: `<div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">About The REI Blueprint</h2>
                        <h2 class="text-4xl font-semibold text-primary mb-6">Empowering Real Estate Investors</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                            We're dedicated to providing innovative tools and a comprehensive platform that serves 
                            as your customized "launchpad" for real estate investment success.
                        </p>
                        
                        <!-- Key Benefits -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 bg-primary rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700 dark:text-gray-200">Personalized Investment Strategies</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 bg-primary rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700 dark:text-gray-200">Real-Time Market Insights</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 bg-primary rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700 dark:text-gray-200">Actionable Investment Plans</span>
                            </div>
                        </div>
                    </div>`,
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/689045ae19fc6.jpg',
        iframe: ''
    }
]


// FAQ data
const faqs = ref([
    {
        question: 'Is the REI Blueprint assessment right for me?',
        answer: `<p>If you are in a real estate investor or serve investors in any way, our software and this assessment are designed for you. </p>
                <p>We have all different types of users like Fix & Flippers, Landlords, Agents & Brokers, Wholesalers, Sellers, service providers, and everything in between.</p>`,
        isOpen: false
    },
    {
        question: 'What is the point of the REI Blueprint assessment? Do I need to do it?',
        answer: `<p>The REI Blueprint assessment is designed to help you analyze your real estate investment goals and strategies.</p><p> It provides personalized feedback and an action plan to optimize your investment approach. Whether you are a novice or an experienced investor, the assessment is a valuable tool for refining your strategy and maximizing returns.</p>`,
        isOpen: false
    },
    {
        question: 'How does it work?',
        answer: `<p>The assessment is a simple and quick process - you don't even need to log in and create a free account if you don't want to! You start answering a series of questions about your investment goals and strategies. Once completed, you will receive a detailed report with customized insights and recommendations to help you achieve your investment objectives.</p>`,
        isOpen: false
    }
]);



// Process steps
const processSteps = ref([
    {
        number: '01',
        title: 'Navigate to the Blueprint',
        description: 'Begin by entering your contact info in our free REI Blueprint Assessment. If you already have an account the assessment will auto-fill this info. Get ready to embark on your investment journey.'
    },
    {
        number: '02',
        title: 'Complete the Assessment',
        description: 'Answer a series of targeted questions about your investment goals and strategies. This helps us tailor a personalized action plan to suit your unique needs.'
    },
    {
        number: '03',
        title: 'Receive Your Custom Plan',
        description: 'Once you\'ve completed the assessment, you\'ll receive a detailed report with actionable insights and recommendations designed to help you achieve your investment objectives.'
    },
    {
        number: '04',
        title: 'Implement and Succeed',
        description: 'Use the insights and resources provided in your custom Blueprint to refine your strategies and maximize your investment returns. Leverage our platform and community for ongoing support and success.'
    }
]);

const smartCards = [
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cbaea4e62.svg',
        title: 'Buy Box Filters for Every Client',
        description: 'Easily manage multiple investor strategies Set a custom buy box for each investor—whether they’re flipping, BRRRRing, or buying turnkey rentals. Get matched listings instantly.',
        alt: 'Deal Intelligence Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cbe22416d.svg',
        title: 'Comps & Numbers Built In',
        description: 'Answer “Is this a good deal?” in seconds Pull ARV, rental comps, cash flow, and ROI with one click—no spreadsheets, no guesswork, just investor-ready analysis.',
        alt: 'Speed & Automation Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cc404e62b.svg',
        title: 'MLS Meets Off-Market Inventory',
        description: 'Deliver deals they can’t find anywhere else Combine MLS, wholesale, pre-foreclosure, and investor-grade listings in one platform. Beat Zillow. Beat Redfin.',
        alt: 'Built for Real Investors Icon'
    },
    {
        image: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cc217146b.svg',
        title: 'Branded Deal Reports & Alerts',
        description: 'Look like the expert, every time Share pro-level property reports and automated deal alerts—fully branded to you. Keep your clients loyal and impressed.',
        alt: 'Investor Reporting Icon'
    }
]


// Features data
const features2 = ref([
    {
        icon: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cd4a07a85.svg',
        title: 'Insights',
        description: 'Gain deep insights into your investment strategies for informed decision-making.'
    },
    {
        icon: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cc217146b.svg',
        title: 'Efficiency',
        description: 'Streamline your investment process with automated tools and expert recommendations.'
    },
    {
        icon: 'https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cbaea4e62.svg',
        title: 'Support',
        description: 'Connect with a community of experts and like-minded investors for ongoing support.'
    }
]);

const toggleFaq = (index: number) => {
    faqs.value[index].isOpen = !faqs.value[index].isOpen;
};

const startAssessment = () => {
    router.visit(route('guest.do_assessment'));
};
</script>

<template>
    <GuestAppLayout>
        <!-- Hero Section -->
        <div
            class="relative bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 pt-20 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight dark:text-white">
                        Get Your Custom <span class="text-primary">REI Blueprint</span>
                    </h1>
                    <p
                        class="text-xl md:text-2xl text-gray-600 mb-8 max-w-4xl mx-auto leading-relaxed dark:text-white/90">
                        Analyze Your Goals, Refine Your Strategies, and Propel Your Success with Our Free REI Blueprint
                        Assessment.
                    </p>

                    <!-- CTA Button -->
                    <button @click="startAssessment"
                        class="bg-primary hover:bg-primary/90 text-primary-foreground font-semibold py-4 px-8 rounded-lg text-lg transition-colors duration-200 mb-4 cursor-pointer">
                        Start Your Free Assessment
                    </button>
                    <p class="text-sm text-muted-foreground">No credit card required</p>
                </div>

                <!-- Statistics -->
                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 py-10 border-b border-t border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-black dark:text-white mb-2">2<span
                                class="text-muted-foreground">K+</span></div>
                        <div class="text-gray-600 dark:text-white/90">users & growing every day</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-black dark:text-white mb-2">1.2<span
                                class="text-muted-foreground">M</span></div>
                        <div class="text-gray-600 dark:text-white/90">closed deals no use-up dumps</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-black dark:text-white mb-2">114<span
                                class="text-muted-foreground">M</span></div>
                        <div class="text-gray-600 dark:text-white/90">data points analyzed</div>
                    </div>
                </div>
            </div>
        </div>


        <GuestDyamicFeature :features="features" />



        <!-- Features & About Section -->
        <div class="py-20 px-4 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">FEATURES</h2>
                        <h3 class="text-4xl font-semibold text-primary mb-6">Enhance Your Investment Strategy Today</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-2 leading-relaxed">
                            Our comprehensive assessment tool empowers you to refine your investment approach. Analyze
                            your goals, streamline your strategies, and gain actionable insights with our advanced
                            evaluation methods.
                        </p>
                        <p class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                            Optimize your investments and achieve unparalleled success with our expert guidance and
                            tailored action plans.
                        </p>
                    </div>
                    <div>
                        <!-- Feature Icons -->
                        <div class="space-y-6">
                            <div v-for="feature in features2" :key="feature.title" class="flex items-start gap-4">
                                <div class="text-5xl"><img :src="feature.icon" :alt="feature.title"
                                        class="min-w-20 max-w-20 min-h-20 max-h-20"></div>
                                <div>
                                    <h4 class="font-semibold text-2xl text-gray-900 dark:text-white mb-2">{{
                                        feature.title }}
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-300">{{ feature.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="py-20 px-4 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-5xl mx-auto text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">FAQ'S</h2>
                <h3 class="text-xl font-semibold text-primary">Find Solutions To Your Queries</h3>
            </div>

            <div class="max-w-5xl mx-auto select-none">
                <div class="bg-gradient-to-r from-amber-900 via-amber-700 to-amber-600 rounded-2xl p-8">
                    <div class="space-y-4">
                        <div v-for="(faq, index) in faqs" :key="index"
                            class="bg-white dark:bg-gray-900 rounded-lg p-6 cursor-pointer" @click="toggleFaq(index)">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-gray-900 dark:text-white text-xl">{{ faq.question }}</h4>
                                <div
                                    class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 transition-transform duration-200"
                                        :class="{ 'rotate-45': faq.isOpen }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div v-if="faq.isOpen" class="mt-4 text-gray-600 dark:text-gray-300 leading-relaxed text-lg"
                                v-html="faq.answer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <GuestSmartCards mainTitle="BENEFITS" mainDescription="Unlocking Key Benefits for Your Investment Success"
            :cards="smartCards" />

        <!-- Benefits Section
        <div class="py-20 px-4 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">BENEFITS</h2>
                <h3 class="text-xl font-semibold text-primary">Unlocking Key Benefits for Your Investment Success</h3>
            </div>

            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="benefit in benefits" :key="benefit.title"
                        class="text-center p-6 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
                        <div class="text-4xl mb-4">{{ benefit.icon }}</div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">{{ benefit.title }}</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm" v-html="benefit.description"></p>
                    </div>
                </div>
            </div>
        </div> -->


        <GuestAssessment />

        <!-- Process Section -->
        <div class="py-20 px-4 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Leverage the Revamp Blueprint to Achieve Your Investment Goals
                </h2>
            </div>

            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div v-for="step in processSteps" :key="step.number"
                        class="p-8 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold">
                                    {{ step.number }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ step.title }}</h4>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ step.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <GuestTestimonials />

        <!-- Final CTA Section -->
        <div class="py-20 px-4 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <!-- Left Side -->
                    <div class="text-gray-900 dark:text-white">
                        <h2 class="text-3xl font-bold mb-6">LET'S GO</h2>
                        <h3 class="text-xl font-semibold text-primary mb-8">We're Here To Make Magic Happen!</h3>

                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div
                                        class="min-w-12 max-w-12 min-h-12 max-h-12 rounded-lg flex items-center justify-center">
                                        <img src="https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cd4a07a85.svg"
                                            alt="Let's icon" class="w-full h-full">
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg mb-2">Let's...</h4>
                                    <p class="text-gray-600 dark:text-gray-300">You have nothing to lose. Take the REI
                                        Blueprint Assessment now for free.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div
                                        class="min-w-12 max-w-12 min-h-12 max-h-12 rounded-lg flex items-center justify-center">
                                        <img src="https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cc217146b.svg"
                                            alt="Get icon" class="w-full h-full">
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg mb-2">Get...</h4>
                                    <p class="text-gray-600 dark:text-gray-300">You don't even need to create an account
                                        or sign up for our paid memberships.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div
                                        class="min-w-12 max-w-12 min-h-12 max-h-12 rounded-lg flex items-center justify-center">
                                        <img src="https://revamp365-storage.s3.amazonaws.com/assets/assets/6890cbaea4e62.svg"
                                            alt="Started icon" class="w-full h-full">
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg mb-2">Started!</h4>
                                    <p class="text-gray-600 dark:text-gray-300">The advice and feedback you will receive
                                        will yield you results entirely independently of using our services.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="text-center border-2 border-gray-200 dark:border-gray-700 rounded-xl p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Get your free REI Blueprint
                            NOW</h2>

                        <!-- AI Assisted Strategy Image -->
                        <div class="mb-8 bg-gray-50 dark:bg-gray-800 p-6 rounded-xl max-w-md mx-auto">
                            <img src="https://revamp365-storage.s3.amazonaws.com/assets/assets/6890d52e49044.jpg"
                                alt="AI Assisted Strategy" class="rounded-lg shadow-xl mx-auto max-w-full">
                        </div>

                        <button @click="startAssessment"
                            class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold py-4 px-8 rounded-lg text-lg transition-all duration-200 mb-4 cursor-pointer shadow-lg">
                            Start Your Free Assessment
                        </button>
                        <div class="flex items-center justify-center gap-2 text-red-500 italic text-sm">
                            <span>No credit card required</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestAppLayout>
</template>

<style scoped>
/* Custom styles for the assessment page */
.bg-gradient-to-br {
    background: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

/* Ensure proper spacing and responsive design */
@media (max-width: 768px) {
    .text-6xl {
        font-size: 2.5rem;
    }

    .text-4xl {
        font-size: 2rem;
    }
}
</style>