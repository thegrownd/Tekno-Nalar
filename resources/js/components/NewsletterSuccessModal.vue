<template>
  <Transition name="modal">
    <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center px-4" @click.self="close">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="close"></div>
      
      <!-- Modal Content -->
      <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 max-w-sm w-full text-center transform transition-all scale-100 border border-gray-100 dark:border-gray-700">
        <!-- Success Icon -->
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900/30 mb-6 animate-bounce-small">
          <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>

        <!-- Title -->
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
          Terima Kasih!
        </h3>

        <!-- Message -->
        <p class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
          Terima kasih <span class="font-semibold text-cyan-600 dark:text-cyan-400">{{ name }}</span>, Anda sekarang berlangganan newsletter kami!
        </p>

        <!-- Button -->
        <button 
          @click="close"
          class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 shadow-lg transition-transform active:scale-95"
        >
          Tutup
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'

const props = defineProps({
  show: Boolean,
  name: String
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.show) {
    close()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-enter-from .transform,
.modal-leave-to .transform {
  transform: scale(0.9) translateY(20px);
  opacity: 0;
}

@keyframes bounce-small {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

.animate-bounce-small {
  animation: bounce-small 2s infinite ease-in-out;
}
</style>
