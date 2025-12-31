<template>
  <div class="max-w-4xl mx-auto animate-fade-in">
    <div class="card-theme rounded-3xl shadow-2xl overflow-hidden border border-theme">
      <!-- Header -->
      <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-8 text-white">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
              <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold">{{ isEdit ? 'Edit Artikel' : 'Buat Artikel Baru' }}</h1>
            <p class="text-white/80 text-sm">{{ isEdit ? 'Perbarui konten artikel Anda' : 'Tulis dan publikasikan artikel Anda' }}</p>
          </div>
        </div>
      </div>

      <!-- Form Content -->
      <form @submit.prevent="submit" class="p-8 space-y-6">
        <!-- Publication Status Indicator -->
        <div v-if="isAdmin" class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 flex items-start gap-3 animate-fade-in">
          <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
          <div>
            <p class="font-bold text-green-800 dark:text-green-200">Mode Admin</p>
            <p class="text-sm text-green-600 dark:text-green-400">Artikel yang Anda buat akan <span class="font-bold underline">langsung diterbitkan</span> tanpa proses verifikasi.</p>
          </div>
        </div>
        <div v-else class="p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 flex items-start gap-3 animate-fade-in">
          <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
          </svg>
          <div>
            <p class="font-bold text-yellow-800 dark:text-yellow-200">Menunggu Verifikasi</p>
            <p class="text-sm text-yellow-600 dark:text-yellow-400">Artikel akan diperiksa oleh admin sebelum diterbitkan ke publik.</p>
          </div>
        </div>

        <!-- Title Input -->
        <div class="space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-secondary">
            <svg class="w-5 h-5 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/>
            </svg>
            Judul Artikel
          </label>
          <input 
            v-model="title" 
            type="text" 
            placeholder="Masukkan judul artikel yang menarik..." 
            class="input-theme w-full px-4 py-3 rounded-xl border-2 outline-none transition-all text-lg font-medium"
            required 
          />
        </div>

        <!-- Image Upload Section -->
        <div class="space-y-4">
          <label class="flex items-center gap-2 text-sm font-semibold text-secondary">
            <svg class="w-5 h-5 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
              <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
            </svg>
            Gambar Artikel
          </label>

          <!-- Upload Method Tabs -->
          <div class="flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl">
            <button 
              type="button"
              @click="selectMethod('url')"
              :class="['flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all', uploadMethod === 'url' ? 'bg-white dark:bg-gray-700 shadow-sm text-cyan-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300']"
            >
              Link Eksternal
            </button>
            <button 
              type="button"
              @click="selectMethod('upload')"
              :class="['flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all', uploadMethod === 'upload' ? 'bg-white dark:bg-gray-700 shadow-sm text-cyan-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300']"
            >
              Upload Lokal
            </button>
          </div>

          <!-- URL Input -->
          <div v-if="uploadMethod === 'url'" class="space-y-2 animate-fade-in">
            <input 
              v-model="image_url" 
              type="url" 
              placeholder="https://example.com/image.jpg" 
              class="input-theme w-full px-4 py-3 rounded-xl border-2 outline-none transition-all"
              :required="uploadMethod === 'url'"
            />
            <p class="text-xs text-muted flex items-center gap-1">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              Gunakan URL gambar dari Unsplash, Pexels, atau sumber lainnya
            </p>
          </div>

          <!-- File Upload Input -->
          <div v-if="uploadMethod === 'upload'" class="space-y-2 animate-fade-in">
            <div 
              class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-cyan-500 transition-colors cursor-pointer bg-gray-50 dark:bg-gray-800/50"
              @click="$refs.fileInput.click()"
              @dragover.prevent
              @drop.prevent="handleDrop"
            >
              <input 
                ref="fileInput"
                type="file" 
                class="hidden" 
                accept="image/png, image/jpeg, image/jpg, image/webp, image/gif"
                @change="onLocalFileChange"
              />
              <div v-if="!image_file && !image_url" class="space-y-2">
                <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center mx-auto text-cyan-600">
                  <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z"/></svg>
                </div>
                <p class="font-medium text-gray-700 dark:text-gray-300">Klik untuk upload atau drag & drop</p>
                <p class="text-xs text-muted">JPG, PNG, WebP, GIF (Max 5MB)</p>
              </div>
              <div v-else class="flex flex-col items-center gap-2">
                 <p class="text-sm font-medium text-cyan-600 truncate max-w-xs">{{ fileName || 'Gambar Terpilih' }}</p>
                 <button type="button" @click.stop="clearImage" class="text-xs text-red-500 hover:underline">Hapus Gambar</button>
              </div>
            </div>
          </div>
          
          <!-- Image Preview -->
          <div v-if="image_url" class="mt-4 relative rounded-2xl overflow-hidden shadow-xl group bg-black/5 border border-gray-200 dark:border-gray-700">
            <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all">
              <button 
                type="button" 
                @click="openEditor"
                class="p-2 bg-black/50 hover:bg-cyan-500 text-white rounded-full transition-all"
                title="Edit Gambar"
                v-if="uploadMethod === 'upload'"
              >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
              </button>
              <button 
                type="button" 
                @click="clearImage"
                class="p-2 bg-black/50 hover:bg-red-500 text-white rounded-full transition-all"
                title="Hapus Gambar"
              >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
              </button>
            </div>
            <div v-if="!imageLoaded" class="absolute inset-0 skeleton"></div>
            <img 
              :src="image_url" 
              alt="Preview" 
              @load="imageLoaded = true"
              @error="handleImageError"
              class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" 
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6 pointer-events-none">
              <p class="text-white font-semibold">Preview Gambar</p>
            </div>
          </div>
        </div>

        <!-- Image Editor Modal -->
        <div v-if="showEditor" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
              <h3 class="font-bold text-lg">Edit Gambar</h3>
              <button @click="closeEditor" type="button" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
              </button>
            </div>
            
            <div class="flex-1 overflow-hidden relative bg-gray-900 flex items-center justify-center p-4">
               <canvas ref="editorCanvas" class="max-w-full max-h-[60vh] object-contain shadow-lg"></canvas>
            </div>

            <div class="p-6 space-y-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
               <div class="flex flex-wrap items-center justify-center gap-6">
                 <!-- Zoom Control -->
                 <div class="flex items-center gap-3">
                   <span class="text-sm font-medium">Zoom</span>
                   <input 
                     type="range" 
                     v-model.number="editorScale" 
                     min="0.5" 
                     max="3" 
                     step="0.1"
                     class="w-32 accent-cyan-600"
                     @input="drawEditor"
                   >
                   <span class="text-xs w-8">{{ Math.round(editorScale * 100) }}%</span>
                 </div>

                 <!-- Rotate Control -->
                 <button 
                   type="button" 
                   @click="rotateEditor(90)"
                   class="flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                 >
                   <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7.11 8.53L5.7 7.11C4.8 8.27 4.24 9.61 4.07 11h2.02c.14-.87.49-1.72 1.02-2.47zM6.09 13H4.07c.17 1.39.72 2.73 1.62 3.89l1.41-1.42c-.52-.75-.87-1.59-1.01-2.47zm1.01 5.32c1.16.9 2.51 1.44 3.9 1.61V17.9c-.87-.15-1.71-.49-2.46-1.03L7.1 18.32zM13 4.07V1L8.45 5.55 13 10V6.09c2.84.48 5 2.94 5 5.91s-2.16 5.43-5 5.91v2.02c3.95-.49 7-3.85 7-7.93s-3.05-7.44-7-7.93z"/></svg>
                   Rotasi
                 </button>

                 <!-- Crop Mode (Visual Toggle only for this simple implementation) -->
                 <div class="flex items-center gap-2">
                    <span class="text-xs text-muted">Drag gambar untuk mengatur posisi crop</span>
                 </div>
               </div>

               <div class="flex justify-end gap-3 pt-2">
                 <button 
                   type="button" 
                   @click="closeEditor"
                   class="px-6 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition-all"
                 >
                   Batal
                 </button>
                 <button 
                   type="button" 
                   @click="saveEditor"
                   class="px-6 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold shadow-lg hover:shadow-cyan-500/30 transition-all flex items-center gap-2"
                 >
                   <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                   Simpan Perubahan
                 </button>
               </div>
            </div>
          </div>
        </div>

        <!-- Category Select -->
        <div class="space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-secondary">
            <svg class="w-5 h-5 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
              <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
            </svg>
            Kategori
          </label>
          <select 
            v-model="category" 
            class="input-theme w-full px-4 py-3 rounded-xl border-2 outline-none transition-all font-medium appearance-none pr-10 relative"
            style="background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e&quot;); background-position: right 1rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em;"
            required
          >
            <option value="Cyber Security" class="bg-card text-primary">🛡️ Cyber Security</option>
            <option value="Pemrograman" class="bg-card text-primary">💻 Pemrograman</option>
            <option value="Teknologi" class="bg-card text-primary">🚀 Teknologi</option>
            <option value="Sistem Digital" class="bg-card text-primary"> Sistem Digital</option>
          </select>
          <div class="mt-2">
            <span :class="['inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider', previewClass]">{{ category }}</span>
            <span class="ml-2 text-xs text-muted">Kontras: {{ previewContrast.toFixed(2) }}:1</span>
          </div>
        </div>

        <!-- Content Textarea -->
        <div class="space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-secondary">
            <svg class="w-5 h-5 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
              <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
            Konten Artikel
          </label>
          <textarea 
            v-model="content" 
            placeholder="Tulis konten artikel Anda di sini..." 
            class="input-theme w-full px-4 py-3 rounded-xl border-2 outline-none transition-all min-h-[300px] resize-y"
            required 
          />
          <div class="flex items-center justify-between text-xs text-muted">
            <span>{{ content.length }} karakter</span>
            <span>Minimal 50 karakter</span>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 flex items-start gap-3 animate-fade-in">
          <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
          </svg>
          <div class="flex-1">
            <p class="font-semibold text-red-800 dark:text-red-200">Terjadi Kesalahan</p>
            <p class="text-red-600 dark:text-red-400">{{ error }}</p>
            <ul v-if="Object.keys(validationErrors).length > 0" class="mt-2 list-disc list-inside text-sm text-red-600 dark:text-red-400">
              <li v-for="(errs, field) in validationErrors" :key="field">
                {{ errs[0] }}
              </li>
            </ul>
          </div>
        </div>

        <!-- Draft & Backup Controls -->
        <div class="flex items-center justify-between text-sm text-secondary animate-fade-in">
           <div class="flex items-center gap-2">
             <span v-if="lastSaved" class="text-green-600 dark:text-green-400 flex items-center gap-1">
               <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
               Tersimpan otomatis {{ lastSaved }}
             </span>
             <button 
               v-if="hasDraft && !isEdit" 
               type="button" 
               @click="restoreDraft"
               class="text-cyan-600 hover:underline font-medium"
             >
               Pulihkan Draft Terakhir
             </button>
           </div>
           <button 
             type="button" 
             @click="downloadBackup"
             class="flex items-center gap-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
             title="Download Backup Artikel"
           >
             <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
             Backup Lokal
           </button>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-4 pt-4">
          <button 
            type="submit"
            :disabled="loading"
            class="flex-1 btn-gradient text-white px-6 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
          >
            <svg v-if="!loading" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
              <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
            <svg v-else class="w-6 h-6 animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Menyimpan...' : (isEdit ? 'Perbarui Artikel' : 'Publikasikan Artikel') }}
          </button>
          <router-link 
            to="/" 
            class="px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 font-semibold transition-all flex items-center gap-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
            Batal
          </router-link>
        </div>
      </form>
    </div>

    <!-- Tips Section -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="card-theme rounded-2xl p-6 shadow-lg border border-theme">
        <div class="w-12 h-12 rounded-full bg-gradient-primary flex items-center justify-center mb-4">
          <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
        <h3 class="font-bold text-lg mb-2">Judul Menarik</h3>
        <p class="text-sm text-secondary">Buat judul yang singkat, jelas, dan menarik perhatian pembaca.</p>
      </div>
      <div class="card-theme rounded-2xl p-6 shadow-lg border border-theme">
        <div class="w-12 h-12 rounded-full bg-gradient-secondary flex items-center justify-center mb-4">
          <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
          </svg>
        </div>
        <h3 class="font-bold text-lg mb-2">Gambar Berkualitas</h3>
        <p class="text-sm text-secondary">Gunakan gambar beresolusi tinggi yang relevan dengan konten artikel.</p>
      </div>
      <div class="card-theme rounded-2xl p-6 shadow-lg border border-theme">
        <div class="w-12 h-12 rounded-full bg-gradient-success flex items-center justify-center mb-4">
          <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
          </svg>
        </div>
        <h3 class="font-bold text-lg mb-2">Konten Berkualitas</h3>
        <p class="text-sm text-secondary">Tulis konten yang informatif, mudah dipahami, dan memberikan nilai tambah.</p>
      </div>
    </div>
    
    <!-- Success Modal -->
    <ArticleSuccessModal 
      :show="showSuccessModal" 
      @close="handleSuccessModalClose" 
    />
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ArticleSuccessModal from './ArticleSuccessModal.vue'

