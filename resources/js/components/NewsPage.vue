<template>
  <div>
    <!-- Hero Gambar Berita Utama -->
    <section 
      v-if="hero" 
      class="relative mb-8 rounded-xl overflow-hidden cursor-pointer group"
      @click="openDetail(hero)"
    >
      <img :src="hero.image_url" alt="Berita Utama" class="w-full aspect-[16/9] object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" />
      <div class="absolute inset-0 bg-black/40 group-hover:bg-black/30 transition-colors"></div>
      <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
        <h1 class="text-[32px] font-bold leading-tight mb-2 group-hover:text-cyan-400 transition-colors">
          {{ hero.title }}
          <span v-if="isRead(hero.id)" class="ml-2 text-xs bg-white/20 px-2 py-1 rounded-full text-white/80 font-normal">Dibaca</span>
        </h1>
        <div class="mt-2 text-sm opacity-80">Sumber: {{ hero.user?.name }} • {{ formatDate(hero.created_at) }}</div>
      </div>
    </section>

    <!-- Konten Utama: Trending & Sponsor -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Trending Artikel (kiri) -->
      <div class="lg:col-span-2 space-y-4">
        <h2 class="text-2xl font-semibold">Trending</h2>
        <article 
          v-for="t in trending" 
          :key="t.id" 
          class="card-dark-theme rounded shadow hover:shadow-lg transition-all overflow-hidden cursor-pointer group"
          :class="{ 'opacity-75': isRead(t.id) }"
          @click="openDetail(t)"
        >
          <div class="grid grid-cols-1 sm:grid-cols-3">
            <div class="relative overflow-hidden sm:aspect-[4/3]">
              <img :src="t.image_url" alt="" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" />
            </div>
            <div class="p-4 sm:col-span-2">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                  {{ t.title }}
                </h3>
                <span :class="['text-xs px-2 py-1 rounded', labelClass(t.category)]">{{ t.category }}</span>
              </div>
              <div class="flex items-center gap-2 mb-2">
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(t.created_at) }}</span>
                <span v-if="isRead(t.id)" class="text-[10px] bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500 dark:text-gray-400">Dibaca</span>
              </div>
              <p class="text-sm text-gray-700 dark:text-gray-200 line-clamp-3">{{ t.content }}</p>
            </div>
          </div>
        </article>

        <!-- Rekomendasi Trending -->
        <div class="mt-6">
          <h2 class="text-2xl font-semibold mb-3">Rekomendasi untuk Anda</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article 
              v-for="r in recommendations" 
              :key="r.id" 
              class="card-dark-theme rounded shadow hover:shadow-lg transition-all overflow-hidden cursor-pointer group"
              :class="{ 'opacity-75': isRead(r.id) }"
              @click="openDetail(r)"
            >
              <div class="relative overflow-hidden aspect-[4/3]">
                <img :src="r.image_url" alt="" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" />
              </div>
              <div class="p-3">
                <h4 class="font-semibold text-sm mb-1 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2">
                  {{ r.title }}
                </h4>
                <div class="flex items-center justify-between">
                  <div class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(r.created_at) }}</div>
                  <span v-if="isRead(r.id)" class="text-[10px] text-gray-400">Dibaca</span>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>

      <!-- Sponsor (kanan) -->
      <aside class="space-y-6">
        <div class="card-dark-theme rounded shadow p-4">
          <h4 class="text-lg font-semibold mb-3">Iklan Sponsor</h4>
          <div class="h-40 rounded bg-neutral-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
            Sponsor Area
          </div>
        </div>
        <div class="card-dark-theme rounded shadow p-4">
          <h4 class="text-lg font-semibold mb-2">Newsletter</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Dapatkan berita terbaru langsung ke email Anda.</p>
          <form @submit.prevent="subscribe" class="space-y-2">
            <input v-model="email" type="email" placeholder="email@contoh.com" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 dark:focus:ring-cyan-800 outline-none" />
            <button class="w-full bg-cyan-600 hover:bg-cyan-700 text-white px-3 py-2 rounded transition-colors">Langganan</button>
          </form>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const articles = ref([])
const hero = computed(() => (articles.value.length ? articles.value[0] : null))
const trending = computed(() => articles.value.slice(0, 5))
const recommendations = computed(() => articles.value.slice(5, 11))
const email = ref('')

// Read Status Logic
const readArticles = ref(new Set(JSON.parse(localStorage.getItem('read_articles') || '[]')))

const isRead = (id) => readArticles.value.has(id)

const openDetail = (article) => {
  if (!readArticles.value.has(article.id)) {
    readArticles.value.add(article.id)
    localStorage.setItem('read_articles', JSON.stringify([...readArticles.value]))
  }
  router.push(`/articles/${article.id}`)
}

const fetch = async () => {
  const { data } = await axios.get('/articles')
  articles.value = data.data ?? data
}
onMounted(fetch)

const formatDate = (iso) => new Date(iso).toLocaleString()
const labelClass = (cat) => {
  switch (cat) {
    case 'Cyber Security': return 'bg-red-100 text-red-700'
    case 'Pemrograman': return 'bg-blue-100 text-blue-700'
    case 'Teknologi': return 'bg-cyan-100 text-cyan-700'
    case 'Sistem Digital': return 'bg-purple-100 text-purple-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const subscribe = () => {
  email.value = ''
  alert('Terima kasih telah berlangganan!')
}
</script>
