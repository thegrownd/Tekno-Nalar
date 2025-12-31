<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" @click.self="onCancel">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Modal -->
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 animate-modal-in">
          <!-- Icon -->
          <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full" :class="iconBgClass">
            <svg class="w-8 h-8" :class="iconColorClass" viewBox="0 0 24 24" fill="currentColor">
              <path v-if="type === 'danger'" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              <path v-else d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>

          <!-- Title -->
          <h3 class="text-xl font-bold text-center mb-2 text-gray-900 dark:text-white">
            {{ title }}
          </h3>

          <!-- Message -->
          <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
            {{ message }}
          </p>

          <!-- Actions -->
          <div class="flex gap-3">
            <button
              @click="onCancel"
              class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 font-semibold transition-all text-gray-700 dark:text-gray-300"
            >
              {{ cancelText }}
            </button>
            <button
              @click="onConfirm"
              class="flex-1 px-4 py-3 rounded-xl font-semibold transition-all shadow-lg"
              :class="confirmButtonClass"
            >
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Konfirmasi'
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Konfirmasi'
  },
  cancelText: {
    type: String,
    default: 'Batal'
  },
  type: {
    type: String,
    default: 'danger', // 'danger' or 'success'
    validator: (value) => ['danger', 'success'].includes(value)
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const iconBgClass = computed(() => {
  return props.type === 'danger' 
    ? 'bg-red-100 dark:bg-red-900/30' 
    : 'bg-green-100 dark:bg-green-900/30'
})

const iconColorClass = computed(() => {
  return props.type === 'danger' 
    ? 'text-red-600 dark:text-red-400' 
    : 'text-green-600 dark:text-green-400'
})

const confirmButtonClass = computed(() => {
  return props.type === 'danger'
    ? 'bg-red-500 hover:bg-red-600 text-white'
    : 'bg-green-500 hover:bg-green-600 text-white'
})

const onConfirm = () => {
  emit('confirm')
}

const onCancel = () => {
  emit('cancel')
}
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

@keyframes modalIn {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-modal-in {
  animation: modalIn 0.3s ease-out;
}
</style>
