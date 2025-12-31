<template>
  <div class="animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div class="animate-slide-in-left">
        <h1 class="text-4xl font-bold tracking-tight gradient-text mb-2">Artikel & Analisis</h1>
        <p class="text-secondary">Wawasan terkini seputar Cyber Security, Pemrograman, dan Teknologi Digital.</p>
      </div>
      <div class="flex items-center gap-3 animate-slide-in-right">
        <select 
          v-model="filter" 
          class="px-4 py-2.5 rounded-xl border-2 border-cyan-200 dark:border-cyan-800 input-theme text-sm font-medium focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 dark:focus:ring-cyan-800 outline-none transition-all cursor-pointer appearance-none pr-10 relative"
          style="background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e&quot;); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em;"
        >
          <option value="" class="bg-card text-primary">🎯 Semua Topik</option>
          <option class="bg-card text-primary">Cyber Security</option>
          <option class="bg-card text-primary">Pemrograman</option>
          <option class="bg-card text-primary">Teknologi</option>
          <option class="bg-card text-primary">Sistem Digital</option>
        </select>
        <router-link v-if="isAuth" to="/articles/new" class="inline-flex items-center gap-2 home-cta-button">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13H13v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
          Tambah Artikel
        </router-link>
      </div>
    </div>

    <!-- Hero Section with Modern Design -->
    <section v-if="hero" class="relative mb-10 rounded-3xl overflow-hidden shadow-2xl group animate-fade-in">
      <div class="relative min-h-[450px]">
        <!-- Image with Loading State -->
        <div v-if="!imageLoaded[hero.id]" class="absolute inset-0 skeleton"></div>
        <img 
          :src="hero.image_url || 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=1200'" 
          :alt="hero.title"
          @load="imageLoaded[hero.id] = true"
          @error="handleImageError"
          class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
        />
        <div :class="['absolute inset-0 bg-gradient-to-t', overlayClass]"></div>
        
        <!-- Content -->
        <div class="relative min-h-[450px] flex items-end">
          <div class="p-8 sm:p-12 w-full">
            <div class="flex items-center gap-3 mb-4">
              <span :class="['px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider', labelClass(hero.category)]">
                {{ hero.category }}
              </span>
              <span class="text-white/80 text-sm">{{ formatDate(hero.created_at) }}</span>
            </div>
            <h2 class="font-bold text-4xl sm:text-5xl leading-tight mb-4 text-white drop-shadow-lg">{{ hero.title }}</h2>
            <p class="text-white/90 text-lg mb-6 line-clamp-2 max-w-3xl">{{ hero.content }}</p>
            <div class="flex items-center gap-4">
              <button @click="openDetail(hero)" class="inline-flex items-center gap-2 btn-gradient px-6 py-3 rounded-xl font-semibold shadow-xl">
                Baca Selengkapnya
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
              </button>
              <div class="flex items-center gap-2 text-white/80">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span class="text-sm font-medium">{{ hero.user?.name }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-6">
        <!-- Articles Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <article
            v-for="(a, index) in filteredArticles"
            :key="a.id"
            :style="{ animationDelay: `${index * 0.1}s` }"
            class="card-hover card-theme rounded-2xl shadow-lg overflow-hidden group animate-fade-in"
          >
            <!-- Image Container -->
            <div class="relative overflow-hidden aspect-[16/10]">
              <div v-if="!imageLoaded[a.id]" class="absolute inset-0 skeleton"></div>
              <img 
                :src="a.image_url || `https://images.unsplash.com/photo-${1550751827 + index}?w=600`" 
                :alt="a.title"
                @load="imageLoaded[a.id] = true"
                @error="handleImageError"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                loading="lazy" 
              />
              <div :class="['absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300', cardOverlayClass]"></div>
              <span :class="['absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg', labelClass(a.category)]">
                {{ a.category }}
              </span>
            </div>

            <!-- Content -->
            <div class="p-5">
              <h2 class="text-xl font-bold mb-2 line-clamp-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                <span :class="isSearching ? 'text-[#000000]' : ''">{{ a.title }}</span>
              </h2>
              
              <div class="flex items-center gap-2 mb-3 text-sm text-muted">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <span>{{ a.user?.name }}</span>
                <span class="text-muted">•</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
                <span>{{ formatDate(a.created_at) }}</span>
              </div>

              <p :class="['text-sm line-clamp-3 mb-4', isSearching ? 'text-[#000000]' : 'text-secondary']">{{ a.content }}</p>

              <!-- Action Buttons -->
              <div class="flex items-center gap-3 pt-3 border-t border-theme">
                <button @click="openDetail(a)" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white rounded-lg font-medium transition-all">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                  </svg>
                  Baca
                </button>
                <router-link v-if="isAuth && (currentUserId === a.user_id || isAdmin)" :to="`/articles/${a.id}/edit`" class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/>
                  </svg>
                </router-link>
                <button v-if="isSuperAdmin" @click="remove(a.id)" class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 19c0 1.1.9 2 2 2h8a2 2 0 002-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                  </svg>
                </button>
              </div>
            </div>
          </article>
        </div>

        <!-- Video/Media Section with Modern Design -->
        <section class="mt-10">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-2xl font-bold gradient-text">Video & Media</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Konten edukasi pilihan</p>
            </div>
            <div class="flex gap-2">
              <button v-if="isAdmin" @click="showUploadModal = true" class="inline-flex items-center gap-2 home-cta-button">
                 <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z"/></svg>
                 Tambah Video
              </button>
              <template v-if="videos.length > 0">
                <button @click="scrollLeft" class="p-3 rounded-xl card-dark-theme border-2 border-cyan-200 dark:border-cyan-800 hover:border-cyan-500 transition-all shadow-md hover:shadow-lg" aria-label="Prev">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                </button>
                <button @click="scrollRight" class="p-3 rounded-xl card-dark-theme border-2 border-cyan-200 dark:border-cyan-800 hover:border-cyan-500 transition-all shadow-md hover:shadow-lg" aria-label="Next">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                </button>
              </template>
            </div>
          </div>
          
          <div v-if="videos.length === 0" class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <div class="w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500">
               <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4zM14 13h-3v3H9v-3H6v-2h3V8h2v3h3v2z"/></svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada video</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">Video edukasi dan tutorial akan muncul di sini.</p>
          </div>

          <div v-else ref="carousel" class="flex overflow-x-auto gap-4 scroll-smooth snap-x snap-mandatory pb-4 -mx-2 px-2">
          <div v-for="v in videos" :key="v.id" class="min-w-[280px] snap-start card-dark-theme rounded-2xl shadow-lg hover:shadow-2xl transition-all overflow-hidden group">
              <div class="relative overflow-hidden cursor-pointer" @click="openPlayer(v)">
                <img :src="v.thumbnail_path || 'https://via.placeholder.com/640x360?text=Video'" :alt="v.title" class="w-full h-40 object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" />
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <div class="w-14 h-14 rounded-full bg-white/90 flex items-center justify-center transform group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-cyan-600 ml-1" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M8 5v14l11-7z"/>
                    </svg>
                  </div>
                </div>
                <!-- Delete Button for Admin -->
                <button v-if="isAdmin" @click.stop="confirmDeleteVideo(v)" class="absolute top-2 right-2 p-1.5 bg-red-500/80 hover:bg-red-600 rounded-lg text-white opacity-0 group-hover:opacity-100 transition-all backdrop-blur-sm z-10" title="Hapus Video">
                   <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8a2 2 0 002-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                </button>
                <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/80 rounded text-white text-xs font-semibold backdrop-blur-sm">
                  {{ v.duration || '00:00' }}
                </div>
              </div>
              <div class="p-4">
                <div class="text-sm font-bold mb-2 line-clamp-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">{{ v.title }}</div>
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                  </svg>
                  <span>{{ v.views || 0 }} views</span>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Modern Sidebar -->
      <aside class="space-y-6">
        <!-- Popular Articles -->
        <div class="card-dark-theme rounded-2xl shadow-lg p-6 border border-cyan-100 dark:border-cyan-900">
          <div class="flex items-center gap-2 mb-1">
            <svg class="w-6 h-6 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
              <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
            </svg>
            <h4 class="text-xl font-bold gradient-text">10 Terpopuler Minggu Ini</h4>
          </div>
          <p class="text-xs text-muted mb-4 flex items-center gap-1">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ weekRange }}
          </p>
          <ul class="space-y-4">
            <li v-for="(p, index) in popular" :key="p.id" class="group">
              <a @click.prevent="openDetail(p)" href="#" class="block">
                <div class="flex gap-3">
                  <div class="relative flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-gradient-primary flex items-center justify-center text-white font-bold text-sm">
                      {{ index + 1 }}
                    </div>
                    <span v-if="index === 0" class="absolute -top-3 -right-3 bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold shadow-sm animate-pulse">POPULER</span>
                  </div>
                  <div class="flex-1">
                    <div class="text-sm font-semibold group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2 mb-1">
                      {{ p.title }}
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                      </svg>
                      {{ p.weekly_views_count || 0 }} views
                    </div>
                  </div>
                </div>
              </a>
            </li>
          </ul>
        </div>

        <!-- Sponsor Ad -->
        <div class="rounded-2xl shadow-lg overflow-hidden relative group">
          <!-- Background Image -->
          <div class="absolute inset-0">
             <img 
               src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600" 
               alt="Ad Background" 
               class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
             />
             <div class="absolute inset-0 bg-gradient-to-br from-cyan-900/90 to-blue-900/90"></div>
          </div>
          
          <!-- Content -->
          <div class="relative p-8 text-white text-center">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30">
              <svg class="w-6 h-6 text-cyan-300" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
              </svg>
            </div>
            <h4 class="text-xl font-bold mb-2">TeknoNalar Pro</h4>
            <p class="text-sm text-cyan-100 mb-6">Akses konten eksklusif, tutorial mendalam, dan komunitas premium.</p>
            <a href="#" @click.prevent="openProModal" class="inline-block w-full py-3 px-4 rounded-xl bg-white text-cyan-900 font-bold hover:bg-cyan-50 transition-colors shadow-lg">
              Gabung Sekarang
            </a>
            <p class="text-xs text-white/60 mt-4">Mulai dari Rp 49.000/bulan</p>
          </div>
        </div>

        <!-- Newsletter -->
        <div class="card-dark-theme rounded-2xl shadow-lg p-6 border border-cyan-100 dark:border-cyan-900">
          <div class="flex items-center gap-2 mb-3">
            <svg class="w-6 h-6 text-cyan-600" viewBox="0 0 24 24" fill="currentColor">
              <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <h4 class="text-lg font-bold gradient-text">Newsletter</h4>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Dapatkan tips teknologi dan keamanan terbaru langsung ke email Anda.</p>
          <div class="space-y-3">
            <div v-if="authState.isAuth" class="text-sm text-cyan-600 dark:text-cyan-400 font-medium mb-2">
              Masuk sebagai: {{ authState.user?.email }}
            </div>
            <button 
              @click="subscribe" 
              class="w-full btn-gradient text-white px-4 py-2.5 rounded-xl font-semibold shadow-lg hover:shadow-cyan-500/30 transition-all active:scale-95 flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
              Langganan Sekarang
            </button>
            <p v-if="!authState.isAuth" class="text-xs text-center text-gray-500 dark:text-gray-400 mt-2">
              Harap login untuk berlangganan
            </p>
          </div>
        </div>
      </aside>
    </div>

    <!-- Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Artikel"
      message="Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan."
      confirm-text="Ya, Hapus"
      cancel-text="Batal"
      type="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteModal = false"
    />

    <!-- Video Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteVideoModal"
      title="Hapus Video"
      message="Apakah Anda yakin ingin menghapus video ini? Video akan dipindahkan ke sampah dan dapat dipulihkan jika diperlukan."
      confirm-text="Ya, Hapus Video"
      cancel-text="Batal"
      type="danger"
      @confirm="deleteVideo"
      @cancel="showDeleteVideoModal = false"
    />

    <!-- Upload Modal -->
    <Transition name="modal">
    <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="closeUploadModal">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800 sticky top-0 z-10">
          <h3 class="text-xl font-bold gradient-text">Upload Video Baru</h3>
          <button @click="closeUploadModal" class="group p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-500" title="Tutup">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-red-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar">
          <!-- Title Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Video <span class="text-red-500">*</span></label>
            <input 
              v-model="uploadForm.title" 
              type="text" 
              :class="['w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-cyan-500 outline-none transition-all', errors.title ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 dark:border-gray-600']" 
              placeholder="Masukkan judul video"
            >
            <p v-if="errors.title" class="text-xs text-red-500 mt-1">{{ errors.title }}</p>
          </div>

          <!-- Description Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
            <textarea 
              v-model="uploadForm.description" 
              rows="3" 
              class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-cyan-500 outline-none transition-all" 
              placeholder="Deskripsi singkat video"
            ></textarea>
          </div>

          <!-- Video File Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File Video <span class="text-red-500">*</span></label>
            <div 
              :class="['relative border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer', errors.file ? 'border-red-400 hover:border-red-500 bg-red-50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-600 hover:border-cyan-500']" 
              @click="$refs.uploadFile.click()"
            >
              <input ref="uploadFile" type="file" class="hidden" accept="video/*" @change="handleFileChange">
              <div v-if="!uploadForm.file">
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                  <svg class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Klik untuk upload video</p>
                <p class="text-xs text-gray-500 mt-1">MP4, MOV, OGG (Max 50MB)</p>
              </div>
              <div v-else class="flex flex-col items-center gap-2">
                <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center text-cyan-600">
                   <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8 8z"/></svg>
                </div>
                <p class="text-sm font-medium text-cyan-600 truncate max-w-xs">{{ uploadForm.file.name }}</p>
                <button @click.stop="uploadForm.file = null; $refs.uploadFile.value = ''" class="text-xs text-red-500 hover:underline">Ganti File</button>
              </div>
            </div>
            <p v-if="errors.file" class="text-xs text-red-500 mt-1">{{ errors.file }}</p>
          </div>

          <!-- Thumbnail Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thumbnail (Opsional)</label>
            <div class="flex flex-col sm:flex-row gap-4 items-start">
              <div class="flex-1 w-full relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center hover:border-cyan-500 transition-colors cursor-pointer" @click="$refs.uploadThumbnail.click()">
                <input ref="uploadThumbnail" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" @change="handleThumbnailChange">
                <div v-if="!uploadForm.thumbnail">
                  <svg class="w-6 h-6 text-gray-400 mx-auto mb-2" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                  <p class="text-xs text-gray-500">Upload thumbnail (16:9)</p>
                </div>
                <div v-else class="text-cyan-600 font-medium text-sm truncate px-2">
                  {{ uploadForm.thumbnail.name }}
                </div>
              </div>
              <div v-if="thumbnailPreview" class="w-full sm:w-32 aspect-video rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-black shadow-sm">
                <img :src="thumbnailPreview" class="w-full h-full object-cover" alt="Preview">
              </div>
            </div>
          </div>

          <!-- Progress Bar -->
          <div v-if="isUploading" class="space-y-1">
            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
              <span>Mengupload...</span>
              <span>{{ uploadProgress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700 overflow-hidden">
              <div class="bg-cyan-500 h-full rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 bg-gray-50 dark:bg-gray-800/50">
          <button @click="closeUploadModal" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors focus:ring-2 focus:ring-gray-300">
            Batal
          </button>
          <button 
            @click="submitUpload" 
            :disabled="isUploading" 
            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white text-sm font-bold shadow-lg hover:shadow-cyan-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg v-if="isUploading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isUploading ? 'Memproses...' : 'Upload Video' }}
          </button>
        </div>
      </div>
    </div>
    </Transition>

    <!-- Video Player Modal -->
    <div v-if="showPlayerModal && currentVideo" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md animate-fade-in" @click.self="closePlayer">
      <div class="relative w-full max-w-5xl mx-4 animate-scale-in">
        <button @click="closePlayer" class="absolute -top-12 right-0 text-white/70 hover:text-white transition-colors z-50">
          <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
        <div class="relative pt-[56.25%] bg-black rounded-2xl overflow-hidden shadow-2xl border border-white/10">
          <video 
            :src="currentVideo.file_path" 
            controls 
            autoplay 
            class="absolute inset-0 w-full h-full object-contain"
            controlsList="nodownload"
          >
            Browser Anda tidak mendukung tag video.
          </video>
        </div>
        <div class="mt-4 text-white">
          <h3 class="text-2xl font-bold mb-2">{{ currentVideo.title }}</h3>
          <p class="text-white/70">{{ currentVideo.description }}</p>
          <div class="flex items-center gap-4 mt-2 text-sm text-white/50">
             <span>{{ currentVideo.views }} views</span>
             <span>•</span>
             <span>{{ formatDate(currentVideo.created_at) }}</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Opsi Tampilan -->
    <div class="fixed bottom-6 right-6 z-40">
      <div class="relative">
        <div v-if="optionsOpen" class="absolute bottom-14 right-0 w-72 p-4 rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-cyan-100 dark:border-cyan-800 space-y-3">
          <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pengaturan Tampilan</div>
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-600 dark:text-gray-400">Skema Warna</span>
            <select v-model="overlayScheme" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
              <option value="auto">Auto</option>
              <option value="high">High Contrast</option>
              <option value="light">Light Overlay</option>
              <option value="dark">Dark Overlay</option>
            </select>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-600 dark:text-gray-400">Warna Teks Label</span>
            <input v-model="manualTextColor" type="color" class="w-10 h-8 p-0 border rounded" />
          </div>
          <div class="h-px bg-gray-100 dark:bg-gray-800 my-2"></div>
          <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Warna Tombol Tambah Artikel</div>
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-600 dark:text-gray-400">Latar</span>
            <input v-model="homeBtnBg" type="color" class="w-10 h-8 p-0 border rounded" />
          </div>
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-600 dark:text-gray-400">Teks</span>
            <input v-model="homeBtnText" type="color" class="w-10 h-8 p-0 border rounded" />
          </div>
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-600 dark:text-gray-400">Border</span>
            <input v-model="homeBtnBorder" type="color" class="w-10 h-8 p-0 border rounded" />
          </div>
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-600 dark:text-gray-400">Reset</span>
            <button @click="resetOptions" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">Default</button>
          </div>
        </div>
      </div>
    </div>
    <!-- TeknoNalar Pro Modal -->
    <Transition name="modal">
      <div v-if="showProModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="closeProModal">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-5 transform transition-all text-center">
          <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
             <svg class="w-8 h-8 text-cyan-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Fitur ini masih dalam pengembangan</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Kami sedang bekerja keras untuk menghadirkan konten premium terbaik untuk Anda. Nantikan update selanjutnya!</p>
          <button @click="closeProModal" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold hover:shadow-lg hover:shadow-cyan-500/30 transition-all">
            Tutup
          </button>
        </div>
      </div>
    </Transition>
    <!-- Newsletter Success Modal -->
    <NewsletterSuccessModal 
      :show="showNewsletterModal" 
      :name="subscriberName" 
      @close="showNewsletterModal = false" 
    />
  </div>
</template>

<script setup>
import ConfirmModal from './ConfirmModal.vue'
import NewsletterSuccessModal from './NewsletterSuccessModal.vue'
import axios from 'axios'
import { ref, onMounted, computed, watch, reactive, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSEO } from '../composables/useSEO'
import { authState } from '../authState'

// SEO Implementation
useSEO(
  ref('Beranda'), 
  ref('TeknoNalar - Pusat edukasi teknologi, tutorial pemrograman, dan analisis keamanan siber terkini.'), 
  ref('/images/logo.png')
)

const articles = ref([])
const imageLoaded = reactive({})
const hero = computed(() => (filteredArticles.value.length ? filteredArticles.value[0] : null))
const popular = ref([])
const weekRange = computed(() => {
  const now = new Date()
  const currentDay = now.getDay()
  // Adjust to get Monday (1) to Sunday (7). JS getDay() is Sun(0)-Sat(6)
  const day = currentDay === 0 ? 7 : currentDay
  
  const monday = new Date(now)
  monday.setDate(now.getDate() - day + 1)
  
  const sunday = new Date(now)
  sunday.setDate(now.getDate() - day + 7)
  
  const options = { day: 'numeric', month: 'short' }
  return `${monday.toLocaleDateString('id-ID', options)} - ${sunday.toLocaleDateString('id-ID', options)}`
})
const email = ref('')
const name = ref('')
const showNewsletterModal = ref(false)
const subscriberName = ref('')
const videos = ref([])
const showUploadModal = ref(false)
const showPlayerModal = ref(false)
const showProModal = ref(false)
const currentVideo = ref(null)
const uploadForm = reactive({
  title: '',
  description: '',
  file: null,
  thumbnail: null
})
const isUploading = ref(false)
const uploadProgress = ref(0)
const uploadFile = ref(null)
const uploadThumbnail = ref(null)
const thumbnailPreview = ref(null)
const errors = reactive({})

const closeUploadModal = () => {
  if (isUploading.value) return
  showUploadModal.value = false
  setTimeout(() => {
    uploadForm.title = ''
    uploadForm.description = ''
    uploadForm.file = null
    uploadForm.thumbnail = null
    thumbnailPreview.value = null
    if(uploadFile.value) uploadFile.value.value = ''
    if(uploadThumbnail.value) uploadThumbnail.value.value = ''
    Object.keys(errors).forEach(key => delete errors[key])
  }, 300)
}

const openProModal = () => showProModal.value = true
const closeProModal = () => showProModal.value = false

const subscribe = async () => {
  // Check if user is authenticated
  if (!authState.isAuth || !authState.user) {
    // Alert or redirect to login
    if (confirm('Anda perlu login untuk berlangganan newsletter. Apakah Anda ingin login sekarang?')) {
      router.push('/login')
    }
    return
  }

  try {
    const userEmail = authState.user.email
    const userName = authState.user.name || ''

    await axios.post('/subscribe', { email: userEmail, name: userName })
    
    subscriberName.value = userName || userEmail.split('@')[0]
    showNewsletterModal.value = true
    
    // Clear input refs if they were used (though now we use auth data)
    email.value = ''
    name.value = ''
    
    console.log(`Newsletter subscription successful for user: ${userEmail}`)
  } catch (e) {
    if (e.response && e.response.status === 401) {
      alert('Sesi Anda telah berakhir. Silakan login kembali.')
      router.push('/login')
    } else {
      alert(e.response?.data?.message || 'Gagal berlangganan. Silakan coba lagi.')
    }
  }
}

const handleEscKey = (e) => {
  if (e.key === 'Escape') {
    if (showUploadModal.value) closeUploadModal()
    if (showPlayerModal.value) closePlayer()
    if (showProModal.value) closeProModal()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleEscKey)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleEscKey)
})

