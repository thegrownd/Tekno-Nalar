<template>
  <div class="animate-fade-in">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center">
        <div class="inline-block w-16 h-16 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-muted">Memuat artikel...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center max-w-md">
        <svg class="w-24 h-24 mx-auto mb-4 text-red-500" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <h2 class="text-2xl font-bold mb-2 text-primary">Artikel Tidak Ditemukan</h2>
        <p class="text-muted mb-6">{{ error }}</p>
        <router-link to="/" class="inline-flex items-center gap-2 btn-gradient text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
          </svg>
          Kembali ke Beranda
        </router-link>
      </div>
    </div>

    <!-- Article Content -->
    <article v-else-if="article" class="max-w-4xl mx-auto">
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center gap-2 text-sm mb-6 text-muted" aria-label="Breadcrumb">
        <router-link to="/" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors">
          Beranda
        </router-link>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
        </svg>
        <span class="text-primary font-medium">{{ article.title }}</span>
      </nav>

      <!-- Back Button -->
      <button 
        @click="goBack" 
        class="inline-flex items-center gap-2 mb-6 px-4 py-2 rounded-lg bg-card border border-theme hover:border-cyan-500 transition-all shadow-md hover:shadow-lg group"
        aria-label="Kembali ke halaman sebelumnya"
      >
        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" viewBox="0 0 24 24" fill="currentColor">
          <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
        </svg>
        Kembali
      </button>

      <!-- Article Header -->
      <header class="mb-8">
        <!-- Category Badge & Actions -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
          <div class="flex items-center gap-3">
            <span :class="['px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider', labelClass(article.category)]">
              {{ article.category }}
            </span>
            <div class="flex items-center gap-2 text-sm text-muted">
              <time :datetime="article.created_at">
                {{ formatDate(article.created_at) }}
              </time>
              <span>•</span>
              <div class="flex items-center gap-1">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                </svg>
                {{ article.views_count || 0 }} views
              </div>
            </div>
          </div>
          
          <!-- Admin/Owner Actions -->
          <div v-if="canDelete" class="flex items-center gap-2">
            <button 
              @click="showDeleteModal = true" 
              class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
              title="Hapus Artikel"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
            <router-link 
              :to="`/articles/${article.id}/edit`" 
              class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
              title="Edit Artikel"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
            </router-link>
          </div>
        </div>

        <!-- Title -->
        <h1 class="text-4xl sm:text-5xl font-bold leading-tight mb-6 gradient-text">
          {{ article.title }}
        </h1>

        <!-- Author Info -->
        <div class="flex items-center gap-4 pb-6 border-b border-theme">
          <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-400 to-blue-600 rounded-full opacity-75 group-hover:opacity-100 blur transition duration-200"></div>
            <div class="relative w-14 h-14 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-inner ring-2 ring-white dark:ring-gray-800">
              {{ article.user?.name?.charAt(0).toUpperCase() }}
            </div>
          </div>
          <div>
            <div class="font-semibold text-primary">{{ article.user?.name }}</div>
            <div class="text-sm text-muted">Penulis</div>
          </div>
        </div>
      </header>

      <!-- Featured Image -->
      <div v-if="article.image_url" class="mb-8 rounded-2xl overflow-hidden shadow-2xl">
        <img 
          :src="article.image_url" 
          :alt="article.title"
          class="w-full aspect-[16/9] object-cover"
          @error="handleImageError"
        />
      </div>

      <!-- Article Content -->
      <div class="prose prose-lg dark:prose-invert max-w-none mb-12">
        <div class="text-secondary leading-relaxed whitespace-pre-wrap">
          {{ article.content }}
        </div>
      </div>

      <!-- Article Footer -->
      <footer class="border-t border-theme pt-8 mb-12">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <!-- Share Buttons -->
          <div>
            <h3 class="text-sm font-semibold text-muted mb-3">Bagikan Artikel:</h3>
            <div class="flex gap-2">
              <button 
                @click="shareOnFacebook"
                class="p-3 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors"
                aria-label="Bagikan ke Facebook"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </button>
              <button 
                @click="shareOnTwitter"
                class="p-3 rounded-lg bg-sky-500 hover:bg-sky-600 text-white transition-colors"
                aria-label="Bagikan ke Twitter"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
              </button>
              <button 
                @click="shareOnWhatsApp"
                class="p-3 rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors"
                aria-label="Bagikan ke WhatsApp"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
              </button>
              <button 
                @click="copyLink"
                class="p-3 rounded-lg bg-gray-500 hover:bg-gray-600 text-white transition-colors"
                aria-label="Salin link"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Tags -->
          <div>
            <h3 class="text-sm font-semibold text-muted mb-3">Tags:</h3>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300">
                {{ article.category }}
              </span>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-hover text-secondary">
                Artikel
              </span>
            </div>
          </div>
        </div>
      </footer>

      <!-- Navigation Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 mb-12">
        <router-link 
          to="/" 
          class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white rounded-xl font-semibold shadow-lg transition-all"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
          </svg>
          Kembali ke Beranda
        </router-link>
        <button 
          @click="goBack"
          class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 bg-card border border-theme hover:border-cyan-500 text-primary rounded-xl font-semibold shadow-lg transition-all"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
          </svg>
          Halaman Sebelumnya
        </button>
      </div>

      <!-- Comment Section -->
      <CommentSection :article-id="article.id" />

      <!-- Related Articles Section -->
      <section v-if="relatedArticles.length > 0" class="border-t border-theme pt-12">
        <h2 class="text-3xl font-bold gradient-text mb-6">Artikel Terkait</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <router-link
            v-for="related in relatedArticles"
            :key="related.id"
            :to="`/articles/${related.id}`"
            class="group card-theme rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all"
          >
            <div class="relative overflow-hidden aspect-[16/10]">
              <img 
                :src="related.image_url || 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=600'" 
                :alt="related.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              />
              <span :class="['absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg', labelClass(related.category)]">
                {{ related.category }}
              </span>
            </div>
            <div class="p-4">
              <h3 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                {{ related.title }}
              </h3>
              <p class="text-sm text-muted line-clamp-2">
                {{ related.content }}
              </p>
            </div>
          </router-link>
        </div>
      </section>
    </article>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSEO } from '../composables/useSEO'
