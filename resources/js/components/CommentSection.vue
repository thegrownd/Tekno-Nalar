<template>
  <div class="mt-12 border-t pt-8 border-theme transition-colors duration-300">
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
      <h3 class="text-2xl font-bold text-primary flex items-center gap-2">
        Komentar
        <span class="text-sm font-normal text-muted">({{ totalComments }})</span>
      </h3>
      
      <!-- Font Size Controls -->
      <div class="flex items-center bg-elevated rounded-lg p-1 border border-theme">
        <button 
          @click="fontSize = 'small'"
          :class="['p-2 rounded transition-colors', fontSize === 'small' ? 'bg-card shadow text-link' : 'text-muted hover:text-primary']"
          title="Ukuran Font Kecil"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19h6M9 19v-9M6 19v-9M12 5l3 7h-6l3-7z" />
          </svg>
        </button>
        <button 
          @click="fontSize = 'medium'"
          :class="['p-2 rounded transition-colors', fontSize === 'medium' ? 'bg-card shadow text-link' : 'text-muted hover:text-primary']"
          title="Ukuran Font Sedang"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19h9M9 19v-11M6 19v-11M14 5l4 9h-8l4-9z" />
          </svg>
        </button>
        <button 
          @click="fontSize = 'large'"
          :class="['p-2 rounded transition-colors', fontSize === 'large' ? 'bg-card shadow text-link' : 'text-muted hover:text-primary']"
          title="Ukuran Font Besar"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19h12M9 19v-13M6 19v-13M16 5l5 11h-10l5-11z" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Comment Form -->
    <div v-if="isAuthenticated" class="mb-8">
      <div class="flex gap-4">
        <img 
          :src="currentUser.profile_picture_url || 'https://ui-avatars.com/api/?name=' + currentUser.name" 
          alt="Profile" 
          class="w-10 h-10 rounded-full object-cover border border-theme"
        >
        <div class="flex-1">
          <textarea
            v-model="newComment"
            rows="3"
            class="w-full rounded-lg input-theme p-3 text-primary placeholder:text-muted focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all duration-300"
            placeholder="Tulis komentar Anda..."
          ></textarea>
          <div class="flex justify-end mt-2">
            <button 
              @click="submitComment" 
              :disabled="isSubmitting || !newComment.trim()"
              class="btn-gradient px-6 py-2 rounded-lg font-medium shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all"
            >
              {{ isSubmitting ? 'Mengirim...' : 'Kirim Komentar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="mb-8 p-6 bg-elevated rounded-lg text-center border border-theme transition-colors duration-300">
      <p class="text-muted">
        Silakan <router-link to="/login" class="text-link hover:underline font-semibold">Login</router-link> untuk ikut berdiskusi.
      </p>
    </div>

    <!-- Comments List -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-500"></div>
    </div>
    
    <div v-else-if="comments.length === 0" class="text-center py-8 text-muted">
      Belum ada komentar. Jadilah yang pertama berkomentar!
    </div>

    <div v-else class="space-y-6">
      <div v-for="comment in comments" :key="comment.id" class="animate-fade-in group">
        <div class="flex gap-4">
          <img 
            :src="comment.user.profile_picture_url || 'https://ui-avatars.com/api/?name=' + comment.user.name" 
            :alt="comment.user.name" 
            class="w-10 h-10 rounded-full object-cover flex-shrink-0 border border-theme"
          >
          <div class="flex-1">
            <div class="bg-elevated p-4 rounded-xl border border-theme transition-all duration-300 hover:border-cyan-500/30 hover:shadow-sm">
              <div class="flex justify-between items-start mb-2">
                <div>
                  <h4 class="font-semibold text-primary">{{ comment.user.name }}</h4>
                  <span class="text-xs text-muted">{{ formatDate(comment.created_at) }}</span>
                </div>
                
                <!-- Actions -->
                <div v-if="isAuthenticated" class="relative opacity-0 group-hover:opacity-100 transition-opacity">
                  <button 
                    v-if="canModify(comment)" 
                    @click="confirmDelete(comment)"
                    class="text-red-500 hover:text-red-600 text-sm ml-2 transition-colors"
                    title="Hapus Komentar"
                  >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                  </button>
                </div>
              </div>
              
              <p :class="['text-secondary whitespace-pre-wrap transition-all duration-300', contentSizeClass]">{{ comment.content }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="mt-2 flex gap-4 text-sm">
              <button 
                v-if="isAuthenticated" 
                @click="toggleReply(comment.id)" 
                class="text-muted hover:text-link font-medium transition-colors flex items-center gap-1"
              >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10 9V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"/></svg>
                Balas
              </button>
            </div>

            <!-- Reply Form -->
            <div v-if="activeReplyId === comment.id" class="mt-4 ml-4 flex gap-3 animate-fade-in">
              <img 
                :src="currentUser.profile_picture_url || 'https://ui-avatars.com/api/?name=' + currentUser.name" 
                class="w-8 h-8 rounded-full border border-theme"
              >
              <div class="flex-1">
                 <textarea
                    v-model="replyContent"
                    rows="2"
                    class="w-full rounded-lg input-theme p-3 text-sm text-primary placeholder:text-muted dark:placeholder:text-white focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-colors duration-300"
                    placeholder="Tulis balasan..."
                  ></textarea>
                  <div class="flex justify-end gap-2 mt-2">
                    <button 
                      @click="activeReplyId = null"
                      class="px-3 py-1.5 text-muted hover:bg-elevated rounded-lg transition-colors text-sm font-medium"
                    >
                      Batal
                    </button>
                    <button 
                      @click="submitReply(comment.id)"
                      :disabled="isSubmitting || !replyContent.trim()"
                      class="px-4 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-sm disabled:opacity-50 transition-colors text-sm font-medium"
                    >
                      Kirim Balasan
                    </button>
                  </div>
              </div>
            </div>

            <!-- Nested Replies -->
            <div v-if="comment.replies && comment.replies.length > 0" class="mt-4 ml-4 space-y-4 border-l-2 border-theme pl-4 transition-colors duration-300">
              <div v-for="reply in comment.replies" :key="reply.id" class="flex gap-3 group/reply">
                 <img 
                    :src="reply.user.profile_picture_url || 'https://ui-avatars.com/api/?name=' + reply.user.name" 
                    class="w-8 h-8 rounded-full object-cover flex-shrink-0 border border-theme"
                  >
                  <div class="flex-1">
                    <div class="bg-elevated p-3 rounded-xl border border-theme transition-colors duration-300 hover:border-cyan-500/30">
                      <div class="flex justify-between items-start mb-1">
                         <h4 class="font-semibold text-sm text-primary">{{ reply.user.name }}</h4>
                         <div v-if="canModify(reply)" class="opacity-0 group-hover/reply:opacity-100 transition-opacity">
                            <button @click="confirmDelete(reply)" class="text-red-500 hover:text-red-600 text-xs transition-colors">
                              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                            </button>
                         </div>
                      </div>
                      <p :class="['text-secondary transition-all duration-300', contentSizeClass]">{{ reply.content }}</p>
                    </div>
                    <span class="text-xs text-muted mt-1 block ml-1">{{ formatDate(reply.created_at) }}</span>
                  </div>
              </div>
            </div>

          </div>
        </div>
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="flex justify-center gap-2 mt-8">
        <button 
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-1.5 rounded-lg border border-theme bg-card text-primary disabled:opacity-50 hover:bg-elevated transition-colors"
        >
          Prev
        </button>
        <span class="px-3 py-1.5 text-muted">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
        <button 
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-3 py-1.5 rounded-lg border border-theme bg-card text-primary disabled:opacity-50 hover:bg-elevated transition-colors"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  articleId: {
    type: [Number, String],
    required: true
  }
})

