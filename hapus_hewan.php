<?php
// ============================================
// FILE: hapus_hewan.php
// FUNGSI: Menghapus data hewan
// ============================================

require_once 'database.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil nama file foto
$query = "SELECT foto FROM hewan WHERE id_hewan = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Hapus file foto
if ($data['foto'] && file_exists('uploads/' . $data['foto'])) {
    unlink('uploads/' . $data['foto']);
}

// Hapus data
$query = "DELETE FROM hewan WHERE id_hewan = $id";

if (mysqli_query($koneksi, $query)) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'Data hewan berhasil dihapus',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'hewan.php';
        });
    </script>";
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Data hewan gagal dihapus'
        }).then(() => {
            window.location.href = 'hewan.php';
        });
    </script>";
}
?>