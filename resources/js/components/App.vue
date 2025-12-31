<template>
  <div class="min-h-screen bg-body text-primary transition-colors duration-300 flex flex-col">
    <!-- Navbar Modern dengan Unified Color -->
    <nav class="sticky top-0 z-50 navbar-unified text-primary shadow-lg">
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <!-- Logo & Brand -->
          <router-link to="/" class="flex items-center gap-3 group">
            <img src="/Image/logo.png" alt="Logo Tekno Nalar" width="40" class="w-10 h-auto object-contain" />
            <div class="flex flex-col">
              <div class="font-bold text-xl tracking-tight leading-tight">TeknoNalar</div>
              <div class="text-xs text-muted leading-tight">Teknologi & Cyber Security</div>
            </div>
          </router-link>

          <!-- Right Actions -->
          <div class="flex items-center space-x-3">
            <!-- Search Bar -->
            <div class="hidden md:block relative group">
              <input 
                v-model="q" 
                @keyup.enter="search" 
                type="search" 
                placeholder="Cari artikel..." 
                class="input-theme w-64 pl-10 pr-4 py-2.5 rounded-xl outline-none transition-all"
              />
              <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted group-focus-within:text-[var(--color-primary)] transition-colors" viewBox="0 0 24 24" fill="currentColor">
                <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
              </svg>
            </div>
            
            <!-- Notification Dropdown -->
            <NotificationDropdown v-if="isAuth" />

            <!-- Theme Toggle -->
            <button 
              @click="toggleTheme" 
              class="p-2.5 rounded-xl border border-theme hover:border-[var(--color-primary)] text-primary transition-all group"
              aria-label="Toggle theme"
            >
              <svg v-if="!isDark" class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 18a6 6 0 110-12 6 6 0 010 12zm0 4a1 1 0 001-1v-1a1 1 0 10-2 0v1a1 1 0 001 1zm0-19a1 1 0 001-1V1a1 1 0 10-2 0v1a1 1 0 001 1zM4.22 5.64a1 1 0 10-1.41-1.41l-.7.7a1 1 0 101.41 1.41l.7-.7zm16.69-.71a1 1 0 00-1.41 0l-.7.7a1 1 0 101.41 1.41l.7-.7a1 1 0 000-1.41zM4 12a1 1 0 01-1-1H2a1 1 0 100 2h1a1 1 0 011-1zm18 0a1 1 0 011-1h1a1 1 0 110 2h-1a1 1 0 01-1-1zM4.22 18.36l-.7.7a1 1 0 101.41 1.41l.7-.7a1 1 0 10-1.41-1.41zm15.36.7l.7.7a1 1 0 001.41-1.41l-.7-.7a1 1 0 10-1.41 1.41z"/>
              </svg>
              <svg v-else class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
              </svg>
            </button>

            <!-- Auth Buttons -->
            <router-link 
              v-if="!isAuth" 
              to="/login" 
              class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 bg-card hover:bg-[var(--color-primary)] hover:text-white text-primary rounded-xl border border-theme transition-all"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
              </svg>
              Login
            </router-link>
            <button 
              v-else 
              @click="logout" 
              class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 bg-red-500/10 hover:bg-red-500 hover:text-white text-red-500 rounded-xl border border-red-500/30 transition-all"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
              </svg>
              Logout
            </button>

            <!-- Sidebar Toggle (All Screens) -->
            <button 
              @click="toggleSidebar"
              class="inline-flex p-2.5 rounded-xl border border-theme hover:border-[var(--color-primary)] text-primary transition-all ml-2"
              aria-label="Menu"
              aria-haspopup="true"
              :aria-expanded="sidebarOpen"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu (Removed) -->
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      <router-view />
    </main>

    <!-- Modern Footer -->
    <footer class="w-full mt-auto bg-[var(--footer-bg)] border-t border-[var(--border-color)] shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] transition-colors duration-300">
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-6">
          <div>
            <div class="flex items-center gap-2 mb-3">
              <h3 class="font-bold text-lg gradient-text">TeknoNalar</h3>
            </div>
            <p class="text-sm text-[var(--text-muted)]">Platform personal branding yang mengedepankan edukasi, analisis, dan penulisan di bidang teknologi, khususnya cyber security, pemrograman, dan sistem digital.</p>
          </div>
          <div>
            <h4 class="font-semibold mb-3 text-[var(--text-primary)]">Topik</h4>
            <ul class="space-y-2 text-sm text-[var(--text-muted)]">
              <li><a href="#" class="hover:text-[var(--color-primary)] transition-colors">Cyber Security</a></li>
              <li><a href="#" class="hover:text-[var(--color-primary)] transition-colors">Pemrograman</a></li>
              <li><a href="#" class="hover:text-[var(--color-primary)] transition-colors">Sistem Digital</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-3 text-[var(--text-primary)]">Ikuti Kami</h4>
            <div class="flex gap-3">
              <!-- Facebook -->
              <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 text-gray-500 hover:text-[#1877F2] hover:bg-[#1877F2]/10 dark:text-gray-400 dark:hover:text-white dark:hover:bg-[#1877F2]" aria-label="Facebook">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <!-- TikTok -->
              <a href="https://www.tiktok.com/@t3kno.nalar?is_from_webapp=1&sender_device=pc" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 text-gray-500 hover:text-[#000000] hover:bg-[#000000]/10 dark:text-gray-400 dark:hover:text-white dark:hover:bg-[#000000]" aria-label="TikTok">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.65-1.62-1.1-.01-.01-.02-.01-.03-.02v12.02c-.13 1.79-.82 3.65-2.09 4.98-1.29 1.34-3.15 2.06-4.98 2.08-1.92-.01-3.8-.82-5.07-2.18-1.35-1.47-1.9-3.66-1.4-5.63.51-1.93 2.03-3.66 3.92-4.23.83-.24 1.7-.35 2.56-.25v4.2a2.3 2.3 0 0 0-2.2 2.45c.06 1.17 1.05 2.11 2.22 2.13 1.15.06 2.15-.79 2.22-1.93V.02h-3.93z"/></svg>
              </a>
              <!-- Instagram -->
              <a href="https://www.instagram.com/tekno.nalar?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 text-gray-500 hover:text-[#E1306C] hover:bg-[#E1306C]/10 dark:text-gray-400 dark:hover:text-white dark:hover:bg-[#E1306C]" aria-label="Instagram">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
              </a>
              <!-- Discord -->
              <a href="https://discord.gg/ZWdqkPSby4" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 text-gray-500 hover:text-[#5865F2] hover:bg-[#5865F2]/10 dark:text-gray-400 dark:hover:text-white dark:hover:bg-[#5865F2]" aria-label="Discord">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037 13.48 13.48 0 0 0-.599 1.227 18.353 18.353 0 0 0-5.502 0 14.125 14.125 0 0 0-.608-1.227.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
              </a>
            </div>
          </div>
        </div>
        <div class="pt-6 border-t border-[var(--border-color)] text-center text-sm text-[var(--text-muted)]">
          © {{ new Date().getFullYear() }} TeknoNalar. Dibangun dengan ❤️ menggunakan Laravel + Vue + Tailwind CSS.
        </div>
      </div>
    </footer>

    <ConfirmationModal  
      :show="showLogoutModal"
      title="Logout"
      message="Apakah Anda yakin ingin keluar dari sesi ini?"
      @confirm="confirmLogout"
      @cancel="showLogoutModal = false"
    />

    <!-- Sidebar Drawer (Desktop/Responsive) -->
    <Transition name="slide-fade">
      <div v-if="sidebarOpen" class="fixed inset-0 z-[60] flex justify-end">
        <!-- Backdrop -->
        <div @click="sidebarOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        
        <!-- Drawer -->
        <div class="relative w-full sm:w-80 h-full bg-white dark:bg-gray-900 shadow-2xl p-6 flex flex-col border-l border-gray-200 dark:border-gray-800 transform transition-all duration-300">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-cyan-500 to-blue-500">Menu</h2>
                <button @click="sidebarOpen = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto space-y-2">
                <template v-if="loadingSidebar">
                    <div class="animate-pulse space-y-4">
                        <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
                        <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
                        <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
                    </div>
                </template>
                <template v-else>
                    <!-- Static Links -->
                    <router-link 
                        to="/" 
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-cyan-500 hover:text-white dark:hover:bg-cyan-600 text-gray-700 dark:text-gray-200 transition-all group"
                        active-class="bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-semibold"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span class="font-medium">Beranda</span>
                    </router-link>
                    <router-link 
                        to="/about" 
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-cyan-500 hover:text-white dark:hover:bg-cyan-600 text-gray-700 dark:text-gray-200 transition-all group"
                        active-class="bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-semibold"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                        <span class="font-medium">Tentang Kami</span>
                    </router-link>
                    <router-link 
                        to="/news" 
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-cyan-500 hover:text-white dark:hover:bg-cyan-600 text-gray-700 dark:text-gray-200 transition-all group"
                        active-class="bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-semibold"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4V6zm0 5h10v2H4v-2zm0 5h16v2H4v-2z"/></svg>
                        <span class="font-medium">Berita</span>
                    </router-link>
                    
                    <div v-if="sidebarItems.length > 0" class="h-px bg-gray-200 dark:bg-gray-700 my-2 mx-4"></div>

                    <!-- Dynamic Links -->
                    <router-link 
                        v-for="(item, index) in sidebarItems" 
                        :key="index"
                        :to="item.to"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-cyan-500 hover:text-white dark:hover:bg-cyan-600 text-gray-700 dark:text-gray-200 transition-all group"
                        active-class="bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-semibold"
                    >
                        <!-- Icon based on item.icon -->
                         <span v-if="item.icon === 'document'">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                         </span>
                         <span v-else-if="item.icon === 'verify'">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                         </span>
                         <span v-else-if="item.icon === 'users'">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                         </span>
                         <span v-else>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4V6zm0 5h10v2H4v-2zm0 5h16v2H4v-2z"/></svg>
                         </span>
                        
                        <span class="font-medium">{{ item.label }}</span>
                    </router-link>

                    <!-- Auth Links (Mobile/Sidebar) -->
                    <div class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-800">
                        <router-link 
                            v-if="!isAuth" 
                            to="/login" 
                            @click="sidebarOpen = false"
                            class="flex items-center justify-center w-full py-2.5 rounded-xl border border-theme hover:bg-[var(--color-primary)] hover:text-white text-gray-900 dark:text-white transition-all duration-300 gap-2"
                        >
                             <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                             </svg>
                             Login
                        </router-link>
                        <button 
                            v-else 
                            @click="logout" 
                            class="flex items-center justify-center w-full py-2.5 rounded-xl border border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white transition-all gap-2"
                        >
                             <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                             </svg>
                             Logout
                        </button>
                    </div>
                </template>
            </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: opacity 0.3s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
}

.slide-fade-enter-active .relative,
.slide-fade-leave-active .relative {
  transition: transform 0.3s ease-out;
}

.slide-fade-enter-from .relative,
.slide-fade-leave-to .relative {
  transform: translateX(100%);
}
</style>

<script setup>
import axios from 'axios'
import { computed, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ConfirmationModal from './ConfirmationModal.vue'
import NotificationDropdown from './NotificationDropdown.vue'
import { authState } from '../authState'

const isAuth = computed(() => authState.isAuth)
const isAdmin = computed(() => authState.isAdmin)
const isSuperAdmin = computed(() => authState.isSuperAdmin)
const isDark = ref(false)
const menuOpen = ref(false)
const q = ref('')
const router = useRouter()
const showLogoutModal = ref(false)
const sidebarOpen = ref(false)
const sidebarItems = ref([])
const loadingSidebar = ref(false)

const toggleSidebar = async () => {
  sidebarOpen.value = !sidebarOpen.value
  if (sidebarOpen.value && sidebarItems.value.length === 0 && isAuth.value) {
    await fetchSidebarMenu()
  }
}

const fetchSidebarMenu = async () => {
  loadingSidebar.value = true
  try {
    const { data } = await axios.get('/sidebar-menu')
    sidebarItems.value = data
  } catch (e) {
    console.error('Failed to fetch menu', e)
  } finally {
    loadingSidebar.value = false
  }
}

const applyTheme = () => {
  if ('theme' in localStorage) {
    isDark.value = localStorage.getItem('theme') === 'dark'
  } else {
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }
  updateDOM()
}

const updateDOM = () => {
  const dark = isDark.value
  document.documentElement.classList.toggle('dark', dark)
  document.body.classList.toggle('dark-mode', dark)
}

const toggleTheme = () => {
  isDark.value = !isDark.value
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
  updateDOM()
}

onMounted(() => {
  authState.checkAuth()
  applyTheme()
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!('theme' in localStorage)) {
      isDark.value = e.matches
      updateDOM()
    }
  })
})

const search = () => {
  const term = q.value.trim()
  if (term) {
    router.push({ path: '/', query: { q: term } })
  } else {
    router.push({ path: '/', query: {} })
  }
  console.debug('Search submitted', { query: term })
  menuOpen.value = false
}

const goNews = () => {
  router.push('/news')
  menuOpen.value = false
}

const logout = () => {
  showLogoutModal.value = true
}

const confirmLogout = async () => {
  try {
    await axios.post('/auth/logout')
  } catch {}
  localStorage.removeItem('token')
  localStorage.removeItem('user_id')
  localStorage.removeItem('is_admin')
  localStorage.removeItem('is_super_admin')
  showLogoutModal.value = false
  window.location.href = '/'
}
</script>