import CommentSection from './CommentSection.vue'

const route = useRoute()
const router = useRouter()

const article = ref(null)
const loading = ref(true)
const error = ref(null)
const allArticles = ref([])

// Computed SEO properties
const pageTitle = computed(() => article.value?.title || 'Memuat...')
const pageDescription = computed(() => {
  if (!article.value?.content) return ''
  return article.value.content.substring(0, 160).replace(/\n/g, ' ') + '...'
})
const pageImage = computed(() => article.value?.image_url || '/images/logo.png')

// Schema.org Structured Data
const articleSchema = computed(() => {
  if (!article.value) return null
  return {
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": article.value.title,
    "image": [article.value.image_url || '/images/logo.png'],
    "datePublished": new Date(article.value.created_at).toISOString(),
    "dateModified": new Date(article.value.updated_at).toISOString(),
    "author": [{
      "@type": "Person",
      "name": article.value.user?.name || "TeknoNalar Author"
    }]
  }
})

// Initialize SEO
useSEO(pageTitle, pageDescription, pageImage, 'article', articleSchema)

// Fetch article detail
const fetchArticle = async () => {
  try {
    loading.value = true
    error.value = null
    
    const id = route.params.id
    const { data } = await axios.get(`/articles/${id}`)
    article.value = data.data || data
    
    // Fetch all articles for related section
    const { data: articlesData } = await axios.get('/articles')
    allArticles.value = articlesData.data || articlesData
    
  } catch (err) {
    console.error('Error fetching article:', err)
    error.value = err.response?.status === 404 
      ? 'Artikel yang Anda cari tidak ditemukan.'
      : 'Terjadi kesalahan saat memuat artikel. Silakan coba lagi.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchArticle()
  window.scrollTo({ top: 0, behavior: 'smooth' })
})

// Related articles (same category, exclude current)
const relatedArticles = computed(() => {
  if (!article.value) return []
  return allArticles.value
    .filter(a => a.id !== article.value.id && a.category === article.value.category)
    .slice(0, 3)
})

const labelClass = (cat) => {
  switch (cat) {
    case 'Cyber Security': return 'bg-red-500 text-white'
    case 'Pemrograman': return 'bg-blue-500 text-white'
    case 'Teknologi': return 'bg-cyan-500 text-white'
    case 'Sistem Digital': return 'bg-purple-500 text-white'
    default: return 'bg-gray-500 text-white'
  }
}

const formatDate = (iso) => {
  const date = new Date(iso)
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)
  
  if (diff < 60) return 'Baru saja'
  if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`
  if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`
  if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`
  
  return date.toLocaleDateString('id-ID', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  })
}

const handleImageError = (e) => {
  e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="1200" height="675"%3E%3Cdefs%3E%3ClinearGradient id="grad" x1="0%25" y1="0%25" x2="100%25" y2="100%25"%3E%3Cstop offset="0%25" style="stop-color:%2306B6D4;stop-opacity:1" /%3E%3Cstop offset="100%25" style="stop-color:%233B82F6;stop-opacity:1" /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width="1200" height="675" fill="url(%23grad)" /%3E%3Ctext x="50%25" y="50%25" font-family="Arial" font-size="48" fill="white" text-anchor="middle" dominant-baseline="middle"%3ETeknoNalar%3C/text%3E%3C/svg%3E'
}

const goBack = () => {
  if (window.history.length > 1) {
    router.go(-1)
  } else {
    router.push('/')
  }
}

// Share functions
const shareOnFacebook = () => {
  const url = encodeURIComponent(window.location.href)
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400')
}

const shareOnTwitter = () => {
  const url = encodeURIComponent(window.location.href)
  const text = encodeURIComponent(article.value?.title || '')
  window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400')
}

const shareOnWhatsApp = () => {
  const url = encodeURIComponent(window.location.href)
  const text = encodeURIComponent(article.value?.title || '')
  window.open(`https://wa.me/?text=${text}%20${url}`, '_blank')
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    alert('Link berhasil disalin ke clipboard!')
  } catch (err) {
    console.error('Failed to copy:', err)
    alert('Gagal menyalin link. Silakan salin secara manual.')
  }
}
</script>
