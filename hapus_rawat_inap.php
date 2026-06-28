<?php
// ============================================
// FILE: hapus_rawat_inap.php
// FUNGSI: Menghapus data rawat inap (Redirect)
// ============================================

require_once 'database.php';

// Cek koneksi
if (!$koneksi) {
    header("Location: rawat_inap.php?error=Koneksi database gagal");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    header("Location: rawat_inap.php?error=ID tidak valid");
    exit();
}

// Hapus perawatan harian terlebih dahulu (karena foreign key)
mysqli_query($koneksi, "DELETE FROM perawatan_harian WHERE id_rawat = $id");

// Hapus rawat inap
$query = "DELETE FROM rawat_inap WHERE id_rawat = $id";

if (mysqli_query($koneksi, $query)) {
    header("Location: rawat_inap.php?success=Data rawat inap berhasil dihapus");
} else {
    header("Location: rawat_inap.php?error=Gagal menghapus data: " . mysqli_error($koneksi));
}
exit();
?>