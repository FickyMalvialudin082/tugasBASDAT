<?php
// ============================================
// FILE: resep.php
// FUNGSI: Menampilkan semua resep obat
// ============================================

require_once 'database.php';

// Hapus resep
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM resep_obat WHERE id_resep = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Resep berhasil dihapus!'); window.location.href='resep.php';</script>";
    }
}

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = $search ? "WHERE h.nama_hewan LIKE '%$search%' OR r.obat LIKE '%$search%'" : "";

// Query data resep
$query = "SELECT r.*, h.nama_hewan, p.nama_pemilik, j.tanggal_pemeriksaan 
          FROM resep_obat r
          JOIN hewan h ON r.id_hewan = h.id_hewan
          JOIN pemilik p ON h.id_pemilik = p.id_pemilik
          JOIN jadwal_pemeriksaan j ON r.id_jadwal = j.id_jadwal
          $where
          ORDER BY r.created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resep Obat - Klinik Hewan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>
    
    <div class="content-wrapper fade-in">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-prescription-bottle me-2"></i> Resep Obat</h4>
            <a href="tambah_resep.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Resep
            </a>
        </div>
        
        <!-- Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari berdasarkan nama hewan atau nama obat..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Hewan</th>
                                <th>Pemilik</th>
                                <th>Obat</th>
                                <th>Dosis</th>
                                <th>Durasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['nama_hewan']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['nama_pemilik']); ?></td>
                                <td><?php echo htmlspecialchars($row['obat']); ?></td>
                                <td><?php echo htmlspecialchars($row['dosis']); ?></td>
                                <td><?php echo htmlspecialchars($row['durasi']); ?></td>
                                <td>
                                    <a href="cetak_resep.php?id=<?php echo $row['id_resep']; ?>" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="edit_resep.php?id=<?php echo $row['id_resep']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="hapusResep(<?php echo $row['id_resep']; ?>, '<?php echo $row['nama_hewan']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                 </td
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-prescription-bottle fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada resep obat</p>
                                    <a href="tambah_resep.php" class="btn btn-sm btn-primary">Tambah Resep Pertama</a>
                                 </td
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function hapusResep(id, nama) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Resep untuk '" + nama + "' akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'hapus_resep.php?id=' + id;
        }
    });
}
</script>
</body>
</html>