const fetchVideos = async () => {
    try {
      const { data } = await axios.get('/videos')
      videos.value = data
    } catch (e) {
      console.error('Failed to fetch videos', e)
    }
  }

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
      uploadForm.file = file
  }
}

const handleThumbnailChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  // Validate type
  const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg']
  if (!allowedTypes.includes(file.type)) {
    alert('Format file tidak didukung. Harap upload gambar JPEG atau PNG.')
    e.target.value = ''
    return
  }

  // Validate size (10MB)
  if (file.size > 10 * 1024 * 1024) {
    alert('Ukuran file melebihi batas 10MB.')
    e.target.value = ''
    return
  }

  uploadForm.thumbnail = file
  thumbnailPreview.value = URL.createObjectURL(file)
}

const submitUpload = async () => {
  // Reset errors
  Object.keys(errors).forEach(key => delete errors[key])
  
  // Validate
  let isValid = true
  if (!uploadForm.title.trim()) {
    errors.title = 'Judul video wajib diisi'
    isValid = false
  }
  if (!uploadForm.file) {
    errors.file = 'File video wajib dipilih'
    isValid = false
  }
  
  if (!isValid) return
  
  const formData = new FormData()
  formData.append('title', uploadForm.title)
  formData.append('description', uploadForm.description || '')
  formData.append('video_file', uploadForm.file)
  if (uploadForm.thumbnail) {
    formData.append('thumbnail_file', uploadForm.thumbnail)
  }
  formData.append('duration', '00:00') 

  isUploading.value = true
  uploadProgress.value = 0
  
  try {
    await axios.post('/admin/videos', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (progressEvent) => {
         const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
         uploadProgress.value = percentCompleted
      }
    })
    closeUploadModal()
    
    fetchVideos()
    alert('Video berhasil diupload!')
  } catch (e) {
    console.error('Upload Error:', e)
    let errorMsg = 'Upload gagal.'
    if (e.response && e.response.data) {
        if (e.response.data.errors) {
            errorMsg += '\n' + Object.values(e.response.data.errors).flat().join('\n')
        } else if (e.response.data.message) {
            errorMsg += ' ' + e.response.data.message
        }
    } else {
        errorMsg += ' ' + e.message
    }
    alert(errorMsg)
  } finally {
    isUploading.value = false
  }
}

