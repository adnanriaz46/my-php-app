<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Testimonial } from '@/types';
import { Icon } from '@iconify/vue';
import { useDateFormat } from '@/composables/useFormat';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Pagination, EffectCoverflow } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/effect-coverflow';


const { formatDate } = useDateFormat();


const testimonials = ref<Testimonial[]>([]);
const cKey = ref<number>(0);
onMounted(async () => {
    const response = await axios.get(route('get.data.testimonials'));
    testimonials.value = response.data;
    cKey.value++;
});

</script>

<template>
    <div class="w-full pt-10 pb-10 mb-8 px-4 flex flex-col gap-12"
        style="background-image: url('https://revamp365-storage.s3.amazonaws.com/assets/assets/687fcda26b37a.png');">
        <div class="w-full  mx-auto text-2xl">
            <h2 class="text-2xl font-bold text-center mb-10 text-white">What Our Users Say</h2>
            <Swiper v-if="testimonials.length > 0"
                :key="cKey"
                :modules="[Autoplay, Pagination,EffectCoverflow]"
                :effect="'coverflow'"
                :coverflowEffect="{
                    rotate: 50,
                    stretch: 0,
                    depth: 0,
                    modifier: 1,
                    slideShadows: false
                
                }"
                :speed="1200"
                :slides-per-view="3"
                :space-between="0"
                :loop="true"
                :autoplay="{
                    delay: 5000,
                    disableOnInteraction: false,
                }"
                :pagination="{
                    clickable: true,
                    dynamicBullets: true,
                }"
                :overflow="true"
                :centered-slides="true"
                :allow-touch-move="true"

                :breakpoints="{
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 7
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 0
                    },
                    1280: {
                        slidesPerView: 5,
                        spaceBetween: 0
                    },
                    1636: {
                        slidesPerView: 5,
                        spaceBetween: 0
                    }
                }"
                class="testimonials-swiper testimonials-3d"
            >
                <SwiperSlide v-for="testimonial in testimonials" :key="testimonial.id">
                    <div class="carousel__item pt-15 select-none">
                        <div class="bg-white rounded-2xl shadow-xl p-6 pt-10 mx-auto text-black relative">
                            <div class="flex items-center gap-4 mb-4 absolute -top-10 left-1/2 transform -translate-x-1/2">
                                <img :src="testimonial.profile_image || ''" alt="User"
                                    class="w-20 h-20 rounded-full object-cover border" />
                            </div>
                            <div class="flex flex-col items-start justify-start gap-1 mb-2">
                                <h3 class="text-lg font-medium">{{ testimonial.name }}</h3>
                                <div class="flex items-center gap-2">
                                    <Icon icon="mdi:star" class="text-yellow-500" v-for="i in testimonial.rate" :key="i" />
                                </div>
                            </div>
                            <div class="flex items-center gap-4 mb-4 max-h-[280px] overflow-y-auto">
                                <p class="text-sm">{{ testimonial.description }}</p>
                            </div>
                            <div class="flex items-center gap-4 mb-4">
                                <p class="text-sm text-gray-500">{{ formatDate(testimonial?.published_date || '') }}</p>
                            </div>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
            
        </div>
    </div>
</template>

<style scoped>
.testimonials-swiper {
    padding-bottom: 50px;
}

.testimonials-swiper :deep(.swiper-pagination) {
    bottom: 0;
}

.testimonials-swiper :deep(.swiper-pagination-bullet) {
    background: rgba(255, 255, 255, 0.5);
    opacity: 1;
}

.testimonials-swiper :deep(.swiper-pagination-bullet-active) {
    background: white;
}

.testimonials-swiper :deep(.swiper-button-next),
.testimonials-swiper :deep(.swiper-button-prev) {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    backdrop-filter: blur(10px);
}

.testimonials-swiper :deep(.swiper-button-next:hover),
.testimonials-swiper :deep(.swiper-button-prev:hover) {
    background: rgba(255, 255, 255, 0.2);
}

.testimonials-swiper :deep(.swiper-button-next::after),
.testimonials-swiper :deep(.swiper-button-prev::after) {
    font-size: 16px;
    font-weight: bold;
}
</style>