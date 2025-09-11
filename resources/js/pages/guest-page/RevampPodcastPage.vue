<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import GuestAppLayout from './reusables/GuestAppLayout.vue'
import { getPropertyList } from '@/lib/DBApiUtil'
import { DBApiPropertyList, paramsDBApiGetProperty } from '@/types/DBApi'


type PodcastEpisode = {
  id: string
  title?: string
  episode?: number | string | null
  date?: string | null
  host?: string | null
  guest?: string | null
  summary?: string | null
  thumb?: string | null
  audio_url?: string | null
  video_url?: string | null
}
type PodcastPageProps = {
  episodes: PodcastEpisode[]
  nextOffset: string | null
}

const hotDeals = ref<DBApiPropertyList[]>([])
const loadingHotDeals = ref(false)

// Fetch hot deals on component mount
onMounted(async () => {
  await fetchHotDeals()
})

async function fetchHotDeals() {
  loadingHotDeals.value = true
  try {
    const params: paramsDBApiGetProperty = {
      status: ['Active'],
      all_wholesale: 1,
      _limit: 3, // Only get 3 properties
      order_by: 'list_price' // Order by price
    }
    
    const response = await getPropertyList(params)
    if (response?.data) {
      hotDeals.value = response.data
    }
  } catch (error) {
    console.error('Error fetching hot deals:', error)
  } finally {
    loadingHotDeals.value = false
  }
}

const page = usePage() as unknown as { props: PodcastPageProps }
const episodes = ref<PodcastEpisode[]>(page.props.episodes ?? [])
const nextOffset = ref<string | null>(page.props.nextOffset ?? null)

const selected = ref<PodcastEpisode | null>(null)
const showModal = ref(false)

// function startAssessment() {
//   router.visit(route('guest.assessment'))
// }

function open(ep: PodcastEpisode) {
  selected.value = ep
  showModal.value = true
  document.body.style.overflow = 'hidden'
}
function close() {
  showModal.value = false
  selected.value = null
  document.body.style.overflow = ''
}

function loadMore() {
  if (!nextOffset.value) return
  router.get(route('guest.podcast'), { offset: nextOffset.value }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (pg: any) => {
      const props = (pg?.props ?? {}) as Partial<PodcastPageProps>
      const newItems = (props.episodes ?? []) as PodcastEpisode[]
      episodes.value.push(...newItems)
      nextOffset.value = (props.nextOffset ?? null)
    }
  })
}

// Close modal on ESC
function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape' && showModal.value) close()
}
onMounted(() => window.addEventListener('keydown', onKey))
onBeforeUnmount(() => window.removeEventListener('keydown', onKey))
</script>