const openPlayer = (video) => {
  currentVideo.value = video
  showPlayerModal.value = true
  axios.post(`/videos/${video.id}/view`)
}

const closePlayer = () => {
  showPlayerModal.value = false
  setTimeout(() => {
    currentVideo.value = null
  }, 300)
}
const filter = ref('')
const isAuth = computed(() => (typeof authState !== 'undefined' ? authState.isAuth : !!localStorage.getItem('token')))
const isSuperAdmin = computed(() => (typeof authState !== 'undefined' ? authState.isSuperAdmin : localStorage.getItem('is_super_admin') === 'true'))
const isAdmin = computed(() => (typeof authState !== 'undefined' ? authState.isAdmin : localStorage.getItem('is_admin') === 'true'))
const currentUserId = computed(() => (typeof authState !== 'undefined' && authState.user ? authState.user.id : (localStorage.getItem('user_id') ? Number(localStorage.getItem('user_id')) : null)))

const fetch = async () => {
  try {
    const [articlesRes, popularRes, videosRes] = await Promise.all([
      axios.get('/articles'),
      axios.get('/articles/popular'),
      axios.get('/videos')
    ])
    
    articles.value = articlesRes.data.data ?? articlesRes.data
    popular.value = popularRes.data
    videos.value = videosRes.data
  } catch (e) {
    console.error('Failed to fetch data', e)
  }
}
onMounted(fetch)

