<template>
  <div class="space-y-3">
    <!-- Upload Area - Only show if no preview -->
    <div 
      v-if="!previewUrl"
      class="border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer transition-all card-theme hover:border-cyan-500 hover:bg-hover"
      :class="dragOver ? 'border-cyan-500 bg-hover scale-105' : 'border-theme'"
      @dragover.prevent="onDragOver"
      @dragleave.prevent="onDragLeave"
      @drop.prevent="onDrop"
      @click="openPicker"
    >
      <div class="flex flex-col items-center justify-center gap-3">
        <div class="w-16 h-16 rounded-full bg-gradient-primary flex items-center justify-center">
          <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14h18V5c0-1.1-.9-2-2-2zm-8 12l2.5-3.01L16 15l3-4 3 4H5l6-7 4 5z"/>
          </svg>
        </div>
        <div class="text-secondary">
          <div class="font-semibold text-lg">Drag & Drop atau Klik untuk pilih gambar</div>
          <div class="text-sm text-muted mt-1">Menerima .jpg, .jpeg, .png, .gif • Maksimal 5MB</div>
        </div>
      </div>
      <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/gif,.jpg,.jpeg,.png,.gif" class="hidden" @change="onFileChange" />
    </div>

    <!-- Error Message -->
    <div v-if="error" class="flex items-start gap-3 text-sm px-4 py-3 rounded-xl bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 border-2 border-red-200 dark:border-red-800 animate-fade-in">
      <svg class="w-5 h-5 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
      </svg>
      <span>{{ error }}</span>
    </div>

    <!-- Real-time Preview (shown immediately after file selection) -->
    <div v-if="previewUrl" class="card-theme rounded-2xl p-4 space-y-4 animate-fade-in-scale">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-lg flex items-center gap-2">
          <svg class="w-5 h-5 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
          </svg>
          Preview Gambar
        </h3>
        <button 
          type="button"
          @click="remove" 
          class="text-xs px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors flex items-center gap-1 font-medium"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
          </svg>
          Hapus
        </button>
      </div>

      <!-- Image Preview -->
      <div class="relative rounded-xl overflow-hidden bg-black/5 dark:bg-white/5 border-2 border-cyan-200 dark:border-cyan-800">
        <img 
          :src="previewUrl" 
          :key="previewUrl"
          alt="Preview" 
          class="w-full h-auto max-h-96 object-contain animate-fade-in-scale"
          @error="handleImageError"
        />
        <div class="absolute top-3 right-3 px-3 py-1.5 rounded-full bg-green-500 text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
          </svg>
          Preview Siap
        </div>
      </div>

      <!-- File Info -->
      <div class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-2 text-muted">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
          </svg>
          <span>{{ fileName }}</span>
        </div>
        <span class="text-muted">{{ humanSize }}</span>
      </div>

      <!-- Canvas for Editing (hidden but functional) -->
      <div v-if="imageSrc" class="space-y-3">
        <details class="card-theme rounded-xl p-4">
          <summary class="cursor-pointer font-semibold text-sm flex items-center gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/>
            </svg>
            Edit Gambar (Opsional)
          </summary>
          
          <div class="mt-4 space-y-4">
            <!-- Canvas for editing -->
            <div class="relative bg-black/5 dark:bg-white/5 rounded-xl overflow-hidden">
              <canvas ref="canvas" class="max-w-full mx-auto"></canvas>
            </div>

            <!-- Editing Controls -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Rotation -->
              <div class="space-y-2">
                <label class="text-xs font-semibold text-secondary">Rotasi</label>
                <div class="flex gap-2">
                  <button type="button" @click="rotate(90)" class="flex-1 px-3 py-2 rounded-lg bg-hover text-secondary hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition-colors text-sm font-medium">90°</button>
                  <button type="button" @click="rotate(180)" class="flex-1 px-3 py-2 rounded-lg bg-hover text-secondary hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition-colors text-sm font-medium">180°</button>
                  <button type="button" @click="rotate(270)" class="flex-1 px-3 py-2 rounded-lg bg-hover text-secondary hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition-colors text-sm font-medium">270°</button>
                </div>
              </div>

              <!-- Quality -->
              <div class="space-y-2">
                <label class="text-xs font-semibold text-secondary">Kualitas (JPEG): {{ quality }}%</label>
                <input type="range" min="50" max="100" v-model="quality" class="w-full accent-cyan-500" />
              </div>

              <!-- Zoom -->
              <div class="space-y-2 md:col-span-2">
                <label class="text-xs font-semibold text-secondary">Zoom: {{ zoom }}%</label>
                <input type="range" min="50" max="150" v-model="zoom" class="w-full accent-cyan-500" />
              </div>
            </div>

            <!-- Change Image Button -->
            <button type="button" @click="openPicker" class="w-full px-4 py-2 rounded-lg bg-hover text-secondary hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition-colors font-medium flex items-center justify-center gap-2">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14h18V5c0-1.1-.9-2-2-2zm-8 12l2.5-3.01L16 15l3-4 3 4H5l6-7 4 5z"/>
              </svg>
              Ganti Gambar
            </button>
          </div>
        </details>
      </div>

      <!-- Upload Progress -->
      <div v-if="progress > 0 && progress < 100" class="space-y-2">
        <div class="flex items-center justify-between text-sm">
          <span class="text-secondary font-medium">Mengunggah...</span>
          <span class="text-cyan-600 font-bold">{{ progress }}%</span>
        </div>
        <div class="w-full h-3 bg-hover rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-cyan-500 to-blue-600 transition-all duration-300 rounded-full" :style="{ width: progress + '%' }"></div>
        </div>
      </div>

      <!-- Upload Button -->
      <div class="flex items-center gap-3">
        <button 
          type="button"
          :disabled="uploading || uploadComplete" 
          @click="upload" 
          class="flex-1 btn-gradient text-white px-6 py-3 rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all font-semibold flex items-center justify-center gap-2"
        >
          <svg v-if="!uploading && !uploadComplete" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
          </svg>
          <svg v-else-if="uploading" class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else-if="uploadComplete" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
          </svg>
          <span v-if="!uploading && !uploadComplete">Upload ke Server</span>
          <span v-else-if="uploading">Mengunggah...</span>
          <span v-else-if="uploadComplete">Upload Berhasil!</span>
        </button>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="success" class="flex items-center gap-2 text-sm px-4 py-3 rounded-xl bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 border-2 border-green-200 dark:border-green-800 animate-fade-in">
        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="font-medium">Gambar berhasil diunggah dan siap digunakan!</span>
      </div>
      <div v-if="uploadError" class="flex items-center gap-2 text-sm px-4 py-3 rounded-xl bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 border-2 border-red-200 dark:border-red-800 animate-fade-in">
        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <span class="font-medium">{{ uploadError }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, watch, onMounted } from 'vue'

