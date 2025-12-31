<template>
  <div class="max-w-md mx-auto card-theme p-8 rounded-2xl shadow-xl border border-theme animate-fade-in">
    <!-- Header -->
    <div class="text-center mb-6">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
      </div>
      <h1 class="text-3xl font-bold mb-2 text-primary">Selamat Datang</h1>
      <p class="text-sm text-secondary">Masuk untuk mengelola artikel Anda</p>
    </div>

    <!-- Login Form -->
    <form v-if="!needsOtp" @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block mb-2 text-sm font-semibold text-secondary flex items-center gap-2">
          <svg class="w-4 h-4 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
          </svg>
          Email
        </label>
        <input 
          v-model="email" 
          type="email" 
          placeholder="nama@email.com"
          class="input-theme w-full px-4 py-3 rounded-xl border-2 outline-none transition-all focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 dark:focus:ring-cyan-800" 
          required 
        />
      </div>
      
      <div>
        <label class="block mb-2 text-sm font-semibold text-secondary flex items-center gap-2">
          <svg class="w-4 h-4 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
          </svg>
          Password
        </label>
        <input 
          v-model="password" 
          type="password" 
          placeholder="••••••••"
          class="input-theme w-full px-4 py-3 rounded-xl border-2 outline-none transition-all focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 dark:focus:ring-cyan-800" 
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
        :disabled="loading" 
        class="w-full btn-gradient text-white px-6 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
      >
        <svg v-if="!loading" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
        </svg>
        <svg v-else class="w-6 h-6 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span v-if="!loading">Masuk</span>
        <span v-else>Mengautentikasi...</span>
      </button>

      <!-- Divider -->
      <div class="relative my-4">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">Atau masuk dengan</span>
        </div>
      </div>

      <!-- Google Button -->
      <button 
        type="button" 
        @click="loginWithGoogle"
        class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-xl shadow-sm bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition-all text-gray-700 dark:text-gray-200 font-medium"
      >
        <svg class="w-5 h-5" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Google
      </button>

      <!-- Register Link -->
      <div class="text-center pt-2">
        <span class="text-sm text-secondary">Belum punya akun? </span>
        <router-link to="/register" class="text-sm font-semibold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 transition-colors">
          Daftar Sekarang
        </router-link>
      </div>
    </form>

    <!-- OTP Form (Shown if email not verified) -->
    <form v-else @submit.prevent="submitOtp">
      <div class="mb-4 text-center">
        <h2 class="text-xl font-bold mb-2 text-primary">Verifikasi Email</h2>
        <p class="text-sm text-secondary">
          Akun Anda belum aktif. Masukkan kode OTP yang dikirim ke <strong>{{ email }}</strong>
        </p>
      </div>
      <div class="mb-3">
        <label class="block mb-1 text-center">Kode OTP (6 Digit)</label>
        <input v-model="otp" type="text" maxlength="6" class="w-full text-center text-2xl tracking-widest border rounded px-3 py-2 bg-gray-50 text-black dark:bg-gray-700 dark:text-white" required placeholder="000000" />
      </div>
      <div v-if="error" class="text-red-500 mb-2 text-sm text-center">{{ error }}</div>
      <div v-if="success" class="text-green-500 mb-2 text-sm text-center">{{ success }}</div>
      
      <button :disabled="loading" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:opacity-50 mb-3">
        {{ loading ? 'Memverifikasi...' : 'Verifikasi & Login' }}
      </button>
      
      <div class="text-center flex justify-between px-4">
        <button type="button" @click="needsOtp = false" class="text-sm text-gray-500">Kembali</button>
        <button type="button" @click="resendOtp" :disabled="resendCooldown > 0" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
          {{ resendCooldown > 0 ? `Kirim ulang (${resendCooldown}s)` : 'Kirim Ulang' }}
        </button>
      </div>
    </form>

    <!-- Info Tip -->
    <div class="mt-6 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
      <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <div>
          <p class="text-sm font-semibold text-blue-800 dark:text-blue-200">Tips</p>
          <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Gunakan halaman <router-link to="/admin-login" class="underline font-semibold">Admin Login</router-link> untuk akses cepat sebagai administrator.</p>
        </div>
      </div>
    </div>
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
const needsOtp = ref(false)
const otp = ref('')
const success = ref('')
const resendCooldown = ref(0)

const submit = async () => {
  error.value = ''
  loading.value = true
  try {
    const response = await axios.post('/auth/login', { email: email.value, password: password.value })
    
    const { access_token, user, is_super_admin } = response.data;
    const isAdmin = user.is_admin || is_super_admin;
    
    if (typeof authState !== 'undefined' && authState.login) {
        authState.login(access_token, user, isAdmin, is_super_admin);
    } else {
        // Fallback
        localStorage.setItem('token', access_token);
        localStorage.setItem('user', JSON.stringify(user));
        localStorage.setItem('is_admin', isAdmin ? 'true' : 'false');
        localStorage.setItem('is_super_admin', is_super_admin ? 'true' : 'false');
        axios.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;
    }

    router.push('/');
  } catch (e) {
    if (e.response?.status === 403 && e.response?.data?.needs_verification) {
      needsOtp.value = true
      startCooldown()
    } else {
      error.value = e.response?.data?.message ?? 'Gagal login'
    }
  } finally {
    loading.value = false
  }
}

const loginWithGoogle = async () => {
  try {
    const response = await axios.get('/auth/google')
    window.location.href = response.data.url
  } catch (e) {
    error.value = 'Gagal inisialisasi Google Login'
  }
}

const submitOtp = async () => {
  error.value = ''
  success.value = ''
  loading.value = true
  try {
    const response = await axios.post('/auth/verify-otp', {
      email: email.value,
      code: otp.value
    })
    
    const { access_token, user, is_super_admin } = response.data;
    const isAdmin = user.is_admin || is_super_admin;
    
    if (typeof authState !== 'undefined' && authState.login) {
        authState.login(access_token, user, isAdmin, is_super_admin);
    } else {
        localStorage.setItem('token', access_token);
        localStorage.setItem('user', JSON.stringify(user));
        localStorage.setItem('is_admin', isAdmin ? 'true' : 'false');
        localStorage.setItem('is_super_admin', is_super_admin ? 'true' : 'false');
        axios.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;
    }
    
    success.value = 'Verifikasi berhasil! Mengalihkan...'
    setTimeout(() => {
        router.push('/') 
    }, 1500)
    
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Kode OTP salah.'
  } finally {
    loading.value = false
  }
}

const resendOtp = async () => {
  if (resendCooldown.value > 0) return
  
  loading.value = true
  error.value = ''
  success.value = ''
  
  try {
    await axios.post('/auth/resend-otp', { email: email.value })
    success.value = 'Kode OTP baru telah dikirim.'
    startCooldown()
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Gagal mengirim ulang OTP.'
  } finally {
    loading.value = false
  }
}

const startCooldown = () => {
  resendCooldown.value = 60
  const timer = setInterval(() => {
    resendCooldown.value--
    if (resendCooldown.value <= 0) clearInterval(timer)
  }, 1000)
}
</script>
