<?php

/**
 * Script Pembuatan Akun Admin (Admin Creator Script)
 * 
 * Script ini digunakan untuk membuat akun administrator baru secara aman melalui Command Line Interface (CLI).
 * Akun admin TIDAK DAPAT dibuat melalui formulir registrasi website publik.
 * 
 * CARA MENJALANKAN:
 * 1. Buka terminal/command prompt
 * 2. Arahkan ke direktori root project
 * 3. Jalankan perintah: php create_admin.php
 * 4. Ikuti instruksi di layar untuk memasukkan Nama, Email, dan Password
 * 
 * PERSYARATAN SISTEM:
 * - PHP 8.1 atau lebih baru
 * - Composer dependencies telah terinstall (vendor/autoload.php ada)
 * - Koneksi database telah terkonfigurasi di file .env
 * 
 * KEAMANAN:
 * - File ini harus memiliki permission ketat (chmod 700 disarankan pada Linux/Unix)
 * - Jangan letakkan file ini di direktori 'public/'
 * - Hapus file ini setelah selesai inisialisasi awal jika tidak diperlukan lagi untuk maintenance
 * 
 * OUTPUT:
 * - Akun user baru dengan hak akses admin (is_admin = 1, is_active = 1)
 * - Log pencatatan pembuatan akun di storage/logs/laravel.log
 */

// 1. Bootstrap Laravel Framework
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// 2. Fungsi Helper untuk Input CLI
function prompt($message) {
    echo $message . ": ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    return trim($line);
}

// 3. Fungsi untuk Validasi Input
function validateInput($data) {
    $validator = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8', // Password minimal 8 karakter
    ]);

    if ($validator->fails()) {
        echo "\n[ERROR] Validasi Gagal:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "- $error\n";
        }
        return false;
    }
    return true;
}

// 4. Main Execution
echo "\n==========================================\n";
echo "      ADMIN ACCOUNT CREATOR WIZARD      \n";
echo "==========================================\n\n";

// Input Data
$name = prompt("Masukkan Nama Lengkap Admin");
$email = prompt("Masukkan Email Admin");
$password = prompt("Masukkan Password Admin (Min. 8 Karakter)");

// Validasi
if (!validateInput(['name' => $name, 'email' => $email, 'password' => $password])) {
    echo "\nProses dibatalkan.\n";
    exit(1);
}

// 5. Generate ID Unik & Enkripsi
$adminId = (string) Str::uuid();
$hashedPassword = Hash::make($password);

try {
    // 6. Penyimpanan Credentials Aman
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => $hashedPassword,
        'is_admin' => true,
        'is_active' => true,
        'email_verified' => true,
        'email_verified_at' => now(),
        // Kita bisa menyimpan adminId di kolom lain jika ada, atau menggunakannya untuk logging saja
    ]);

    // 7. Logging
    Log::info("NEW_ADMIN_CREATED", [
        'admin_id' => $user->id,
        'uuid' => $adminId,
        'email' => $user->email,
        'created_by_script' => true,
        'timestamp' => now()->toIso8601String()
    ]);

    echo "\n[SUCCESS] Akun Admin Berhasil Dibuat!\n";
    echo "------------------------------------------\n";
    echo "Nama       : " . $user->name . "\n";
    echo "Email      : " . $user->email . "\n";
    echo "Admin UUID : " . $adminId . "\n";
    echo "Status     : AKTIF\n";
    echo "------------------------------------------\n";
    echo "Silakan login di halaman /admin-login\n";

} catch (\Exception $e) {
    echo "\n[ERROR] Terjadi kesalahan saat menyimpan data:\n";
    echo $e->getMessage() . "\n";
    Log::error("ADMIN_CREATION_FAILED", ['error' => $e->getMessage()]);
    exit(1);
}