const emit = defineEmits(['uploaded', 'change'])

const fileInput = ref(null)
const dragOver = ref(false)
const error = ref('')
const imageSrc = ref('')
const previewUrl = ref('') // Real-time preview URL
const fileName = ref('')
const imageFile = ref(null)
const image = ref(null)
const canvas = ref(null)
const ctx = ref(null)
const rotation = ref(0)
const quality = ref(90)
const zoom = ref(100)
const progress = ref(0)
const uploading = ref(false)
const uploadComplete = ref(false)
const success = ref(false)
const uploadError = ref('')

const humanSize = ref('')
const allowedTypes = ['image/jpeg', 'image/png', 'image/gif']

const openPicker = () => fileInput.value && fileInput.value.click()
const onDragOver = () => { dragOver.value = true }
const onDragLeave = () => { dragOver.value = false }
const onDrop = (e) => {
  dragOver.value = false
  const f = e.dataTransfer.files?.[0]
  if (f) validateAndLoad(f)
}
const onFileChange = (e) => {
  const f = e.target.files?.[0]
  if (f) validateAndLoad(f)
}

const validateAndLoad = (file) => {
  error.value = ''
  uploadError.value = ''
  success.value = false
  uploadComplete.value = false

  // Validate file type
  if (!allowedTypes.includes(file.type)) {
    error.value = 'Format tidak didukung. Gunakan JPG, PNG, atau GIF.'
    return
  }
  
  // Validate file size
  if (file.size > 5 * 1024 * 1024) {
    error.value = 'Ukuran file melebihi 5MB. Silakan pilih gambar yang lebih kecil.'
    return
  }
  
  // Store file info
  imageFile.value = file
  fileName.value = file.name
  humanSize.value = (file.size / (1024 * 1024)).toFixed(2) + ' MB'
  
  // Read file and show preview immediately
  const reader = new FileReader()
  reader.onload = (ev) => {
    const dataUrl = ev.target.result
    imageSrc.value = dataUrl
    previewUrl.value = dataUrl // Show preview immediately
    
    // Load image for canvas editing
    image.value = new Image()
    image.value.onload = () => draw()
    image.value.src = dataUrl
    
    // Emit change event
    emit('change', file)
  }
  reader.readAsDataURL(file)
}