const route = useRoute()
const router = useRouter()

const id = route.params.id
const isEdit = computed(() => !!id)
const isAdmin = ref(false)

const title = ref('')
const content = ref('')
const category = ref('Cyber Security')
const image_url = ref('')
const image_file = ref(null)
const fileName = ref('')
const uploadMethod = ref('url')
const showPreview = computed(() => !!image_url.value)
const error = ref('')
const loading = ref(false)
const imageLoaded = ref(false)
const fileInput = ref(null)

const validationErrors = ref({})
const showSuccessModal = ref(false)
const lastSaved = ref(null)
const hasDraft = ref(false)

const handleSuccessModalClose = () => {
  showSuccessModal.value = false
  if (!isEdit.value) router.push('/')
}

const restoreDraft = () => {
  const draft = localStorage.getItem('article_draft')
  if (draft) {
    const data = JSON.parse(draft)
    title.value = data.title
    content.value = data.content
    category.value = data.category
    image_url.value = data.image_url
  }
}

const downloadBackup = () => {
  const data = {
    title: title.value,
    content: content.value,
    category: category.value,
    image_url: image_url.value
  }
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'article-backup.json'
  link.click()
}

let saveTimeout
const debounceSave = () => {
  if (isEdit.value) return
  clearTimeout(saveTimeout)
  saveTimeout = setTimeout(() => {
    localStorage.setItem('article_draft', JSON.stringify({
      title: title.value,
      content: content.value,
      category: category.value,
      image_url: image_url.value
    }))
    lastSaved.value = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    hasDraft.value = true
  }, 1000)
}

