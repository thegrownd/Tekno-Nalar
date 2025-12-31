<template>
  <div class="relative" ref="dropdownRef">
    <!-- Bell Icon Button -->
    <button 
      @click="toggleDropdown"
      class="relative p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none"
    >
      <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <!-- Badge -->
      <span 
        v-if="unreadCount > 0" 
        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full"
      >
        {{ unreadCount }}
      </span>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div 
        v-if="isOpen" 
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
      >
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
          <h3 class="font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
          <button 
            v-if="unreadCount > 0"
            @click="markAllRead"
            class="text-xs text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-300 font-medium"
          >
            Tandai semua dibaca
          </button>
        </div>

        <div class="max-h-96 overflow-y-auto">
          <div v-if="loading" class="p-4 text-center text-gray-500">
            <svg class="w-6 h-6 mx-auto animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>

          <div v-else-if="notifications.length === 0" class="p-8 text-center text-gray-500 dark:text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p>Belum ada notifikasi</p>
          </div>

          <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
            <li 
              v-for="notification in notifications" 
              :key="notification.id"
              :class="['hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors', !notification.is_read ? 'bg-cyan-50/50 dark:bg-cyan-900/10' : '']"
            >
              <button @click="handleNotificationClick(notification)" class="w-full text-left p-4 flex gap-3">
                <div class="flex-shrink-0 mt-1">
                  <!-- Icon based on type -->
                  <div v-if="notification.type === 'article_status_update' && notification.message.includes('diterbitkan')" class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </div>
                  <div v-else-if="notification.type === 'article_status_update' && notification.message.includes('ditolak')" class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                  </div>
                   <div v-else-if="notification.type === 'new_article_submission'" class="w-8 h-8 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                  </div>
                  <div v-else class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                    {{ notification.message }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ formatDate(notification.created_at) }}
                  </p>
                </div>
                <div v-if="!notification.is_read" class="flex-shrink-0 self-center">
                  <div class="w-2 h-2 rounded-full bg-cyan-500"></div>
                </div>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const notifications = ref([]);
const isOpen = ref(false);
const loading = ref(false);
const dropdownRef = ref(null);
const router = useRouter();
let pollInterval = null;

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.is_read).length;
});

const fetchNotifications = async () => {
  try {
    const response = await axios.get('/notifications');
    notifications.value = response.data;
  } catch (error) {
    console.error('Failed to fetch notifications:', error);
  }
};

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    fetchNotifications();
  }
};

const markAsRead = async (notification) => {
  if (notification.is_read) return;
  try {
    await axios.put(`/notifications/${notification.id}/read`);
    notification.is_read = true;
  } catch (error) {
    console.error('Failed to mark as read:', error);
  }
};

const markAllRead = async () => {
  try {
    loading.value = true;
    await axios.put('/notifications/read-all');
    notifications.value.forEach(n => n.is_read = true);
  } catch (error) {
    console.error('Failed to mark all as read:', error);
  } finally {
    loading.value = false;
  }
};

const handleNotificationClick = async (notification) => {
  await markAsRead(notification);
  isOpen.value = false;
  
  if (notification.type === 'new_article_submission') {
    router.push('/admin/pending-articles');
  } else if (notification.type === 'article_status_update') {
    router.push('/my-articles');
  }
};


const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInSeconds = Math.floor((now - date) / 1000);
  
  if (diffInSeconds < 60) return 'Baru saja';
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit yang lalu`;
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam yang lalu`;
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
};

// Close on click outside
const closeOnClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  fetchNotifications();
  document.addEventListener('click', closeOnClickOutside);
  // Poll every 15 seconds for near real-time updates
  pollInterval = setInterval(fetchNotifications, 15000);
});

onUnmounted(() => {
  document.removeEventListener('click', closeOnClickOutside);
  if (pollInterval) clearInterval(pollInterval);
});
</script>