const draw = () => {
  if (!canvas.value || !image.value) return
  ctx.value = canvas.value.getContext('2d')

  const targetW = 960
  const targetH = 540 // 16:9
  canvas.value.width = targetW
  canvas.value.height = targetH

  const scale = zoom.value / 100
  const imgW = image.value.width * scale
  const imgH = image.value.height * scale

  ctx.value.clearRect(0, 0, targetW, targetH)
  ctx.value.save()
  // Apply rotation
  if (rotation.value !== 0) {
    ctx.value.translate(targetW / 2, targetH / 2)
    ctx.value.rotate(rotation.value * Math.PI / 180)
    ctx.value.translate(-targetW / 2, -targetH / 2)
  }
  // Center image
  const x = (targetW - imgW) / 2
  const y = (targetH - imgH) / 2
  ctx.value.drawImage(image.value, x, y, imgW, imgH)
  ctx.value.restore()
}

watch([rotation, zoom], draw)
onMounted(() => { if (imageSrc.value) draw() })

const rotate = (deg) => { rotation.value = (rotation.value + deg) % 360 }
const remove = () => {
  imageSrc.value = ''
  previewUrl.value = ''
  fileName.value = ''
  imageFile.value = null
  humanSize.value = ''
  progress.value = 0
  success.value = false
  uploadComplete.value = false
  uploadError.value = ''
  rotation.value = 0
  zoom.value = 100
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const handleImageError = (e) => {
  console.error('Image load error:', e)
  uploadError.value = 'Gagal memuat gambar. Periksa URL atau coba lagi.'
}

const upload = async () => {
  if (!canvas.value) return
  
  uploading.value = true
  progress.value = 0
  success.value = false
  uploadComplete.value = false
  uploadError.value = ''
  
  try {
    // Determine file type
    const isPng = imageFile.value?.type === 'image/png'
    const isGif = imageFile.value?.type === 'image/gif'
    const mime = isPng ? 'image/png' : isGif ? 'image/gif' : 'image/jpeg'
    const q = isPng || isGif ? undefined : quality.value / 100
    
    // Convert canvas to blob
    const dataUrl = canvas.value.toDataURL(mime, q)
    const blob = await (await fetch(dataUrl)).blob()
    
    // Create form data
    const form = new FormData()
    const ext = isPng ? '.png' : isGif ? '.gif' : '.jpg'
    form.append('file', blob, (imageFile.value?.name || 'image') + ext)

    // Upload to server
    const token = localStorage.getItem('token')
    const { data } = await axios.post('/uploads/images', form, {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
      onUploadProgress: (e) => {
        if (e.total) {
          progress.value = Math.round((e.loaded / e.total) * 100)
        }
      }
    })
    
    // Success
    success.value = true
    uploadComplete.value = true
    
    // Update preview with uploaded URL - force re-render with key change
    const uploadedUrl = data.url
    console.log('Upload successful, URL:', uploadedUrl)
    
    // Clear and update preview to trigger animation
    previewUrl.value = ''
    await new Promise(resolve => setTimeout(resolve, 50))
    previewUrl.value = uploadedUrl
    
    // Emit uploaded event
    emit('uploaded', uploadedUrl)
  } catch (e) {
    console.error('Upload error:', e)
    uploadError.value = e.response?.data?.message || 'Gagal mengunggah gambar. Silakan coba lagi.'
    progress.value = 0
  } finally {
    uploading.value = false
  }
}
</script>