// Watch for changes to auto-save
watch([title, content, category, image_url], () => {
  debounceSave()
})

// Editor State
const showEditor = ref(false)
const editorCanvas = ref(null)
const editorImage = ref(null)
const editorScale = ref(1)
const editorRotation = ref(0)
const editorCtx = ref(null)

const methodInfo = computed(() => {
  return uploadMethod.value === 'url' ? 'Link Gambar Eksternal' : 'Upload File Lokal'
})

const methodDescription = computed(() => {
  return uploadMethod.value === 'url' 
    ? 'Gunakan URL gambar dari internet (Unsplash, Pexels, dll)' 
    : 'Upload gambar langsung dari perangkat Anda'
})

const methodInfoClass = computed(() => {
  return uploadMethod.value === 'url' 
    ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300'
    : 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300'
})

onMounted(async () => {
  isAdmin.value = localStorage.getItem('is_admin') === 'true'
  
  if (isEdit.value) {
    try {
      const { data } = await axios.get(`/articles/${id}`)
      title.value = data.title
      content.value = data.content
      category.value = data.category
      image_url.value = data.image_url
    } catch (e) {
      error.value = 'Gagal memuat artikel'
    }
  }
})

const selectMethod = (method) => {
  uploadMethod.value = method
  // Clear image data when switching methods to avoid confusion
  if (method === 'url' && image_file.value) {
    clearImage()
  } else if (method === 'upload' && image_url.value && !image_file.value) {
    // If switching to upload but we have a URL (that is not a blob URL from file), clear it
    clearImage()
  }
}