const comments = ref([])
const loading = ref(true)
const pagination = ref({})
const newComment = ref('')
const replyContent = ref('')
const activeReplyId = ref(null)
const isSubmitting = ref(false)
const totalComments = ref(0)
const fontSize = ref('medium')

const currentUser = ref(null)
const isAuthenticated = computed(() => !!currentUser.value)

const contentSizeClass = computed(() => {
  switch (fontSize.value) {
    case 'small': return 'text-sm'
    case 'large': return 'text-lg'
    default: return 'text-base'
  }
})

onMounted(() => {
  checkAuth()
  fetchComments()
})

const checkAuth = () => {
  const token = localStorage.getItem('token')
  const user = localStorage.getItem('user')
  if (token && user) {
    currentUser.value = JSON.parse(user)
  }
}

const fetchComments = async (page = 1) => {
  loading.value = true
  try {
    const response = await axios.get(`/articles/${props.articleId}/comments?page=${page}`)
    comments.value = response.data.data
    pagination.value = response.data
    totalComments.value = response.data.total
  } catch (error) {
    console.error('Failed to fetch comments', error)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchComments(page)
  }
}

const submitComment = async () => {
  if (!newComment.value.trim()) return
  
  isSubmitting.value = true
  try {
    const response = await axios.post(`/articles/${props.articleId}/comments`, {
      content: newComment.value
    })
    
    // Add new comment to top
    comments.value.unshift(response.data)
    newComment.value = ''
    totalComments.value++
  } catch (error) {
    if (error.response?.status === 429) {
      alert('Mohon tunggu sebentar sebelum mengirim komentar lagi.')
    } else {
      alert('Gagal mengirim komentar.')
    }
  } finally {
    isSubmitting.value = false
  }
}

const toggleReply = (commentId) => {
  if (activeReplyId.value === commentId) {
    activeReplyId.value = null
  } else {
    activeReplyId.value = commentId
    replyContent.value = ''
  }
}

const submitReply = async (parentId) => {
  if (!replyContent.value.trim()) return
  
  isSubmitting.value = true
  try {
    const response = await axios.post(`/articles/${props.articleId}/comments`, {
      content: replyContent.value,
      parent_id: parentId
    })
    
    // Find parent comment and append reply
    const parentComment = comments.value.find(c => c.id === parentId)
    if (parentComment) {
      if (!parentComment.replies) parentComment.replies = []
      parentComment.replies.push(response.data)
    }
    
    activeReplyId.value = null
    replyContent.value = ''
  } catch (error) {
     alert('Gagal mengirim balasan.')
  } finally {
    isSubmitting.value = false
  }
}

const confirmDelete = async (comment) => {
  if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
    try {
      await axios.delete(`/comments/${comment.id}`)
      
      // Remove from list
      if (comment.parent_id) {
         // It's a reply
         const parent = comments.value.find(c => c.id === comment.parent_id)
         if (parent && parent.replies) {
           parent.replies = parent.replies.filter(r => r.id !== comment.id)
         }
      } else {
         // It's a main comment
         comments.value = comments.value.filter(c => c.id !== comment.id)
         totalComments.value--
       }
    } catch (error) {
      alert('Gagal menghapus komentar.')
    }
  }
}

const canModify = (comment) => {
  if (!currentUser.value) return false
  return currentUser.value.id === comment.user_id || currentUser.value.is_admin
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  }).format(date)
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
