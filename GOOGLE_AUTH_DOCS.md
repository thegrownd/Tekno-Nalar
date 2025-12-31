# Dokumentasi Teknis Implementasi Google OAuth

Dokumen ini menjelaskan alur autentikasi, penanganan error, dan troubleshooting untuk fitur Login dengan Google pada aplikasi TeknoNalar.

## 1. Alur Autentikasi (Authentication Flow)

Proses autentikasi menggunakan protokol OAuth 2.0 dengan library Laravel Socialite (Backend) dan Vue.js (Frontend).

### Langkah-langkah:
1.  **Inisiasi**: User mengklik tombol "Login with Google" di halaman Login.
2.  **Redirect ke Google**: Frontend mengarahkan user ke endpoint backend `/api/auth/google`, yang kemudian me-redirect ke halaman persetujuan Google.
3.  **Callback**: Setelah user menyetujui, Google me-redirect kembali ke aplikasi dengan parameter `code`.
    *   URL: `http://127.0.0.1:8000/auth-google-callback?code=...`
4.  **Frontend Processing**:
    *   Komponen `GoogleCallback.vue` menangkap `code` dari URL.
    *   Mengirim request POST ke `/api/auth/google/callback` dengan `code`.
5.  **Backend Processing (`AuthController::handleGoogleCallback`)**:
    *   Menerima `code`.
    *   Menggunakan `Socialite::driver('google')->stateless()->user()` untuk menukar `code` dengan Access Token dan mengambil data profil user.
    *   **User Lookup/Creation**:
        *   Jika email sudah ada: Update `google_id` dan `profile_picture_url`.
        *   Jika email belum ada: Buat user baru dengan `email_verified = true` dan `is_active = true`.
    *   **Validasi Status**: Cek apakah `is_active` user adalah `true`.
    *   **Token Generation**: Generate JWT Token untuk user.
    *   **Logging**: Mencatat aktivitas login.
6.  **Response**: Backend mengembalikan JWT Token dan data user ke Frontend.
7.  **Finalisasi**: Frontend menyimpan token di LocalStorage dan me-redirect user ke halaman utama (atau dashboard admin).

## 2. Penanganan Error (Error Handling)

Pola penanganan error diterapkan di kedua sisi (Backend dan Frontend) untuk memastikan pengalaman pengguna yang baik.

### Backend (`AuthController.php`)
*   **Socialite Error**: Menangkap exception saat komunikasi dengan Google gagal (misal: koneksi timeout, code invalid).
    *   Return: 401 Unauthorized.
*   **Inactive Account**: Mengecek flag `is_active`.
    *   Return: 403 Forbidden dengan pesan "Akun Anda dinonaktifkan".
*   **General Exception**: `try-catch` block global untuk menangkap error tak terduga.
    *   Return: 500 Internal Server Error dengan detail pesan (log mencatat stack trace).

### Frontend (`GoogleCallback.vue`)
*   **Code Missing**: Menampilkan pesan jika parameter `code` tidak ditemukan di URL.
*   **API Error**: Menangkap response error dari backend.
    *   Menampilkan pesan error spesifik dari server jika ada.
    *   Menampilkan pesan "Koneksi Bermasalah" jika server tidak dapat dihubungi.
*   **Retry Mechanism**: Menyediakan tombol "Coba Lagi" yang mengulang proses dari awal.

## 3. Troubleshooting

### Masalah: "Akun Anda dinonaktifkan"
*   **Penyebab**: User berhasil login ke Google, tetapi status `is_active` di database `users` adalah `false` (0).
*   **Solusi**:
    *   Cek database: `SELECT * FROM users WHERE email = '...';`
    *   Update status: `UPDATE users SET is_active = 1 WHERE email = '...';`
    *   **Fix Otomatis**: Kode backend telah diperbarui untuk memastikan user baru yang dibuat via Google selalu memiliki `is_active = true`.

### Masalah: "cURL error 60: SSL certificate problem"
*   **Penyebab**: PHP tidak dapat memverifikasi sertifikat SSL Google karena CA Bundle tidak dikonfigurasi.
*   **Solusi**:
    *   Pastikan file `cacert.pem` ada (misal: `C:\laragon\etc\ssl\cacert.pem`).
    *   Set environment variable di `.env`: `CURL_CA_BUNDLE=C:/laragon/etc/ssl/cacert.pem`.
    *   Update `config/services.php` untuk menggunakan opsi `verify` pada Guzzle.

### Masalah: Halaman Blank setelah Login
*   **Penyebab**: Frontend routing tidak mengenali URL callback.
*   **Solusi**: Pastikan route `/auth-google-callback` terdaftar di `app.js` dan mengarah ke komponen yang benar.

## 4. Logging

Log aplikasi tersimpan di `storage/logs/laravel.log`. Gunakan log ini untuk menelusuri masalah.
*   Keyword pencarian: "Google Callback hit", "Google User Retrieved", "Google Auth Failed".
