<template>
  <div class="flex items-center justify-center min-h-[50vh]">
    <div class="text-center">
      <div v-if="error" class="text-red-500 mb-4 p-4 border border-red-200 rounded-lg bg-red-50">
        <p class="font-bold text-xl mb-2">Login Gagal</p>
        <p class="mb-4">{{ error }}</p>
        <div class="space-x-4">
          <button @click="retryLogin" class="text-blue-600 hover:text-blue-800 font-semibold">
            Coba Lagi
          </button>
          <router-link to="/login" class="text-gray-600 hover:text-gray-800 font-semibold">
            Kembali ke Login
          </router-link>
        </div>
      </div>
      <div v-else class="flex flex-col items-center">
        <svg class="w-16 h-16 text-blue-600 animate-spin mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Memverifikasi Login...</h3>
        <p class="text-gray-600 dark:text-gray-400">Mohon tunggu sebentar, kami sedang menyiapkan akun Anda.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { authState } from '../authState';

const route = useRoute();
const router = useRouter();
const error = ref('');

const retryLogin = () => {
    window.location.href = '/api/auth/google'; // Restart the flow
};

onMounted(async () => {
  const code = route.query.code;
  
  console.log('Google Callback mounted. Code present:', !!code);

  if (!code) {
    error.value = 'Kode otentikasi tidak ditemukan. Silakan ulangi proses login.';
    return;
  }

  try {
    // Kirim code ke backend
    console.log('Sending code to backend...');
    const response = await axios.post('/auth/google/callback', { 
        code: code 
    });

    console.log('Backend response received:', response.status);

    const { access_token, user, is_super_admin } = response.data;

    if (access_token && user) {
        // Simpan data sesi menggunakan authState
        const isAdmin = user.is_admin || is_super_admin;
        
        if (typeof authState !== 'undefined' && authState.login) {
            authState.login(access_token, user, isAdmin, is_super_admin);
        } else {
            console.error('CRITICAL: authState is not defined or missing login method', { authState });
            // Fallback manual jika authState bermasalah
            localStorage.setItem('token', access_token);
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('is_admin', isAdmin ? 'true' : 'false');
            localStorage.setItem('is_super_admin', is_super_admin ? 'true' : 'false');
            axios.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;
        }
        
        console.log('Login successful. User:', user.email, 'Role:', isAdmin ? 'Admin' : 'User');

        // Redirect berdasarkan role
        if (isAdmin) {
            router.push('/admin/pending-articles'); // Atau dashboard admin lainnya
        } else {
            router.push('/'); // Halaman utama untuk user biasa
        }
    } else {
        throw new Error('Respons server tidak valid: Token tidak ditemukan.');
    }

  } catch (e) {
    console.error('Google Auth Error:', e);
    
    if (e.response) {
        console.error('Error Response:', e.response.data);
        error.value = e.response.data.message || 'Terjadi kesalahan pada server saat memproses login.';
    } else if (e.request) {
        error.value = 'Tidak dapat menghubungi server. Periksa koneksi internet Anda.';
    } else {
        error.value = e.message || 'Gagal memproses login Google.';
    }
  }
});
</script>
