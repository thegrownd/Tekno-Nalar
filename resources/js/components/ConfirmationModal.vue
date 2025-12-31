<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="cancel"></div>

    <!-- Modal Content -->
    <div class="relative card-theme rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all scale-100">
      <div class="text-center">
        <!-- Icon -->
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
          <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>

        <!-- Title & Message -->
        <h3 class="text-lg leading-6 font-bold text-primary mb-2">{{ title }}</h3>
        <p class="text-sm text-muted mb-6">
          {{ message }}
        </p>

        <!-- Buttons -->
        <div class="flex gap-3 justify-center">
          <button
            @click="cancel"
            class="px-4 py-2 bg-hover text-secondary rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium"
          >
            Batal
          </button>
          <button
            @click="confirm"
            class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-500/30 font-medium"
          >
            Konfirmasi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  title: {
    type: String,
    default: 'Konfirmasi'
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin ingin melanjutkan tindakan ini?'
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const confirm = () => {
  emit('confirm')
}

const cancel = () => {
  emit('cancel')
}
</script>
