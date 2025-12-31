<template>
  <div class="animate-fade-in max-w-7xl mx-auto">
      <div class="flex flex-col sm:flex-row gap-4 justify-between items-center mb-6">
        <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
          Verifikasi Artikel
        </h2>
        <div class="flex items-center gap-2">
          <select 
            v-model="filterStatus" 
            @change="page=1; fetchArticles()" 
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 outline-none transition-colors"
          >
            <option value="pending">Menunggu Verifikasi</option>
            <option value="published">Diterbitkan</option>
            <option value="rejected">Ditolak</option>
          </select>
          <div v-if="loading" class="flex items-center gap-2 text-sm text-green-500 font-medium animate-pulse">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            Live Update
          </div>
        </div>
      </div>
  
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="overflow-x-auto">
          <table class="w-full text-left" v-if="articles.length">
            <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
              <tr>
                <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Artikel</th>
                <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Penulis</th>
                <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Kategori</th>
                <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Status</th>
                <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Tanggal</th>
                <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="article in articles" :key="article.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-6 py-4 max-w-xs">
                  <div class="font-medium text-gray-900 dark:text-white line-clamp-2" :title="article.title">{{ article.title }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                  {{ article.user?.name || 'Unknown' }}
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 text-xs rounded-full font-medium">
                    {{ article.category }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="['px-2 py-1 text-xs rounded-full font-medium border border-transparent', statusClass(article.status)]">
                    {{ statusLabel(article.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                  {{ formatDate(article.created_at) }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <button 
                      @click="openPreview(article)" 
                      class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                      title="Lihat Detail & Verifikasi"
                    >
                      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <button 
                      v-if="filterStatus === 'pending'"
                      @click="processArticle('published', article)" 
                      class="p-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors"
                      title="Setujui Langsung"
                    >
                      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </button>
                    <button 
                      v-if="filterStatus === 'pending'"
                      @click="processArticle('rejected', article)" 
                      class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors"
                      title="Tolak Langsung"
                    >
                      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <button
                        @click="openEdit(article)"
                        class="p-2 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-900/40 transition-colors"
                        title="Edit Artikel"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div v-else class="p-12 text-center text-gray-500 dark:text-gray-400">
             <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                 <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
             </div>
             <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada artikel ditemukan</h3>
             <p class="mt-1">Coba ubah filter status.</p>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="totalPages > 1" class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-2">
          <button @click="page--" :disabled="page === 1" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 disabled:opacity-50 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Prev</button>
          <span class="px-4 py-2 text-gray-700 dark:text-gray-300">Page {{ page }} of {{ totalPages }}</span>
          <button @click="page++" :disabled="page === totalPages" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 disabled:opacity-50 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Next</button>
        </div>
      </div>

    <!-- Preview & Review Modal -->
    <div v-if="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 animate-fade-in overflow-y-auto">
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl my-8 overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-900 z-10">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ selectedArticle.title }}</h2>
            <div class="flex items-center gap-3 mt-2">
              <span class="text-sm text-gray-500 dark:text-gray-400">Oleh {{ selectedArticle.user?.name }}</span>
              <span class="text-gray-300 dark:text-gray-600">•</span>
              <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(selectedArticle.created_at) }}</span>
            </div>
          </div>
          <button @click="showPreviewModal = false" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-8 overflow-y-auto custom-scrollbar">
          <img :src="selectedArticle.image_url" class="w-full h-64 object-cover rounded-xl mb-8 shadow-md" alt="Cover">
          <div class="prose dark:prose-invert max-w-none text-gray-900 dark:text-gray-300" v-html="selectedArticle.content"></div>
          
           <!-- Logs Section -->
           <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
               <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Riwayat Aktivitas</h3>
               <div v-if="loadingLogs" class="text-center py-4 text-gray-500 dark:text-gray-400">Memuat log...</div>
               <div v-else-if="articleLogs.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">Belum ada aktivitas.</div>
               <ul v-else class="space-y-4">
                   <li v-for="log in articleLogs" :key="log.id" class="text-sm border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                       <div class="flex justify-between">
                           <span class="font-medium text-gray-900 dark:text-white">{{ log.description }}</span>
                           <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(log.created_at) }}</span>
                       </div>
                       <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Oleh: {{ log.user ? log.user.name : 'System' }}</div>
                   </li>
               </ul>
           </div>
        </div>

        <!-- Modal Footer (Actions) -->
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
          <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Verifikasi Artikel</h3>
          <textarea 
            v-model="feedback" 
            placeholder="Berikan alasan penolakan atau catatan persetujuan..." 
            class="w-full h-24 p-3 rounded-xl border-2 resize-none mb-4 focus:ring-2 focus:ring-purple-500/20 transition-all bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 focus:border-purple-500 dark:focus:border-purple-400 outline-none"
          ></textarea>
          
          <div class="flex justify-end gap-3">
            <button 
              @click="processArticle('rejected')" 
              class="px-6 py-2.5 rounded-xl font-bold text-white bg-gradient-to-r from-red-500 to-pink-600 hover:opacity-90 transition-opacity shadow-lg shadow-red-500/30"
            >
              Tolak Artikel
            </button>
            <button 
              @click="processArticle('published')" 
              class="px-6 py-2.5 rounded-xl font-bold text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:opacity-90 transition-opacity shadow-lg shadow-green-500/30"
            >
              Terbitkan Artikel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const articles = ref([])
const loading = ref(true)
const page = ref(1)
const totalPages = ref(1)
let pollingInterval = null
const filterStatus = ref('pending')

const showPreviewModal = ref(false)
const selectedArticle = ref(null)
const feedback = ref('')
const articleLogs = ref([])
const loadingLogs = ref(false)

const fetchArticles = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/admin/pending-articles?page=${page.value}&status=${filterStatus.value}`)
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

const fetchLogs = async (articleId) => {
    loadingLogs.value = true
    try {
        const { data } = await axios.get(`/articles/${articleId}/logs`)
        articleLogs.value = data
    } catch (e) {
        console.error("Failed to fetch logs", e)
    } finally {
        loadingLogs.value = false
    }
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}

const openPreview = (article) => {
  selectedArticle.value = article
  feedback.value = ''
  showPreviewModal.value = true
  fetchLogs(article.id)
}

const openEdit = (article) => {
  router.push(`/articles/${article.id}/edit`)
}

const processArticle = async (status, targetArticle = null) => {
  const article = targetArticle || selectedArticle.value
  if (!article) return
  
  // Validation for rejection (only if using modal with feedback)
  if (status === 'rejected' && !targetArticle && !feedback.value.trim()) {
      alert('Mohon berikan alasan penolakan pada kolom feedback.')
      return
  }

  const actionText = status === 'published' ? 'menerbitkan' : 'menolak'
  if (!confirm(`Apakah Anda yakin ingin ${actionText} artikel "${article.title}"?`)) return
  
  try {
    if (status === 'published') {
        await axios.post(`/admin/articles/${article.id}/approve`, {
            feedback: feedback.value
        })
    } else if (status === 'rejected') {
        await axios.post(`/admin/articles/${article.id}/reject`, {
            reason: feedback.value
        })
    } else {
        // Fallback for other status updates if needed (e.g. draft)
        await axios.put(`/articles/${article.id}`, {
            status: status,
            feedback: feedback.value
        })
    }
    
    showPreviewModal.value = false
    selectedArticle.value = null
    fetchArticles()
    
    // Show success notification (could be a toast)
    alert(`Artikel berhasil ${status === 'published' ? 'diterbitkan' : 'ditolak'}`)
  } catch (e) {
    alert(`Gagal ${actionText} artikel: ` + (e.response?.data?.message || 'Error'))
  }
}

watch(page, fetchArticles)

onMounted(() => {
  fetchArticles()
  
  // Polling for real-time updates (every 15 seconds)
  pollingInterval = setInterval(() => {
     axios.get(`/admin/pending-articles?page=${page.value}&status=${filterStatus.value}`)
      .then(({ data }) => {
        // Only update if data changed (simple check usually enough, or just replace)
        // For simplicity, just replacing. Vue handles DOM diffing.
        articles.value = data.data
        totalPages.value = data.last_page
      })
      .catch(console.error)
  }, 15000)
})

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval)
})
</script>