const searchTerm = computed(() => {
  try {
    return String(route.query.q ?? '').trim()
  } catch (e) {
    console.error('Search term parse error', e)
    return ''
  }
})

const filteredArticles = computed(() => {
  try {
    let base = Array.isArray(articles.value) ? articles.value : []
    const term = searchTerm.value.toLowerCase()
    if (term) {
      base = base.filter(a =>
        String(a.title ?? '').toLowerCase().includes(term) ||
        String(a.content ?? '').toLowerCase().includes(term)
      )
    }
    if (filter.value) {
      base = base.filter(a => a.category === filter.value)
    }
    return base
  } catch (err) {
    console.error('Search filtering error', err)
    return Array.isArray(articles.value) ? articles.value : []
  }
})

const route = useRoute()
const router = useRouter()
const isSearching = computed(() => !!route.query.q)

watch(() => route.query.q, (q, prev) => {
  try {
    const term = String(q ?? '').toLowerCase()
    const matches = (Array.isArray(articles.value) ? articles.value : []).filter(a =>
      String(a.title ?? '').toLowerCase().includes(term) ||
      String(a.content ?? '').toLowerCase().includes(term)
    ).length
    console.debug('Search term changed', { previous: prev ?? '', current: q ?? '', matches })
  } catch (e) {
    console.error('Search watch error', e)
  }
}, { flush: 'post' })

