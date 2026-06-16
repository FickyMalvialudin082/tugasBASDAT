<?php
// ============================================
// FILE: hapus_resep.php
// FUNGSI: Menghapus data resep
// ============================================

require_once 'database.php';

$id = $_GET['id'];

// Ambil id_jadwal untuk update status
$query = "SELECT id_jadwal FROM resep_obat WHERE id_resep = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
$id_jadwal = $data['id_jadwal'];

// Hapus resep
$query = "DELETE FROM resep_obat WHERE id_resep = $id";

if (mysqli_query($koneksi, $query)) {
    // Update status resep_terbit di jadwal
    $update = "UPDATE jadwal_pemeriksaan SET resep_terbit = 'Tidak' WHERE id_jadwal = $id_jadwal";
    mysqli_query($koneksi, $update);
    
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'Resep obat berhasil dihapus',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'resep.php';
        });
    </script>";
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Resep obat gagal dihapus'
        }).then(() => {
            window.location.href = 'resep.php';
        });
    </script>";
}
?>