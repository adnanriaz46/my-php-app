<script setup lang="ts">
import { onMounted } from 'vue';
// import axios from 'axios';
import { DBApiSoldUnderMarketValue } from '@/types/DBApi';
import { Icon } from '@iconify/vue';
import { useDateFormat, useNumber } from '@/composables/useFormat';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Pagination, EffectCoverflow } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/effect-coverflow';


const { formatNumber, formatPrice } = useNumber()
const { formatDate } = useDateFormat()
// const { formatToCapitalizeEachWord, } = useTextFormat()


defineProps<{
    properties: DBApiSoldUnderMarketValue[];
}>();


// const testimonials = ref<DBApiSoldUnderMarketValue[]>([]);
// const cKey = ref<number>(0);
onMounted(async () => {
    // const response = await axios.get(route('get.data.testimonials'));
    // testimonials.value = response.data;
    // cKey.value++;
});

</script>

<template>
    <div class="w-full pt-10 pb-10 mb-8 px-4 flex flex-col gap-12">
        <div class="w-full  mx-auto text-2xl">
            <Swiper v-if="properties.length > 0" :key="1" :modules="[Autoplay, Pagination, EffectCoverflow]"
                :effect="'coverflow'" :coverflowEffect="{
                    rotate: 50,
                    stretch: 0,
                    depth: 0,
                    modifier: 1,
                    slideShadows: false,

                }" :speed="1200" :slides-per-view="3" :space-between="0" :loop="true" :autoplay="{
                    delay: 5000,
                    disableOnInteraction: false,
                }" :pagination="{
                    clickable: true,
                    dynamicBullets: true,
                }" :breakpoints="{
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 7
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 0
                    },
                    1280: {
                        slidesPerView: 4.5,
                        spaceBetween: 0
                    },
                    1636: {
                        slidesPerView: 5,
                        spaceBetween: 0
                    }
                }" class="testimonials-swiper testimonials-3d">
                <SwiperSlide v-for="property in properties" :key="property.id">
                    <div class="carousel__item pt-15 select-none">
                        <div @click="$emit('click')"
                            class="rounded-lg shadow-md text-sm font-normal cursor-pointer dark:border dark:border-gray-700 hover:shadow-lg hover:dark:shadow-gray-700 transition-all duration-300 bg-white dark:bg-gray-800/40">
                            <div class="img relative h-[220px] p-2 bg-white rounded-t-lg bg-cover bg-center"
                                :style="{ backgroundImage: `url(${property.image})` }">
                                <div class="property-status absolute top-2 left-2">
                                    <span
                                        class="text-white text-xs bg-red-700/80 rounded-lg px-2 py-0.5 shadow-md">Closed
                                        {{ formatDate(property.close_date) }}</span>
                                </div>

                                <div class="absolute bottom-2 right-2 flex flex-col items-end gap-2 text-sm">
                                    <div
                                        class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold">
                                        {{ property.real_dom }} DOM
                                    </div>
                                    <div
                                        class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold flex">
                                        <Icon icon="tabler:flame" class="wholesale-icon"
                                            v-if="property.wholesale == 'Wholesale'" />
                                        {{ property.wholesale }}
                                        Listing
                                        <Icon icon="tabler:flame" class="wholesale-icon"
                                            v-if="property.wholesale == 'Wholesale'" />
                                    </div>
                                </div>
                            </div>
                            <div class="bottom-container">
                                <div class="price-container flex pb-1 p-2 gap-2 px-2">
                                    <div class="font-semibold text-lg/4 my-auto">{{
                                        formatPrice(property.close_price)
                                        }}
                                    </div>
                                    <div
                                        class="text-xs text-muted-foreground align-bottom my-auto truncate overflow-hidden whitespace-nowrap">
                                        By
                                        {{ property.office_info }}
                                    </div>
                                </div>
                                <div class="address-container py-1 px-2 truncate overflow-hidden whitespace-nowrap">
                                    <span>{{ property.address }}</span>
                                </div>
                                <div class="flex flex-row p5-2 gap-2 px-2 justify-start">
                                    <div class="flex-shrink flex flex-row gap-2 justify-start">
                                        <div class="truncate"><span class="font-semibold">{{
                                            formatNumber(property.bedrooms_count) }}</span> bds</div>
                                        <div class="truncate">|&nbsp; <span class="font-semibold"> {{
                                            formatNumber(property.bathrooms_total_count) }}</span>
                                            ba
                                        </div>
                                        <div class="truncate">|&nbsp; <span class="font-semibold"> {{
                                            formatNumber(property.total_finished_sqft)
                                                }}</span> sqft
                                        </div>
                                    </div>
                                    <div class="truncate"><span class="text-sm">| &nbsp; {{
                                        property.structure_type }}</span></div>
                                </div>
                                <div class="grid grid-cols-3 justify-around bg-gray-300 dark:bg-gray-700 py-2 mt-2">
                                    <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
                                        formatPrice(property.medianrent) }}</span>Rent
                                    </div>
                                    <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
                                        formatPrice(property.est_avm) }}</span>AVM
                                    </div>
                                    <div class="text-xs text-center"><span class="font-semibold mr-1 text-sm">{{
                                        formatPrice(property.est_arv) }}</span>ARV
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 justify-around text-xs mt-1">
                                    <div class="grid grid-cols-1">
                                        <div class="text-center font-medium">Est Cash Flow</div>
                                        <div class="text-center"
                                            :class="{ 'text-green-500': property.est_cashflow > 0 }">
                                            {{ formatPrice(property.est_cashflow) }}</div>
                                    </div>
                                    <div class="grid grid-cols-1">
                                        <div class="text-center font-medium">Delta PSF</div>
                                        <div class="text-center">{{ formatPrice(property.delta_psf) }}</div>
                                    </div>
                                    <div class="grid grid-cols-1">
                                        <div class="text-center font-medium">Est Flip Profit</div>
                                        <div class="text-center"
                                            :class="{ ' text-green-500': property.est_profit > 0 }">
                                            {{ formatPrice(property.est_profit) }}</div>
                                    </div>
                                </div>
                                <div class="p-1 bg-transparent"></div>
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