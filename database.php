<?php
// ============================================
// FILE: database.php
// FUNGSI: Koneksi ke database MySQL
// ============================================

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_klinik_hewan');

// Membuat koneksi menggunakan MySQLi
$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($koneksi, "utf8");

// Untuk debugging (hapus komentar jika perlu)
// echo "Koneksi database berhasil!";
?>