const onUploadedUrl = (url) => {
  image_url.value = url
  imageLoaded.value = false
}

const processFile = (file) => {
  if (!file) return
  
  // Validate file type
  if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type)) {
    error.value = 'Format file tidak didukung. Gunakan JPG, PNG, WebP, atau GIF.'
    return
  }
  
  // Validate file size (5MB)
  if (file.size > 5 * 1024 * 1024) {
    error.value = 'Ukuran file terlalu besar. Maksimal 5MB.'
    return
  }
  
  image_file.value = file
  fileName.value = file.name
  image_url.value = URL.createObjectURL(file)
  imageLoaded.value = false
  error.value = ''
}

const onLocalFileChange = (e) => {
  const file = e.target.files[0]
  processFile(file)
}

const handleDrop = (e) => {
  const file = e.dataTransfer.files[0]
  processFile(file)
}

const clearImage = () => {
  image_url.value = ''
  image_file.value = null
  fileName.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const handleImageError = (e) => {
  e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="600" height="400"%3E%3Cdefs%3E%3ClinearGradient id="grad" x1="0%25" y1="0%25" x2="100%25" y2="100%25"%3E%3Cstop offset="0%25" style="stop-color:%2306B6D4;stop-opacity:1" /%3E%3Cstop offset="100%25" style="stop-color:%233B82F6;stop-opacity:1" /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width="600" height="400" fill="url(%23grad)" /%3E%3Ctext x="50%25" y="50%25" font-family="Arial" font-size="20" fill="white" text-anchor="middle" dominant-baseline="middle"%3EGambar tidak dapat dimuat%3C/text%3E%3C/svg%3E'
}

// Editor Logic
const openEditor = () => {
  if (!image_url.value) return
  
  showEditor.value = true
  editorScale.value = 1
  editorRotation.value = 0
  
  const img = new Image()
  img.crossOrigin = 'anonymous'
  img.onload = () => {
    editorImage.value = img
    // Initialize canvas after image loads and DOM updates
    setTimeout(() => {
       initEditor()
    }, 100)
  }
  img.src = image_url.value
}

const closeEditor = () => {
  showEditor.value = false
  editorImage.value = null
}

const initEditor = () => {
  if (!editorCanvas.value || !editorImage.value) return
  
  const canvas = editorCanvas.value
  const ctx = canvas.getContext('2d')
  editorCtx.value = ctx
  
  // Set initial canvas size based on image but limited max size for performance
  const MAX_WIDTH = 800
  const MAX_HEIGHT = 600
  
  let width = editorImage.value.width
  let height = editorImage.value.height
  
  if (width > height) {
    if (width > MAX_WIDTH) {
      height *= MAX_WIDTH / width
      width = MAX_WIDTH
    }
  } else {
    if (height > MAX_HEIGHT) {
      width *= MAX_HEIGHT / height
      height = MAX_HEIGHT
    }
  }
  
  canvas.width = width
  canvas.height = height
  
  drawEditor()
}

const drawEditor = () => {
  if (!editorCtx.value || !editorImage.value) return
  
  const canvas = editorCanvas.value
  const ctx = editorCtx.value
  const img = editorImage.value
  
  // Clear canvas
  ctx.clearRect(0, 0, canvas.width, canvas.height)
  
  ctx.save()
  
  // Move to center
  ctx.translate(canvas.width / 2, canvas.height / 2)
  
  // Rotate
  ctx.rotate((editorRotation.value * Math.PI) / 180)
  
  // Scale
  ctx.scale(editorScale.value, editorScale.value)
  
  // Draw image centered
  // Need to handle aspect ratio based on rotation
  if (editorRotation.value % 180 !== 0) {
      // If rotated 90 or 270, we might need to adjust scaling/fitting logic
      // For simple editor, we just draw the image centered
  }
  
  ctx.drawImage(
    img, 
    -canvas.width / 2, 
    -canvas.height / 2, 
    canvas.width, 
    canvas.height
  )
  
  ctx.restore()
}

const rotateEditor = (deg) => {
  editorRotation.value = (editorRotation.value + deg) % 360
  drawEditor()
}

const saveEditor = () => {
  if (!editorCanvas.value) return
  
  editorCanvas.value.toBlob((blob) => {
    if (blob) {
      // Create a new File object
      const newFile = new File([blob], fileName.value || 'edited-image.png', { type: 'image/png' })
      
      processFile(newFile)
      closeEditor()
    }
  }, 'image/png')
}

const bgHexMap = {
  'Cyber Security': '#ef4444',
  'Pemrograman': '#3b82f6',
  'Teknologi': '#06b6d4',
  'Sistem Digital': '#a855f7',
  default: '#6b7280'
}
const hexToRgb = (hex) => {
  const h = hex.replace('#', '')
  const bigint = parseInt(h.length === 3 ? h.split('').map(x => x + x).join('') : h, 16)
  const r = (bigint >> 16) & 255
  const g = (bigint >> 8) & 255
  const b = bigint & 255
  return [r, g, b]
}
const relLum = ([r, g, b]) => {
  const srgb = [r, g, b].map(v => v / 255).map(v => v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4))
  return 0.2126 * srgb[0] + 0.7152 * srgb[1] + 0.0722 * srgb[2]
}
const contrast = (bgHex, txtHex) => {
  const L1 = relLum(hexToRgb(bgHex))
  const L2 = relLum(hexToRgb(txtHex))
  const lighter = Math.max(L1, L2)
  const darker = Math.min(L1, L2)
  return (lighter + 0.05) / (darker + 0.05)
}
const previewBgClass = computed(() => {
  const hex = bgHexMap[category.value] || bgHexMap.default
  if (hex === bgHexMap['Cyber Security']) return 'bg-red-500'
  if (hex === bgHexMap['Pemrograman']) return 'bg-blue-500'
  if (hex === bgHexMap['Teknologi']) return 'bg-cyan-500'
  if (hex === bgHexMap['Sistem Digital']) return 'bg-purple-500'
  return 'bg-gray-500'
})
const previewTextClass = computed(() => {
  const hex = bgHexMap[category.value] || bgHexMap.default
  return contrast(hex, '#ffffff') >= 4.5 ? 'text-white' : 'text-black'
})
const previewClass = computed(() => `${previewBgClass.value} ${previewTextClass.value}`)
const previewContrast = computed(() => {
  const hex = bgHexMap[category.value] || bgHexMap.default
  const txtHex = previewTextClass.value.includes('white') ? '#ffffff' : '#000000'
  return contrast(hex, txtHex)
})

