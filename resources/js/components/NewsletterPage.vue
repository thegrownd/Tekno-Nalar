<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 py-12">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 p-10 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 transition-all hover:shadow-2xl">
      <div class="text-center">
        <div class="mx-auto h-16 w-16 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform -rotate-6 hover:rotate-0 transition-transform duration-300">
           <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
           </svg>
        </div>
        <h2 class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
          Newsletter UAP
        </h2>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 max-w-sm mx-auto">
          Bergabunglah dengan komunitas kami untuk mendapatkan tips teknologi, tutorial coding, dan update keamanan terbaru.
        </p>
      </div>
      
      <div v-if="authState.isAuth" class="mt-8 space-y-6">
        <div class="text-center p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-xl border border-cyan-100 dark:border-cyan-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">Masuk sebagai:</p>
            <p class="font-semibold text-cyan-700 dark:text-cyan-300">{{ authState.user?.email }}</p>
        </div>

        <div v-if="error" class="text-red-500 text-sm text-center bg-red-50 dark:bg-red-900/20 py-2 rounded-lg">
          {{ error }}
        </div>

        <div>
          <button 
            @click="subscribe"
            :disabled="loading"
            class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 disabled:opacity-70 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transition-all active:scale-[0.98]"
          >
            <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ loading ? 'Sedang Mendaftarkan...' : 'Langganan Sekarang' }}
          </button>
        </div>
      </div>
      
      <div v-else class="mt-8 text-center">
        <p class="text-gray-600 dark:text-gray-400 mb-4">Silakan login terlebih dahulu untuk berlangganan newsletter.</p>
        <router-link to="/login" class="inline-block w-full py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 shadow-lg hover:shadow-xl transition-all">
            Login Sekarang
        </router-link>
      </div>
    </div>

    <NewsletterSuccessModal 
      :show="showSuccessModal" 
      :name="displayName" 
      @close="showSuccessModal = false" 
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import NewsletterSuccessModal from './NewsletterSuccessModal.vue'
import { authState } from '../authState'

const loading = ref(false)
const error = ref('')
const showSuccessModal = ref(false)
const subscriberName = ref('')
const router = useRouter()

const displayName = computed(() => subscriberName.value || authState.user?.name || 'Subscriber')

onMounted(() => {
    authState.checkAuth()
})

const subscribe = async () => {
  error.value = ''
  loading.value = true
  
  try {
    // Backend will use Auth::user()
    await axios.post('/subscribe')
    
    subscriberName.value = authState.user?.name || authState.user?.email.split('@')[0]
    
    showSuccessModal.value = true
  } catch (e) {
    if (e.response && e.response.status === 401) {
        error.value = 'Sesi Anda telah berakhir. Silakan login kembali.'
        // Optionally redirect
    } else {
        error.value = e.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}
</script>
