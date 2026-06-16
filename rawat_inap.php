<?php
// ============================================
// FILE: rawat_inap.php
// FUNGSI: Halaman daftar rawat inap
// ============================================

require_once 'database.php';

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek pesan sukses/error dari hapus
$success_msg = isset($_GET['success']) ? $_GET['success'] : '';
$error_msg = isset($_GET['error']) ? $_GET['error'] : '';

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = $search ? "WHERE h.nama_hewan LIKE '%$search%' OR r.status_rawat LIKE '%$search%'" : "";

// Query data rawat inap dengan JOIN
$query = "SELECT r.*, h.nama_hewan, h.jenis_hewan, p.nama_pemilik, d.nama_dokter 
          FROM rawat_inap r
          LEFT JOIN hewan h ON r.id_hewan = h.id_hewan
          LEFT JOIN pemilik p ON h.id_pemilik = p.id_pemilik
          LEFT JOIN dokter d ON r.id_dokter = d.id_dokter
          $where
          ORDER BY r.tanggal_masuk DESC";

$result = mysqli_query($koneksi, $query);

// Cek error query
if (!$result) {
    die("Error query: " . mysqli_error($koneksi));
}

// Ambil statistik
$total_menunggu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM rawat_inap WHERE status_rawat = 'Menunggu'"))['total'] ?? 0;
$total_dirawat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM rawat_inap WHERE status_rawat = 'Dirawat'"))['total'] ?? 0;
$total_selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM rawat_inap WHERE status_rawat = 'Selesai'"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rawat Inap - Klinik Hewan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>
    
    <div class="content-wrapper fade-in">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-hospital me-2"></i> Rawat Inap</h4>
            <a href="tambah_rawat_inap.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Rawat Inap
            </a>
        </div>
        
        <!-- Alert Success -->
        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Alert Error -->
        <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stats-card h-100" style="border-left-color: #ffc107;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Menunggu</p>
                                <h2 class="mb-0"><?php echo $total_menunggu; ?></h2>
                            </div>
                            <div class="stats-icon" style="opacity: 0.5;"><i class="fas fa-clock text-warning"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stats-card h-100" style="border-left-color: #17a2b8;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Sedang Dirawat</p>
                                <h2 class="mb-0"><?php echo $total_dirawat; ?></h2>
                            </div>
                            <div class="stats-icon" style="opacity: 0.5;"><i class="fas fa-procedures text-info"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stats-card h-100" style="border-left-color: #28a745;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Selesai</p>
                                <h2 class="mb-0"><?php echo $total_selesai; ?></h2>
                            </div>
                            <div class="stats-icon" style="opacity: 0.5;"><i class="fas fa-check-circle text-success"></i></div>
                        </div>
                    </div>
                </div>
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
                                <th>Tanggal Masuk</th>
                                <th>Hewan</th>
                                <th>Pemilik</th>
                                <th>Dokter</th>
                                <th>Diagnosa Awal</th>
                                <th>Status</th>
                                <th>Biaya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if ($result && mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)): 
                                $status_class = '';
                                if ($row['status_rawat'] == 'Menunggu') $status_class = 'badge-menunggu';
                                elseif ($row['status_rawat'] == 'Dirawat') $status_class = 'badge-diperiksa';
                                elseif ($row['status_rawat'] == 'Selesai') $status_class = 'badge-selesai';
                                else $status_class = 'badge-secondary';
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['nama_hewan'] ?? '-'); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['nama_pemilik'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_dokter'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars(substr($row['diagnosa_awal'] ?? '', 0, 30)) . (strlen($row['diagnosa_awal'] ?? '') > 30 ? '...' : ''); ?></td>
                                <td><span class="badge <?php echo $status_class; ?>"><?php echo $row['status_rawat']; ?></span></td>
                                <td>Rp <?php echo number_format($row['biaya'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <a href="detail_rawat_inap.php?id=<?php echo $row['id_rawat']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_rawat_inap.php?id=<?php echo $row['id_rawat']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- HAPUS LANGSUNG DENGAN confirm() bawaan browser -->
                                    <a href="hapus_rawat_inap.php?id=<?php echo $row['id_rawat']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus data rawat inap untuk hewan <?php echo $row['nama_hewan']; ?>?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-hospital fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data rawat inap</p>
                                    <a href="tambah_rawat_inap.php" class="btn btn-sm btn-primary">Tambah Rawat Inap</a>
                                </td>
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
</body>
</html>