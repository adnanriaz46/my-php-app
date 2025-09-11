<script setup lang="ts">
import { computed, HTMLAttributes, nextTick, onMounted, ref } from "vue";
import { DBApi, DBApiCalValData, DBApiPropertyFull, DBApiPropertyList } from "@/types/DBApi";
import { Icon } from '@iconify/vue'
import axios from "axios";
import { PropertyStatusBadge } from "@/components/ui/proprety-status-badge";
import { Tooltip, TooltipContent, TooltipTrigger } from "@/components/ui/tooltip";
import { useDateFormat, useNumber } from "@/composables/useFormat";
import { getMainImage, PropertyStatus } from "../../types/property";
import Separator from "@/components/ui/separator/Separator.vue";
import { cn } from "@/lib/utils";
import { ZillowPListingMain } from "@/lib/zilowAndlocationUtil";
import Markdown from "../ui/markdown/Markdown.vue";
import { getAiSummaryPrompt } from "../property-dialogs";
import { getProperty } from "@/lib/DBApiUtil";

const props = defineProps<{
    propertyListData: DBApiPropertyList,
    selectionIds?: number[],
    class?: HTMLAttributes['class']
    showBtnFavorite?: boolean,
    showSelection?: boolean,
    showDistanceFrom?: boolean,
    isMap?: boolean,
    calculatedData?: DBApiCalValData | null,
    subjectPropertyData?: ZillowPListingMain, // Subject property data for comparison
}>()

const emit = defineEmits<{
    (e: 'click'): void;
    (e: 'update:selection-ids', value: number[]): void
}>();

// const selectionId = ref<number | null>(null); // For individual selection
const aiSummary = ref<string>('');
const loadingAiSummary = ref<boolean>(false);

const { formatNumber, formatPrice } = useNumber()
const { formatDate, formatGetDays } = useDateFormat()
// const { formatToCapitalizeEachWord, } = useTextFormat()

const propertyImage = ref<string>('');
const showFullSummary = ref<boolean>(false);

onMounted(async () => {
    propertyImage.value = getMainImage(props.propertyListData.image);

    nextTick(() => {
        if (location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            loadAiSummary();
        }
    })
})

const onCheck = () => {
    if (!props.selectionIds) return;

    const isSelected = props.selectionIds.includes(props.propertyListData.id);
    const updatedSelection = isSelected
        ? props.selectionIds.filter(id => id !== props.propertyListData.id)
        : [...props.selectionIds, props.propertyListData.id];

    emit('update:selection-ids', updatedSelection);
};

const loadAiSummary = async () => {
    loadingAiSummary.value = true;
    try {
        const summary = await getAiSummary(props.propertyListData, props.subjectPropertyData!);
        if (summary) {
            aiSummary.value = summary.content || summary;
        }
    } catch (error) {
        console.error('Failed to load AI summary:', error);
    } finally {
        loadingAiSummary.value = false;
    }
}

const getPropertyData = async (propertyId: number) => {
    const propertyData: DBApi<DBApiPropertyFull[]> = await getProperty({ id: propertyId });
    return propertyData.data?.[0];

}

const getAiSummary = async (propertyList: DBApiPropertyList, zillowData: ZillowPListingMain) => {
    // Try fallback to non-streaming request

    // const messages = [
    //     {
    //         role: 'user',
    //         content: `Please generate a very brief summary comparing the subject property to this comparable property:

    //         Main(Subject) Property:
    //         Address: ${zillowData?.address?.streetAddress}, ${zillowData?.address?.city}, ${zillowData?.address?.state} ${zillowData?.address?.zipcode}
    //         Year Built: ${zillowData?.yearBuilt}
    //         Zestimate Value: ${zillowData?.zestimate}
    //         List Price: ${zillowData?.price}
    //         Beds: ${zillowData?.bedrooms}
    //         Baths: ${zillowData?.bathrooms}
    //         Total Finished Sqft: ${zillowData?.livingArea}
    //         Lot Sqft: ${zillowData?.resoFacts?.lotSize}
    //         Median Rent: ${zillowData?.rentZestimate}
    //         Posted Date: ${zillowData?.datePosted}

    //         This Comparable Property:
    //         Address: ${propertyList.address}
    //         Close Price(Sold Price): ${propertyList.close_price}
    //         List Price: ${propertyList.list_price}
    //         Close Date(Sold Date): ${propertyList.close_date}
    //         Listing Entry Date: ${propertyList.listing_entry_date}
    //         Bedrooms: ${propertyList.bedrooms_count}
    //         Bathrooms: ${propertyList.bathrooms_total_count}
    //         Total Finished Sqft: ${propertyList.total_finished_sqft}
    //         Lot Sqft: ${propertyList.lot_sqft}
    //         School District: ${propertyList.school_district_name}
    //         Year Built: ${propertyList.year_built}
    //         Median Rent: ${propertyList.medianrent}
    //         AVM Value: ${propertyList.est_avm}

    //         Please provide a brief analysis of how this comparable property relates to the subject property, including price comparison, size comparison(total finished sqft, lot sqft), bedrooms and baths comparison, year built comparison, and market timing insights.

    //         example1: This comparable property sold for $450K, which is 15% higher than your subject property's estimated value of $390K. The comp is 200 total finished sqft larger but in a similar subject property. It sold quickly (45 days), suggesting strong market demand in this area.
    //         example2: This comparable property is [sqft] larger than subject property, bedroom and bath are exactly same as subject, its sold [x days] after listing, [avm] is similar to suject proprety [zestimate], this property almost matching with subject.
    //         example3: This comparable property is [sqft] smaller than suject property, bedroom and bath almost matched, its sold [x days] after listing, [avm] is little higher to suject proprety [zestimate], this property little less matching with subject.`
    //     }
    // ]; 

    const propertyData = await getPropertyData(propertyList.id);
    if (!propertyData) return null;

    const messages = [
        {
            role: 'user',
            content: getAiSummaryPrompt(zillowData, propertyData, props?.calculatedData || null)
        }
    ];

    try {
        const fallbackResponse = await axios.post(route('property.request.ask_ai'), {
            messages: messages,
            stream: false
        });
        const reply = fallbackResponse.data.choices[0].message;
        return reply;
    } catch (fallbackErr) {
        console.log("Fallback also failed:", fallbackErr);
        return null;
    }
}

