<?php
// ============================================
// FILE: hapus_rawat_inap.php
// FUNGSI: Menghapus data rawat inap
// ============================================

// Aktifkan error reporting (opsional, untuk debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'ID tidak valid'
        }).then(() => {
            window.location.href = 'rawat_inap.php';
        });
    </script>
    </body>
    </html>";
    exit();
}

// Hapus perawatan harian terlebih dahulu (karena foreign key)
mysqli_query($koneksi, "DELETE FROM perawatan_harian WHERE id_rawat = $id");

// Hapus rawat inap
$query = "DELETE FROM rawat_inap WHERE id_rawat = $id";

if (mysqli_query($koneksi, $query)) {
    // Jika berhasil, tampilkan SweetAlert success
    echo "<!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'Data rawat inap berhasil dihapus',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'rawat_inap.php';
        });
    </script>
    </body>
    </html>";
} else {
    // Jika gagal, tampilkan SweetAlert error
    $error_msg = mysqli_error($koneksi);
    echo "<!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Data rawat inap gagal dihapus: " . addslashes($error_msg) . "'
        }).then(() => {
            window.location.href = 'rawat_inap.php';
        });
    </script>
    </body>
    </html>";
}
exit();
?>