
## Troubleshooting

### Masalah Login Admin

Jika Anda mengalami masalah saat login sebagai admin, ikuti langkah-langkah berikut:

1.  **Verifikasi Kredensial**:
    *   **Email**: `admin@gmail.com`
    *   **Password**: `Nagasaya1` (Case-sensitive! Pastikan 'N' huruf besar).

2.  **Reset Password Manual**:
    Jika password tetap tidak berfungsi, Anda dapat meresetnya melalui Tinker:
    ```bash
    php artisan tinker
    ```
    Lalu jalankan command berikut:
    ```php
    $user = \App\Models\User::where('email', 'admin@gmail.com')->first();
    $user->password = Hash::make('Nagasaya1');
    $user->save();
    exit
    ```

3.  **Cek Status Akun**:
    Pastikan akun memiliki flag `is_admin = true` dan `is_active = true`.

4.  **Cek Koneksi Database**:
    Pastikan konfigurasi database di `.env` sudah benar dan database server berjalan.

5.  **Clear Cache**:
    Terkadang cache aplikasi menyebabkan masalah:
    ```bash
    php artisan config:clear
    php artisan cache:clear
    ```
