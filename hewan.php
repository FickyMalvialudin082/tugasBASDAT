<?php
// ============================================
// FILE: hewan.php
// FUNGSI: Menampilkan dan mengelola data hewan
// ============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

// ============================================
// PROSES HAPUS DATA HEWAN
// ============================================
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Ambil nama file foto sebelum hapus
    $query_foto = "SELECT foto FROM hewan WHERE id_hewan = $id";
    $result_foto = mysqli_query($koneksi, $query_foto);
    $data_foto = mysqli_fetch_assoc($result_foto);
    
    // Hapus file foto jika ada
    if ($data_foto['foto'] && file_exists('uploads/' . $data_foto['foto'])) {
        unlink('uploads/' . $data_foto['foto']);
    }
    
    // Hapus data hewan
    $query = "DELETE FROM hewan WHERE id_hewan = $id";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: hewan.php?success=Data hewan berhasil dihapus");
    } else {
        header("Location: hewan.php?error=Gagal menghapus data: " . mysqli_error($koneksi));
    }
    exit();
}

// ============================================
// AMBIL NOTIFIKASI DARI URL
// ============================================
$success_msg = isset($_GET['success']) ? $_GET['success'] : '';
$error_msg = isset($_GET['error']) ? $_GET['error'] : '';

// ============================================
// CARI DATA
// ============================================
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$where = '';
if ($search) {
    $where = "WHERE h.nama_hewan LIKE '%$search%' 
              OR h.jenis_hewan LIKE '%$search%' 
              OR p.nama_pemilik LIKE '%$search%'";
}

// Query data hewan
$query = "SELECT h.*, p.nama_pemilik 
          FROM hewan h 
          JOIN pemilik p ON h.id_pemilik = p.id_pemilik 
          $where 
          ORDER BY h.id_hewan DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Hewan - Klinik Hewan</title>
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
            <h4><i class="fas fa-dog me-2"></i> Data Hewan</h4>
            <a href="tambah_hewan.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Hewan
            </a>
        </div>
        
        <!-- NOTIFIKASI SUKSES -->
        <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- NOTIFIKASI ERROR -->
        <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Search Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari berdasarkan nama hewan, jenis, atau pemilik..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Table Data -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama Hewan</th>
                                <th>Jenis</th>
                                <th>Ras</th>
                                <th>JK</th>
                                <th>Pemilik</th>
                                <th>Keluhan</th>
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
                                <td>
                                    <?php if ($row['foto'] && file_exists('uploads/' . $row['foto'])): ?>
                                        <img src="uploads/<?php echo $row['foto']; ?>" class="thumbnail" alt="Foto">
                                    <?php else: ?>
                                        <img src="assets/img/default-pet.png" class="thumbnail" alt="Default">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['nama_hewan']); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis_hewan']); ?></td>
                                <td><?php echo htmlspecialchars($row['ras']); ?></td>
                                <td><?php echo $row['jenis_kelamin']; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pemilik']); ?></td>
                                <td><?php echo htmlspecialchars(substr($row['keluhan'], 0, 30)) . (strlen($row['keluhan']) > 30 ? '...' : ''); ?></td>
                                <td>
                                    <!-- TOMBOL DETAIL (READ) -->
                                    <button class="btn btn-sm btn-info" onclick="detailHewan(<?php echo $row['id_hewan']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="edit_hewan.php?id=<?php echo $row['id_hewan']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?hapus=<?php echo $row['id_hewan']; ?>" class="btn btn-sm btn-danger" onclick="return confirmHapus(event, '<?php echo addslashes($row['nama_hewan']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada data hewan</p>
                                    <a href="tambah_hewan.php" class="btn btn-sm btn-primary">Tambah Hewan</a>
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

<!-- ============================================ -->
<!-- MODAL DETAIL HEWAN -->
<!-- ============================================ -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Detail Hewan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ============================================
// FUNGSI DETAIL HEWAN (READ) - Ambil data via AJAX
// ============================================
function detailHewan(id) {
    // Tampilkan loading di modal
    document.getElementById('detailContent').innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat data...</p>
        </div>
    `;
    
    // Tampilkan modal
    var modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
    
    // Ambil data via AJAX
    fetch('detail_hewan.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('detailContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Gagal memuat data: ${error}
                </div>
            `;
        });
}

// ============================================
// FUNGSI KONFIRMASI HAPUS
// ============================================
function confirmHapus(event, nama) {
    event.preventDefault();
    var url = event.currentTarget.getAttribute('href');
    
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data hewan '" + nama + "' akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#0a261f',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}

// ============================================
// AUTO HIDE ALERT SETELAH 3 DETIK
// ============================================
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 3000);
</script>

</body>
</html>