<template>
  <div class="animate-fade-in">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold tracking-tight gradient-text mb-2">Artikel Saya</h1>
        <p class="text-secondary">Kelola artikel yang Anda tulis.</p>
      </div>
      <router-link to="/articles/new" class="inline-flex items-center gap-2 home-cta-button">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13H13v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Tulis Artikel
      </router-link>
    </div>

    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="n in 3" :key="n" class="card-theme rounded-2xl p-4 h-64 skeleton"></div>
    </div>

    <div v-else-if="articles.length === 0" class="text-center py-12 card-theme rounded-2xl border border-theme">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
      </div>
      <h3 class="text-xl font-bold text-primary mb-2">Belum ada artikel</h3>
      <p class="text-secondary mb-6">Mulai berkontribusi dengan menulis artikel pertama Anda.</p>
      <router-link to="/articles/new" class="btn-gradient px-6 py-2 rounded-xl font-bold text-white">
        Mulai Menulis
      </router-link>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <article
        v-for="(a, index) in articles"
        :key="a.id"
        :style="{ animationDelay: `${index * 0.1}s` }"
        class="card-hover card-theme rounded-2xl shadow-lg overflow-hidden group animate-fade-in flex flex-col"
      >
        <!-- Image -->
        <div class="relative overflow-hidden aspect-video">
           <img 
            :src="a.image_url" 
            :alt="a.title"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
            loading="lazy" 
          />
          <div class="absolute top-4 right-4 flex gap-2">
             <span :class="['px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg', statusClass(a.status)]">
              {{ statusLabel(a.status) }}
            </span>
          </div>
        </div>

        <!-- Content -->
        <div class="p-5 flex-1 flex flex-col">
          <div class="mb-2">
             <span class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider">{{ a.category }}</span>
          </div>
          <h2 class="text-xl font-bold mb-2 line-clamp-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
            {{ a.title }}
          </h2>
          <p class="text-sm text-secondary line-clamp-2 mb-4 flex-1">{{ a.content }}</p>
          
          <div v-if="a.status === 'rejected' && a.rejection_reason" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 text-xs">
            <span class="font-bold text-red-700 dark:text-red-300">Alasan Penolakan:</span>
            <p class="text-red-600 dark:text-red-400 mt-1">{{ a.rejection_reason }}</p>
          </div>

          <div class="flex items-center gap-3 pt-3 border-t border-theme mt-auto">
             <router-link :to="`/articles/${a.id}/edit`" class="flex-1 text-center py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors font-medium text-sm">
               Edit
             </router-link>
             <button @click="confirmDelete(a)" class="flex-1 text-center py-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors font-medium text-sm">
               Hapus
             </button>
          </div>
        </div>
      </article>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex justify-center mt-8 gap-2">
      <button 
        @click="page--" 
        :disabled="page === 1"
        class="px-4 py-2 rounded-lg card-theme border border-theme disabled:opacity-50"
      >
        Prev
      </button>
      <span class="px-4 py-2 text-primary font-medium">Page {{ page }} of {{ totalPages }}</span>
      <button 
        @click="page++" 
        :disabled="page === totalPages"
        class="px-4 py-2 rounded-lg card-theme border border-theme disabled:opacity-50"
      >
        Next
      </button>
    </div>

    <ConfirmModal 
      :show="showDeleteModal"
      title="Hapus Artikel"
      message="Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan."
      @confirm="deleteArticle"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import ConfirmModal from '../components/ConfirmationModal.vue'

const articles = ref([])
const loading = ref(true)
const page = ref(1)
const totalPages = ref(1)

const showDeleteModal = ref(false)
const articleToDelete = ref(null)

const fetchArticles = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/my-articles?page=${page.value}`)
    articles.value = data.data
    totalPages.value = data.last_page
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const statusClass = (status) => {
  switch (status) {
    case 'published': return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
    case 'pending': return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
    case 'rejected': return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
    default: return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
  }
}

const statusLabel = (status) => {
  switch (status) {
    case 'published': return 'Diterbitkan'
    case 'pending': return 'Menunggu Verifikasi'
    case 'rejected': return 'Ditolak'
    default: return 'Draft'
  }
}

const confirmDelete = (article) => {
  articleToDelete.value = article
  showDeleteModal.value = true
}

const deleteArticle = async () => {
  if (!articleToDelete.value) return
  try {
    await axios.delete(`/articles/${articleToDelete.value.id}`)
    showDeleteModal.value = false
    articleToDelete.value = null
    fetchArticles()
  } catch (e) {
    alert('Gagal menghapus artikel')
  }
}

watch(page, fetchArticles)

onMounted(fetchArticles)
</script>
