<template>
  <div class="max-w-md mx-auto card-dark-theme p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">{{ step === 'register' ? 'Daftar Akun' : 'Verifikasi Email' }}</h1>
    
    <!-- Register Form -->
    <form v-if="step === 'register'" @submit.prevent="submitRegister">
      <div class="mb-3">
        <label class="block mb-1">Nama</label>
        <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2 bg-gray-50 text-black dark:bg-gray-700 dark:text-white" required />
      </div>
      <div class="mb-3">
        <label class="block mb-1">Email</label>
        <input v-model="form.email" type="email" class="w-full border rounded px-3 py-2 bg-gray-50 text-black dark:bg-gray-700 dark:text-white" required />
      </div>
      <div class="mb-3">
        <label class="block mb-1">Password</label>
        <input v-model="form.password" type="password" class="w-full border rounded px-3 py-2 bg-gray-50 text-black dark:bg-gray-700 dark:text-white" required minlength="8" />
        <p class="text-xs text-gray-500 mt-1">Min 8 karakter, huruf besar, kecil, angka, simbol.</p>
      </div>
      <div class="mb-3">
        <label class="block mb-1">Konfirmasi Password</label>
        <input v-model="form.password_confirmation" type="password" class="w-full border rounded px-3 py-2 bg-gray-50 text-black dark:bg-gray-700 dark:text-white" required />
      </div>
      <div v-if="error" class="text-red-500 mb-2 text-sm">{{ error }}</div>
      <button :disabled="loading" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded disabled:opacity-50">
        {{ loading ? 'Memproses...' : 'Daftar' }}
      </button>
      <div class="mt-4 text-center">
        <span class="text-gray-600 dark:text-gray-400">Sudah punya akun?</span>
        <router-link to="/login" class="ml-2 text-blue-500 hover:underline">Login</router-link>
      </div>
    </form>

    <!-- OTP Form -->
    <form v-else @submit.prevent="submitOtp">
      <div class="mb-4 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-300">
          Kode verifikasi telah dikirim ke <strong>{{ form.email }}</strong>
        </p>
      </div>
      <div class="mb-3">
        <label class="block mb-1 text-center">Kode OTP (6 Digit)</label>
        <input v-model="otp" type="text" maxlength="6" class="w-full text-center text-2xl tracking-widest border rounded px-3 py-2 bg-gray-50 text-black dark:bg-gray-700 dark:text-white" required placeholder="000000" />
      </div>
      <div v-if="error" class="text-red-500 mb-2 text-sm text-center">{{ error }}</div>
      <div v-if="success" class="text-green-500 mb-2 text-sm text-center">{{ success }}</div>
      
      <button :disabled="loading" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:opacity-50 mb-3">
        {{ loading ? 'Memverifikasi...' : 'Verifikasi OTP' }}
      </button>
      
      <div class="text-center">
        <button type="button" @click="resendOtp" :disabled="resendCooldown > 0" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
          {{ resendCooldown > 0 ? `Kirim ulang dalam ${resendCooldown}s` : 'Kirim Ulang OTP' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { authState } from '../authState'

const router = useRouter()
const step = ref('register') // register | otp
const loading = ref(false)
const error = ref('')
const success = ref('')
const otp = ref('')
const resendCooldown = ref(0)

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const submitRegister = async () => {
  error.value = ''
  loading.value = true
  try {
    await axios.post('/auth/register', form)
    step.value = 'otp'
    startCooldown()
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Gagal mendaftar. Periksa input Anda.'
    if (e.response?.data?.errors) {
      error.value = Object.values(e.response.data.errors).flat().join(', ')
    }
  } finally {
    loading.value = false
  }
}

const submitOtp = async () => {
  error.value = ''
  success.value = ''
  loading.value = true
  try {
    const response = await axios.post('/auth/verify-otp', {
      email: form.email,
      code: otp.value
    })
    
    // Simpan token dan user menggunakan authState
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
        // Force reload or redirect properly to update auth state
        router.push('/') 
    }, 1500)
    
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Kode OTP salah atau kedaluwarsa.'
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
    await axios.post('/auth/resend-otp', { email: form.email })
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