const optionsOpen = ref(false)
const overlayScheme = ref(localStorage.getItem('overlay_scheme') || 'auto')
const manualTextColor = ref(localStorage.getItem('label_text_color') || '')
watch(overlayScheme, v => localStorage.setItem('overlay_scheme', v))
watch(manualTextColor, v => localStorage.setItem('label_text_color', v))
const resetOptions = () => {
  overlayScheme.value = 'auto'
  manualTextColor.value = ''
  homeBtnBg.value = defaultHomeBtnBg
  homeBtnText.value = defaultHomeBtnText
  homeBtnBorder.value = defaultHomeBtnBorder
  applyHomeBtnColors()
}

const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)')
const darkClassEnabled = () => document.documentElement.classList.contains('dark')
const isDarkMode = ref(darkClassEnabled() || (prefersDark ? prefersDark.matches : false))
if (prefersDark) {
  prefersDark.addEventListener('change', e => { isDarkMode.value = e.matches || darkClassEnabled() })
}

const overlayClass = computed(() => {
  const scheme = overlayScheme.value
  if (scheme === 'high') return 'from-black/80 via-black/50 to-transparent'
  if (scheme === 'light') return 'from-black/40 via-black/20 to-transparent'
  if (scheme === 'dark') return 'from-gray-900 via-gray-900/60 to-transparent'
  return isDarkMode.value ? 'from-gray-900 via-gray-900/60 to-transparent' : 'from-black/40 via-black/20 to-transparent'
})
const cardOverlayClass = computed(() => {
  const scheme = overlayScheme.value
  if (scheme === 'high') return 'bg-gradient-to-t from-black/80 to-transparent'
  if (scheme === 'light') return 'bg-gradient-to-t from-black/40 to-transparent'
  if (scheme === 'dark') return 'bg-gradient-to-t from-gray-900/70 to-transparent'
  return isDarkMode.value ? 'bg-gradient-to-t from-black/70 to-transparent' : 'bg-gradient-to-t from-black/40 to-transparent'
})

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
const chooseText = (bgHex) => {
  if (manualTextColor.value) return manualTextColor.value
  const white = '#ffffff'
  const black = '#000000'
  return contrast(bgHex, white) >= 4.5 ? 'text-white' : 'text-black'
}
const bgHexMap = {
  'Cyber Security': '#ef4444',
  'Pemrograman': '#3b82f6',
  'Teknologi': '#06b6d4',
  'Sistem Digital': '#a855f7',
  default: '#6b7280'
}
const labelClass = (cat) => {
  const bg = bgHexMap[cat] || bgHexMap.default
  const txtClass = chooseText(bg)
  const base = bg === bgHexMap['Cyber Security'] ? 'bg-red-500' :
               bg === bgHexMap['Pemrograman'] ? 'bg-blue-500' :
               bg === bgHexMap['Teknologi'] ? 'bg-cyan-500' :
               bg === bgHexMap['Sistem Digital'] ? 'bg-purple-500' : 'bg-gray-500'
  return `${base} ${txtClass}`
}

