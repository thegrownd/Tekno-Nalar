<template>
  <div class="max-w-md mx-auto card-theme p-8 rounded-2xl shadow-xl border border-theme animate-fade-in">
    <!-- Header -->
    <div class="text-center mb-6">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
        </svg>
      </div>
      <h1 class="text-3xl font-bold mb-2 text-primary">Admin Login</h1>
      <p class="text-sm text-secondary">Akses penuh untuk administrator sistem</p>
    </div>

    <!-- Form -->
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block mb-2 text-sm font-semibold text-secondary flex items-center gap-2">
          <svg class="w-4 h-4 text-purple-600" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
          </svg>
          Email Admin
        </label>
        <input 
          v-model="email" 
          type="email" 
          placeholder="admin@example.com"
          class="input-theme w-full px-4 py-3 rounded-xl border-2 outline-none transition-all focus:border-purple-500 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800" 
          required 
        />
      </div>
      
      <div>
        <label class="block mb-2 text-sm font-semibold text-secondary flex items-center gap-2">
          <svg class="w-4 h-4 text-purple-600" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
          </svg>
          Password
        </label>
        <input 
          v-model="password" 
          type="password" 
          placeholder="••••••••"
          class="input-theme w-full px-4 py-3 rounded-xl border-2 outline-none transition-all focus:border-purple-500 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800" 
          required 
        />
      </div>

      <!-- Error Message -->
      <div v-if="error" class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 flex items-start gap-3 animate-fade-in">
        <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <div>
          <p class="font-semibold text-red-800 dark:text-red-200">Login Gagal</p>
          <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
        </div>
      </div>

      <!-- Submit Button -->
      <button 
        :disabled="loading || isLocked" 
        class="w-full bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-6 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
      >
        <svg v-if="!loading" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
        </svg>
        <svg v-else class="w-6 h-6 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span v-if="!loading">Masuk sebagai Admin</span>
        <span v-else>Memverifikasi...</span>
      </button>

      <!-- Regular Login Link -->
      <div class="text-center pt-2">
        <span class="text-sm text-secondary">Bukan admin? </span>
        <router-link to="/login" class="text-sm font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">
          Login Reguler
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { authState } from '../authState'

const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const loginAttempts = ref(0)
const isLocked = ref(false)
const lockTime = ref(0)

const submit = async () => {
  if (isLocked.value) return

  error.value = ''
  loading.value = true
  try {
    const { data } = await axios.post('/auth/login', { email: email.value, password: password.value })
    
    const user = data.user
    const isAdmin = !!user.is_admin
    const isSuperAdmin = !!data.is_super_admin
    
    if (typeof authState !== 'undefined' && authState.login) {
      authState.login(data.access_token, user, isAdmin, isSuperAdmin)
    } else {
      localStorage.setItem('token', data.access_token)
      localStorage.setItem('user_id', data.user.id)
      localStorage.setItem('is_admin', String(!!data.user?.is_admin))
      localStorage.setItem('is_super_admin', String(!!data.is_super_admin))
      axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`
    }
    
    // Redirect to admin dashboard
    router.push('/admin/pending-articles')
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Gagal login'
    
    // Client-side rate limiting
    loginAttempts.value++
    if (loginAttempts.value >= 3) {
      isLocked.value = true
      lockTime.value = 30
      error.value = `Terlalu banyak percobaan gagal. Silakan tunggu ${lockTime.value} detik.`
      
      const timer = setInterval(() => {
        lockTime.value--
        if (lockTime.value > 0) {
           error.value = `Terlalu banyak percobaan gagal. Silakan tunggu ${lockTime.value} detik.`
        } else {
          isLocked.value = false
          loginAttempts.value = 0
          error.value = ''
          clearInterval(timer)
        }
      }, 1000)
    }
  } finally {
    loading.value = false
  }
}
</script>