const isLocalhost = computed(() => {
    return location.hostname === 'localhost' || location.hostname === '127.0.0.1';
});

</script>

<template class="relative">
    <div @click="$emit('click')"
        class="rounded-lg shadow-md text-sm font-normal cursor-pointer dark:border dark:border-gray-700 hover:shadow-lg hover:dark:shadow-gray-700 transition-all duration-300 bg-white dark:bg-gray-800/40"
        :class="cn(props.class)">
        <div class="img relative h-[220px] p-2 bg-white rounded-t-lg bg-cover bg-center"
            :style="{ backgroundImage: `url(${propertyImage})` }">
            <div class="property-status absolute top-2 left-2">
                <PropertyStatusBadge :status="propertyListData.status" />
            </div>
            <div class="action-buttons absolute top-2 right-2 flex gap-2">
                <div v-if="showBtnFavorite" class="flex-1 p-1 rounded-lg bg-gray-800/40">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Icon class=" text-white hover:text-primary action-icon" icon="tabler:heart" />
                        </TooltipTrigger>
                        <TooltipContent side="bottom" align="end">
                            Add to favorite
                        </TooltipContent>
                    </Tooltip>
                </div>
                <div class="flex-1 p-1 rounded-lg bg-gray-800/40 cursor-pointer select-none" v-if="showSelection"
                    @click.stop="onCheck">
                    <Icon
                        :class="' action-icon ' + (selectionIds?.includes(propertyListData.id) ? ' text-primary ' : 'text-white hover:text-primary ')"
                        :icon="selectionIds?.includes(propertyListData.id) ? 'tabler:square-rounded-check-filled' : 'tabler:square-rounded'" />
                </div>
            </div>
            <div class="absolute bottom-2 left-2 flex flex-col items-end gap-2 text-sm" v-if="showDistanceFrom">
                <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold">
                    {{ propertyListData.mile_range_from_subject }}
                    Miles
                </div>
            </div>
            <div class="absolute bottom-2 right-2 flex flex-col items-end gap-2 text-sm">
                <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold"
                    v-if="propertyListData.status == PropertyStatus.Closed">{{
                        formatDate(propertyListData.close_date)
                    }}
                </div>
                <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold"
                    v-if="propertyListData.status != PropertyStatus.Closed">{{ propertyListData.real_dom }} DOM
                </div>
                <div class="rounded-lg bg-gray-800/70 text-white py-1 px-2 w-fit text-right font-semibold flex">
                    <Icon icon="tabler:flame" class="wholesale-icon" v-if="propertyListData.wholesale == 'Wholesale'" />
                    {{ propertyListData.wholesale }}
                    Listing
                    <Icon icon="tabler:flame" class="wholesale-icon" v-if="propertyListData.wholesale == 'Wholesale'" />
                </div>
            </div>
        </div>
        <div class="bottom-container grid grid-cols-1 gap-2">
            <div class="price-container flex pb-1 pt-2 gap-2 px-2">
                <div class="font-semibold text-lg/4 my-auto">{{
                    propertyListData.status == PropertyStatus.Closed ? formatPrice(propertyListData.close_price) :
                        formatPrice(propertyListData.list_price)
                }}
                </div>
                <div
                    class="text-xs text-muted-foreground align-bottom my-auto truncate overflow-hidden whitespace-nowrap">
                    By
                    {{ propertyListData.office_info }}
                </div>
            </div>
            <div class="address-container px-2 truncate overflow-hidden whitespace-nowrap">
                <span v-if="propertyListData.wholesale != 'Wholesale'">{{ propertyListData.address }}</span>
                <span v-else><span class="blur-sm">[Hidden Address]</span>, {{
                    propertyListData.address?.split(',')?.pop()?.trim()
                    }}</span>
            </div>
            <div class="flex flex-row p5-2 gap-2 px-2 justify-start">
                <Icon icon="tabler:home-2" class="size-5" />
                <span class="font-semibold">{{ propertyListData.bedrooms_count }}</span> Beds,
                <span class="font-semibold"> {{ propertyListData.bathrooms_total_count
                }}</span> Baths
                <span v-if="propertyListData.bedrooms_count == subjectPropertyData?.bedrooms"
                    class="text-xs px-2 py-0.5 bg-green-200 dark:bg-green-700 rounded-lg"> Exact Beds
                </span>
                <span v-if="propertyListData.bathrooms_total_count == subjectPropertyData?.bathrooms"
                    class="text-xs px-2 py-0.5 bg-green-200 dark:bg-green-700 rounded-lg"> Exact Baths
                </span>
            </div>
            <div class="flex flex-row p5-2 gap-2 px-2 justify-start">
                <Icon icon="tabler:box-align-bottom-left" class="size-5" />
                <div class="truncate"> {{
                    formatNumber(propertyListData.total_finished_sqft)
                }} sqft;
                </div>
                <div class="truncate"> {{
                    formatNumber(propertyListData.lot_sqft)
                }} sqft
                </div>
                <span v-if="propertyListData.total_finished_sqft == subjectPropertyData?.livingArea"
                    class="text-xs px-2 py-0.5 bg-green-200 dark:bg-green-700 rounded-lg"> Exact Sqft
                </span>
                <span v-if="propertyListData.lot_sqft == Number(subjectPropertyData?.resoFacts?.lotSize)"
                    class="text-xs px-2 py-0.5 bg-green-200 dark:bg-green-700 rounded-lg"> Exact Lot
                </span>
            </div>
            <div class="flex flex-row p5-2 gap-2 px-2 justify-start">
                <Icon icon="tabler:school" class="size-5" />
                <div class="truncate"> {{
                    propertyListData.school_district_name ?? '-'
                }}
                </div>
            </div>
            <div class="flex flex-row p5-2 gap-2 px-2 justify-start">
                <Icon icon="tabler:building-community" class="size-5" />
                <div class="truncate"> {{
                    propertyListData.year_built ?? '-'
                }}
                </div>
                <span v-if="propertyListData.year_built == subjectPropertyData?.yearBuilt"
                    class="text-xs px-2 py-0.5 bg-green-200 dark:bg-green-700 rounded-lg"> Exact Year Built
                </span>
            </div>
            <div class="flex flex-row p5-2 gap-2 px-2 justify-start"
                v-if="propertyListData.status == PropertyStatus.Closed">
                <Icon icon="tabler:calendar-event" class="size-5" />
                <div class="truncate"> {{
                    formatDate(propertyListData.close_date)
                }} ({{ Math.round(formatGetDays(propertyListData.close_date) / 30) }} months ago)
                </div>
            </div>
            <div class="flex flex-row p5-2 gap-2 px-2 justify-start">
                <Icon icon="tabler:clock" class="size-5" />
                <div class="truncate"> {{ formatNumber(Math.round(propertyListData.dom)) }} days on market
                </div>
            </div>
            <Separator class="w-full my-2" v-if="propertyListData.status == PropertyStatus.Closed" />
            <!-- AI Summary Section -->
            <div class="px-2 pb-2">
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <Icon icon="tabler:sparkles" class="size-5 text-primary" />
                        <span class="font-semibold text-sm">AI Analysis</span>
                    </div>

                    <!-- Load AI Summary Button FOR localhost-->
                    <button v-if="!aiSummary && !loadingAiSummary && isLocalhost" @click.stop="loadAiSummary"
                        class="flex items-center gap-1 text-xs text-primary hover:text-primary/80 cursor-pointer transition-colors">
                        <Icon icon="tabler:sparkles" class="size-3" />
                        <span>Get Brief Analysis</span>
                    </button>
                </div>

                <div v-if="loadingAiSummary" class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Icon icon="tabler:loader-2" class="size-4 animate-spin" />
                    <span>Generating brief property analysis...</span>
                </div>

                <div v-else-if="aiSummary" @click.stop="showFullSummary = !showFullSummary"
                    class="text-sm text-muted-foreground bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2 flex items-center gap-2">
                    <!-- <Icon icon="tabler:sparkles" class="min-w-5 min-h-5 text-primary" /> -->
                    <div :class="{ 'line-clamp-4': !showFullSummary }">
                        <Markdown :content="aiSummary" :avoidTailwind="false" />
                    </div>
                    <!-- See More/Less Button -->
                    <button v-if="aiSummary.length > 200"
                        class="mt-2 text-xs text-primary hover:text-primary/80 cursor-pointer transition-colors">
                        {{ showFullSummary ? 'See less' : 'See more' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.action-icon {
    width: 25px;
    height: 25px;
}

.wholesale-icon {
    color: orange;
    width: 18px;
    height: 18px;
}

.line-clamp-4 {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>