const toHsl = (hex) => {
  const [r, g, b] = hexToRgb(hex).map(v => v / 255)
  const max = Math.max(r, g, b), min = Math.min(r, g, b)
  let h, s, l = (max + min) / 2
  if (max === min) { h = s = 0 } else {
    const d = max - min
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min)
    switch (max) {
      case r: h = (g - b) / d + (g < b ? 6 : 0); break
      case g: h = (b - r) / d + 2; break
      case b: h = (r - g) / d + 4; break
    }
    h = h / 6
  }
  return [Math.round(h * 360), Math.round(s * 100), Math.round(l * 100)]
}
const fromHsl = (h, s, l) => {
  s /= 100; l /= 100
  const c = (1 - Math.abs(2 * l - 1)) * s
  const x = c * (1 - Math.abs(((h / 60) % 2) - 1))
  const m = l - c/2
  let r=0,g=0,b=0
  if (0 <= h && h < 60) { r = c; g = x; b = 0 }
  else if (60 <= h && h < 120) { r = x; g = c; b = 0 }
  else if (120 <= h && h < 180) { r = 0; g = c; b = x }
  else if (180 <= h && h < 240) { r = 0; g = x; b = c }
  else if (240 <= h && h < 300) { r = x; g = 0; b = c }
  else { r = c; g = 0; b = x }
  const toHex = (v) => {
    const h = Math.round((v + m) * 255).toString(16).padStart(2, '0')
    return h
  }
  return `#${toHex(r)}${toHex(g)}${toHex(b)}`
}
const adjustLightness = (hex, delta) => {
  const [h, s, l] = toHsl(hex)
  const nl = Math.max(0, Math.min(100, l + delta))
  return fromHsl(h, s, nl)
}
const defaultHomeBtnBg = '#06B6D4'
const defaultHomeBtnText = '#ffffff'
const defaultHomeBtnBorder = '#06B6D4'
const homeBtnBg = ref(localStorage.getItem('home_btn_bg') || defaultHomeBtnBg)
const homeBtnText = ref(localStorage.getItem('home_btn_text') || defaultHomeBtnText)
const homeBtnBorder = ref(localStorage.getItem('home_btn_border') || defaultHomeBtnBorder)
const applyHomeBtnColors = () => {
  const root = document.documentElement
  root.style.setProperty('--home-btn-bg', homeBtnBg.value)
  root.style.setProperty('--home-btn-text', homeBtnText.value)
  root.style.setProperty('--home-btn-border', homeBtnBorder.value)
  root.style.setProperty('--home-btn-bg-hover', adjustLightness(homeBtnBg.value, -6))
  root.style.setProperty('--home-btn-text-hover', homeBtnText.value)
  root.style.setProperty('--home-btn-border-hover', homeBtnBorder.value)
  root.style.setProperty('--home-btn-bg-active', adjustLightness(homeBtnBg.value, -12))
  root.style.setProperty('--home-btn-text-active', homeBtnText.value)
  root.style.setProperty('--home-btn-border-active', homeBtnBorder.value)
}
watch([homeBtnBg, homeBtnText, homeBtnBorder], () => {
  localStorage.setItem('home_btn_bg', homeBtnBg.value)
  localStorage.setItem('home_btn_text', homeBtnText.value)
  localStorage.setItem('home_btn_border', homeBtnBorder.value)
  applyHomeBtnColors()
}, { immediate: true })