<template>
  <GuestAppLayout>
    <div class="max-w-7xl mx-auto px-4 py-10 text-gray-900 dark:text-gray-100">
      <h1 class="text-3xl font-bold mb-6">Podcast Episodes</h1>

      <!-- TWO-COLUMN LAYOUT -->
      <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_360px]">
        <!-- LEFT: episode list (unchanged) -->
        <div>
          <ul class="space-y-8">
            <li v-for="ep in episodes" :key="ep.id" @click="open(ep)"
              class="flex gap-4 pb-6 border-b border-gray-200 dark:border-gray-800/40 cursor-pointer rounded-lg p-2 hover:bg-gray-50 dark:hover:bg-white/5">
              <!-- Thumb -->
              <template v-if="ep.thumb">
                <a v-if="ep.video_url" :href="ep.video_url" target="_blank" rel="noopener" @click.stop
                  class="flex-shrink-0">
                  <img :src="ep.thumb" class="w-44 h-28 object-cover rounded-lg" alt="Open on YouTube" />
                </a>
                <img v-else :src="ep.thumb" class="w-44 h-28 object-cover rounded-lg flex-shrink-0" />
              </template>

              <!-- Text -->
              <div class="min-w-0">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                  <span v-if="ep.guest">{{ ep.guest }}</span>
                  <span v-else-if="ep.host">{{ ep.host }}</span>
                  <span v-if="(ep.guest || ep.host) && ep.episode"> · </span>
                  <span v-if="ep.episode">Episode {{ ep.episode }}</span>
                  <span v-if="ep.date"> · {{ ep.date }}</span>
                </div>
                <div class="font-semibold text-lg mt-1 text-gray-900 dark:text-white">
                  {{ ep.title || 'Untitled Episode' }}
                </div>
                <p class="text-sm mt-1 text-gray-700 dark:text-gray-300 line-clamp-2">
                  {{ ep.summary }}
                </p>
              </div>
            </li>
          </ul>

          <!-- Load more STAYS under the list -->
          <div class="mt-8">
            <button v-if="nextOffset" @click="loadMore"
              class="px-4 py-2 rounded-lg bg-orange-500 text-white hover:bg-orange-600">
              Load more
            </button>
            <span v-else class="text-sm text-gray-500 dark:text-gray-400">No more episodes</span>
          </div>
        </div>

        <!-- RIGHT: sticky sidebar -->
        <aside class="hidden lg:block">
          <div class="sticky top-24 space-y-6">

            <!-- Assessment promo (clean card, no big background) -->
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm
                        dark:bg-neutral-900 dark:border-neutral-800">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-3">
                Get your free REI Blueprint NOW
              </h3>

              <div class="rounded-xl p-4 bg-gray-50 dark:bg-gray-800 mb-4">
                <img src="https://revamp365-storage.s3.amazonaws.com/assets/assets/6890d52e49044.jpg"
                  alt="AI Assisted Strategy" class="w-full rounded-lg shadow" />
              </div>

              <button @click="router.visit(route('guest.do_assessment'))" class="w-full h-11 rounded-lg font-semibold
                   bg-gradient-to-r from-purple-600 to-blue-600
                   hover:from-purple-700 hover:to-blue-700 text-white shadow-md">
                Start Your Free Assessment
              </button>

              <p class="mt-2 text-center text-xs text-red-500 italic">
                No credit card required ↑
              </p>
            </section>

            <!-- Optional: second card (Affiliate Partnerships) -->
            <!-- Affiliate Partnerships (white card style) -->
            <section class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm
         dark:bg-neutral-900 dark:border-neutral-800">
              <img src="http://119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1715872910206x572660920575375000/revamp.jpg"
              alt="Affiliate Partnerships"
              class="w-full h-44 object-cover"
              />
              <div class="p-5">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">
                  Sell YOUR Deals
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                  See how listing your wholesale deals here can increase your deal size and speed of
                </p>

                <!-- ghost / soft button -->
                <a href="/learn" class="inline-flex items-center justify-center w-full h-11 rounded-full
             border border-gray-300 text-gray-800 bg-white
             hover:bg-gray-50
             dark:border-neutral-700 dark:text-white dark:bg-neutral-900 dark:hover:bg-neutral-800">
                  List Your Deal
                </a>
              </div>
            </section>
            <!-- Hot Deals Section -->
