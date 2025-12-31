<template>
  <div class="animate-fade-in max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold tracking-tight gradient-text mb-2">Manajemen User</h1>
        <p class="text-secondary">Kelola pengguna terdaftar dan hak akses.</p>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">User</th>
              <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Email</th>
              <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Role</th>
              <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Status</th>
              <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Terdaftar</th>
              <th class="px-6 py-4 font-semibold text-gray-900 dark:text-white text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-if="loading" class="animate-pulse">
              <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Memuat data...</td>
            </tr>
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                    {{ user.name.charAt(0) }}
                  </div>
                  <span class="font-medium text-gray-900 dark:text-white">{{ user.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                {{ user.email }}
              </td>
              <td class="px-6 py-4">
                <span :class="['px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider', user.is_admin ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300']">
                  {{ user.is_admin ? 'Admin' : 'User' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span :class="['px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider', user.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300']">
                  {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                  <button 
                    @click="toggleActive(user)" 
                    :disabled="isSuperAdmin(user)"
                    :class="['p-2 rounded-lg transition-colors', isSuperAdmin(user) ? 'opacity-50 cursor-not-allowed text-gray-400' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40']"
                    :title="user.is_active ? 'Nonaktifkan User' : 'Aktifkan User'"
                  >
                    <svg v-if="user.is_active" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z"/><circle cx="12" cy="17" r="1"/></svg>
                    <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                  </button>
                  <button 
                    @click="confirmDelete(user)" 
                    :disabled="isSuperAdmin(user)"
                    :class="['p-2 rounded-lg transition-colors', isSuperAdmin(user) ? 'opacity-50 cursor-not-allowed text-gray-400' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40']"
                    title="Hapus User"
                  >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8a2 2 0 002-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="totalPages > 1" class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-2">
        <button @click="page--" :disabled="page === 1" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 disabled:opacity-50 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Prev</button>
        <span class="px-4 py-2 text-gray-700 dark:text-gray-300">Page {{ page }} of {{ totalPages }}</span>
        <button @click="page++" :disabled="page === totalPages" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 disabled:opacity-50 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Next</button>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 animate-fade-in">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6">
          <div class="flex items-center gap-3 mb-4 text-red-600 dark:text-red-400">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            <h3 class="text-xl font-bold">Hapus User?</h3>
          </div>
          <p class="text-secondary mb-4">
            Anda akan menghapus user <strong>{{ userToDelete?.name }}</strong>. Tindakan ini tidak dapat dibatalkan.
          </p>
          
          <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
            <label class="flex items-center gap-3 cursor-pointer">
              <input type="checkbox" v-model="backupData" class="w-5 h-5 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
              <span class="text-sm font-medium text-primary">Backup konten user sebelum menghapus</span>
            </label>
            <p class="text-xs text-secondary mt-2 ml-8">Jika dicentang, data artikel user akan diunduh.</p>
          </div>

          <div class="flex gap-3 justify-end">
            <button @click="showDeleteModal = false" class="px-4 py-2 rounded-xl text-secondary hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Batal</button>
            <button @click="deleteUser" class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition-colors">Hapus User</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'

const users = ref([])
const loading = ref(true)
const page = ref(1)
const totalPages = ref(1)
let pollingInterval = null

const showDeleteModal = ref(false)
const userToDelete = ref(null)
const backupData = ref(false)

const fetchUsers = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/admin/users?page=${page.value}`)
    users.value = data.data
    totalPages.value = data.last_page
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  })
}

const isSuperAdmin = (user) => {
  return !!user.is_super_admin
}

const confirmDelete = (user) => {
  userToDelete.value = user
  backupData.value = false
  showDeleteModal.value = true
}

const toggleActive = async (user) => {
  try {
    const { data } = await axios.put(`/admin/users/${user.id}/toggle-active`)
    user.is_active = data.user.is_active
  } catch (e) {
    alert('Gagal mengubah status user: ' + (e.response?.data?.message || 'Error'))
  }
}

const deleteUser = async () => {
  if (!userToDelete.value) return

  if (backupData.value) {
    try {
        const { data } = await axios.get(`/admin/users/${userToDelete.value.id}/backup`)
        const json = JSON.stringify(data, null, 2)
        const blob = new Blob([json], { type: 'application/json' })
        const url = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `backup_user_${userToDelete.value.id}_${new Date().toISOString().split('T')[0]}.json`
        a.click()
        URL.revokeObjectURL(url)
    } catch (e) {
        alert('Gagal membackup data user: ' + (e.response?.data?.message || 'Error'))
        return // Stop deletion if backup fails? Maybe optional. But user asked for backup.
    }
  }
  
  try {
    await axios.delete(`/admin/users/${userToDelete.value.id}`)
    showDeleteModal.value = false
    userToDelete.value = null
    fetchUsers()
  } catch (e) {
    alert('Gagal menghapus user: ' + (e.response?.data?.message || 'Error'))
  }
}

watch(page, fetchUsers)

onMounted(fetchUsers)
</script>
