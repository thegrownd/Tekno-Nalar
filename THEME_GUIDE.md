# 🎨 Panduan Tema & Preview Website

Dokumen ini berisi panduan lengkap mengenai sistem tema Dark/Light mode yang diimplementasikan serta cara menjalankan preview website secara lokal.

## 🌓 Sistem Tema

Website ini menggunakan sistem tema yang komprehensif berbasis CSS Variables dan Tailwind CSS.

### Fitur Utama
- **Deteksi Otomatis**: Mendeteksi preferensi sistem (Dark/Light) secara otomatis saat pertama kali dimuat.
- **Persistensi**: Preferensi pengguna disimpan di `localStorage` sehingga tetap terjaga saat refresh.
- **Real-time Listener**: Jika pengguna mengubah tema sistem operasi, website akan beradaptasi (kecuali jika sudah di-override manual).
- **High Contrast**: Semua teks memiliki rasio kontras minimum 4.5:1 untuk aksesibilitas.
- **Smooth Transition**: Perubahan tema memiliki efek transisi halus (300ms).

### Cara Menggunakan
1. **Toggle Tema**: Klik ikon Matahari/Bulan di navbar atau tombol 🎨 di pojok kanan bawah (pada halaman Artikel).
2. **Override**: Pilihan Anda akan disimpan. Untuk kembali mengikuti sistem, hapus `theme` dari Local Storage browser.

### CSS Variables
Variabel didefinisikan di `resources/css/app.css`:

| Variable | Light Mode | Dark Mode | Fungsi |
|----------|------------|-----------|--------|
| `--bg-body` | `#F9FAFB` | `#0F172A` | Background utama halaman |
| `--bg-card` | `#FFFFFF` | `#1E293B` | Background kartu/kontainer |
| `--bg-input` | `#FFFFFF` | `#111827` | Background input form |
| `--text-primary` | `#111827` | `#F9FAFB` | Teks utama (Judul, Konten) |
| `--text-secondary` | `#4B5563` | `#D1D5DB` | Teks sekunder (Deskripsi) |
| `--text-muted` | `#6B7280` | `#9CA3AF` | Teks pudar (Meta info) |
| `--border-color` | `#E5E7EB` | `#374151` | Warna border |

### Komponen yang Terpengaruh
- **Navbar**: Menggunakan `navbar-unified` dengan efek blur.
- **Articles**: Card artikel, dropdown filter, dan teks konten.
- **ArticleForm**: Input, textarea, dan preview kategori.
- **About**: Section card dan list fitur.
- **Buttons**: Menggunakan class `btn-gradient` atau border sesuai tema.

---

## 🚀 Panduan Preview Website

Ikuti langkah-langkah ini untuk menjalankan website di lingkungan lokal Anda.

### Persyaratan Sistem
- PHP 8.0 atau lebih baru
- Composer (Dependency Manager untuk PHP)
- Node.js & NPM (untuk compile aset frontend)
- Database (MySQL/MariaDB/SQLite)

### Langkah-langkah Instalasi

1. **Install Dependencies Backend**
   ```bash
   composer install
   ```

2. **Setup Environment**
   Duplikat file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Atur konfigurasi database di file `.env` (DB_DATABASE, DB_USERNAME, dll).

3. **Generate App Key**
   ```bash
   php artisan key:generate
   ```

4. **Migrasi Database** (Pastikan database sudah dibuat)
   ```bash
   php artisan migrate
   ```

5. **Install Dependencies Frontend**
   ```bash
   npm install
   ```

6. **Jalankan Server Development**
   Anda perlu menjalankan dua terminal terpisah:

   **Terminal 1 (Laravel Server):**
   ```bash
   php artisan serve
   ```
   
   **Terminal 2 (Vite Development Server):**
   ```bash
   npm run dev
   ```

7. **Akses Website**
   Buka browser dan kunjungi: [http://localhost:8000](http://localhost:8000)

### Troubleshooting Umum

**Masalah: Tampilan berantakan / CSS tidak termuat**
- Pastikan `npm run dev` sedang berjalan.
- Jika deploy ke production, jalankan `npm run build`.

**Masalah: Error 500 / Database Error**
- Cek file `.env` apakah koneksi database sudah benar.
- Pastikan `php artisan migrate` sudah dijalankan.

**Masalah: Tema tidak berubah**
- Cek Console browser (F12) untuk melihat error JavaScript.
- Hapus `localStorage` dengan menjalankan `localStorage.clear()` di Console.

**Masalah: Permission Denied pada storage**
- Jalankan `chmod -R 775 storage bootstrap/cache` (Linux/Mac).
