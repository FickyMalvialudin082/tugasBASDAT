<?php
// ============================================
// FILE: hewan.php
// FUNGSI: Menampilkan dan mengelola data hewan
// ============================================

require_once 'database.php';

// Cari data
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
                                    <a href="edit_hewan.php?id=<?php echo $row['id_hewan']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="hapusData(<?php echo $row['id_hewan']; ?>, '<?php echo $row['nama_hewan']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
function hapusData(id, nama) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data hewan '" + nama + "' akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'hapus_hewan.php?id=' + id;
        }
    });
}
</script>
</body>
</html>