const formatDate = (iso) => {
  const date = new Date(iso)
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)
  
  if (diff < 60) return 'Baru saja'
  if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`
  if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`
  if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`
  
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const handleImageError = (e) => {
  // Fallback to a gradient placeholder if image fails to load
  e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="600" height="400"%3E%3Cdefs%3E%3ClinearGradient id="grad" x1="0%25" y1="0%25" x2="100%25" y2="100%25"%3E%3Cstop offset="0%25" style="stop-color:%2306B6D4;stop-opacity:1" /%3E%3Cstop offset="100%25" style="stop-color:%233B82F6;stop-opacity:1" /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width="600" height="400" fill="url(%23grad)" /%3E%3Ctext x="50%25" y="50%25" font-family="Arial" font-size="24" fill="white" text-anchor="middle" dominant-baseline="middle"%3ETeknoNalar%3C/text%3E%3C/svg%3E'
}

const carousel = ref(null)
const scrollLeft = () => carousel.value?.scrollBy({ left: -300, behavior: 'smooth' })
const scrollRight = () => carousel.value?.scrollBy({ left: 300, behavior: 'smooth' })

const openDetail = (a) => {
  // Navigate to article detail page
  router.push(`/articles/${a.id}`)
}



const showDeleteModal = ref(false)
  const showDeleteVideoModal = ref(false)
  const articleToDelete = ref(null)
  const videoToDelete = ref(null)

  const confirmDeleteVideo = (v) => {
    videoToDelete.value = v
    showDeleteVideoModal.value = true
  }

  const deleteVideo = async () => {
    if (!videoToDelete.value) return
    try {
      await axios.delete(`/admin/videos/${videoToDelete.value.id}`)
      videos.value = videos.value.filter(v => v.id !== videoToDelete.value.id)
      showDeleteVideoModal.value = false
      videoToDelete.value = null
    } catch (e) {
      alert('Gagal menghapus video: ' + (e.response?.data?.message || e.message))
    }
  }

  const remove = (id) => {
    articleToDelete.value = id
    showDeleteModal.value = true
  }

const confirmDelete = async () => {
  try {
    await axios.delete(`/articles/${articleToDelete.value}`)
    articles.value = articles.value.filter(x => x.id !== articleToDelete.value)
    showDeleteModal.value = false
    articleToDelete.value = null
    // Success notification could be added here
  } catch (error) {
    console.error('Delete error:', error)
    showDeleteModal.value = false
    articleToDelete.value = null
  }
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

.modal-enter-active .transform,
.modal-leave-active .transform {
  transition: all 0.3s ease;
}

.modal-enter-from .transform,
.modal-leave-to .transform {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}
</style>