const submit = async () => {
  error.value = ''
  validationErrors.value = {}
  
  // Validation
  if (content.value.length < 50) {
    error.value = 'Konten artikel minimal 50 karakter'
    return
  }
  
  loading.value = true
  
  try {
    let payload;
    const config = {};

    if (image_file.value) {
      // Use FormData for file upload
      payload = new FormData();
      payload.append('title', title.value);
      payload.append('content', content.value);
      payload.append('category', category.value);
      payload.append('image_file', image_file.value);
      
      // If editing, we need to spoof PUT method
      if (isEdit.value) {
        payload.append('_method', 'PUT');
      }
      
      config.headers = {
        'Content-Type': 'multipart/form-data'
      };
    } else {
      // Use JSON for URL or no image change
      payload = { 
        title: title.value, 
        content: content.value, 
        category: category.value, 
        image_url: image_url.value 
      };
    }
    
    if (isEdit.value) {
      // If using FormData (with _method=PUT), we still POST to the update endpoint
      if (payload instanceof FormData) {
        await axios.post(`/articles/${id}`, payload, config);
      } else {
        await axios.put(`/articles/${id}`, payload);
      }
    } else {
      await axios.post(`/articles`, payload, config);
    }
    
    console.log('Artikel berhasil diterbitkan/diperbarui');
    // Clear draft after successful submission
    if (!isEdit.value) {
      localStorage.removeItem('article_draft')
    }
    showSuccessModal.value = true;
  } catch (e) {
    console.error('Error submitting article:', e);
    if (e.response?.status === 422) {
      validationErrors.value = e.response.data.errors
      error.value = 'Gagal menyimpan artikel. Mohon periksa kembali input Anda.'
    } else {
      error.value = e.response?.data?.message ?? 'Gagal menyimpan artikel. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}
</script>