<section class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm
         dark:bg-neutral-900 dark:border-neutral-800">
  <div class="p-5 border-b border-gray-200 dark:border-neutral-800">
    <h4 class="font-semibold text-gray-900 dark:text-white">
      Hot Deals
    </h4>
  </div>
  
  <div class="p-5 space-y-4">
    <!-- Loading state -->
    <div v-if="loadingHotDeals" class="text-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500 mx-auto"></div>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Loading deals...</p>
    </div>
    
    <!-- Properties -->
    <div v-else-if="hotDeals.length > 0" class="space-y-6">
      <div v-for="deal in hotDeals" :key="deal.id" 
           class="cursor-pointer border border-gray-200 dark:border-neutral-700 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
        
        <!-- Property Image -->
        <div class="relative">
          <img :src="deal.image || '/site-assets/placeholder-property.jpg'" :alt="deal.address" 
               class="w-full h-32 object-cover" />
          
          <!-- Status Badges -->
          <div class="absolute top-2 left-2 flex gap-1">
            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
              {{ deal.status }}
            </span>
            <span v-if="deal.wholesale === 'Yes'" 
                  class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
              WHOLESALE
            </span>
          </div>
        </div>
        
        <!-- Property Details -->
        <div class="p-3">
          <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            {{ deal.bedrooms_count }} bds | {{ deal.bathrooms_total_count }} ba | {{ deal.total_finished_sqft?.toLocaleString() }} sqft
          </div>
          
          <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">
            ${{ deal.list_price?.toLocaleString() }}
          </div>
          
          <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            {{ deal.office_info }}
          </div>
          
          <div class="text-sm text-gray-700 dark:text-gray-300 mb-3">
            {{ deal.address }}
          </div>
          
          <!-- Financial Metrics Table -->
          <div class="text-xs">
            <div class="grid grid-cols-2 gap-2">
              <div class="flex justify-between">
                <span class="text-gray-500">Rent:</span>
                <span class="font-medium">${{ deal.medianrent?.toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">AVM:</span>
                <span class="font-medium">${{ deal.est_avm?.toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">ARV:</span>
                <span class="font-medium">${{ deal.est_arv?.toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Est Cash Flow:</span>
                <span class="font-medium">${{ deal.est_cashflow?.toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Delta PSF:</span>
                <span class="font-medium">${{ deal.delta_psf?.toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Est Flip Profit:</span>
                <span class="font-medium">${{ deal.est_profit?.toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- No deals state -->
    <div v-else class="text-center py-8">
      <p class="text-sm text-gray-500 dark:text-gray-400">No active deals available</p>
    </div>
  </div>
</section>
          </div>
        </aside>
      </div>
    </div>

    <!-- MODAL -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-start md:items-center justify-center">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70" @click="close"></div>

        <!-- Dialog -->
        <div class="relative z-10 w-[92vw] max-w-3xl mx-auto my-8 rounded-xl shadow-2xl border
             bg-white text-gray-900 border-gray-200
             dark:bg-neutral-900 dark:text-gray-100 dark:border-neutral-800" role="dialog" aria-modal="true">
          <!-- Header -->
          <div class="sticky top-0 z-10 p-5 border-b backdrop-blur
               bg-white/95 border-gray-200
               dark:bg-neutral-900/95 dark:border-neutral-800">
            <button class="absolute right-3 top-3 text-gray-500 hover:text-gray-700
                 dark:text-gray-400 dark:hover:text-gray-200" @click="close" aria-label="Close">
              ✕
            </button>

            <div class="text-xl font-semibold">The Revamp Podcast</div>
            <div class="text-sm text-gray-500 dark:text-gray-400" v-if="selected?.episode">
              Episode Number {{ selected?.episode }}
            </div>
            <div class="mt-2 font-medium text-gray-900 dark:text-white" v-if="selected?.title">
              {{ selected?.title }}
            </div>

            <!-- YouTube button -->
            <div class="mt-3">
              <a v-if="selected?.video_url" :href="selected.video_url" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1 rounded
                   bg-red-600 text-white hover:bg-red-700
                   dark:hover:bg-red-500 text-sm" @click.stop title="Watch on YouTube">
                ▶ Watch on YouTube
              </a>
            </div>
          </div>

          <!-- Body -->
          <div class="max-h-[85vh] overflow-y-auto">
            <!-- Thumbnail -->
            <div class="p-5" v-if="selected?.thumb">
              <a v-if="selected?.video_url" :href="selected.video_url" target="_blank" rel="noopener"
                class="block cursor-pointer hover:opacity-95 transition" @click.stop>
                <img :src="selected.thumb" class="w-full rounded-lg" alt="Open on YouTube" />
              </a>
              <img v-else :src="selected.thumb" class="w-full rounded-lg" alt="" />
            </div>

            <!-- Meta + Description -->
            <div class="px-5 pb-5 text-sm space-y-3 text-gray-700 dark:text-gray-200">
              <div v-if="selected?.date">
                <span class="font-medium text-gray-900 dark:text-white">Date Posted:</span>
                {{ selected?.date }}
              </div>

              <div v-if="selected?.host">
                <span class="font-medium text-gray-900 dark:text-white">Host:</span>
                {{ selected?.host }}
              </div>

              <div v-if="selected?.guest">
                <span class="font-medium text-gray-900 dark:text-white">Guest:</span>
                {{ selected?.guest }}
              </div>

              <div>
                <div class="font-medium mb-1 text-gray-900 dark:text-white">
                  Description and Timestamps
                </div>
                <div class="whitespace-pre-wrap leading-relaxed">
                  {{ selected?.summary }}
                </div>
              </div>

              <!-- Actions -->
              <div class="pt-2">
                <a v-if="selected?.video_url" :href="selected.video_url" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                     bg-red-600 text-white hover:bg-red-700 dark:hover:bg-red-500">
                  ▶ Watch on YouTube
                </a>
                <a v-else-if="selected?.audio_url" :href="selected.audio_url" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                     bg-indigo-600 text-white hover:bg-indigo-700 dark:hover:bg-indigo-500">
                  🎧 Listen to audio
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>


  </GuestAppLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
}

/* simple fade for modal */
.fade-enter-active,
.fade-leave-active {
  transition: opacity .15s
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0
}
</style>
