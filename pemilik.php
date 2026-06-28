<?php
// ============================================
// FILE: pemilik.php
// FUNGSI: CRUD Data Pemilik + READ (Detail)
// ============================================

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

// ============================================
// PROSES TAMBAH DATA
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $jk = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $telp = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $query = "INSERT INTO pemilik (nama_pemilik, jenis_kelamin, no_telepon, alamat) 
              VALUES ('$nama', '$jk', '$telp', '$alamat')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: pemilik.php?success=Data pemilik berhasil ditambahkan");
    } else {
        header("Location: pemilik.php?error=Gagal menambahkan data: " . mysqli_error($koneksi));
    }
    exit();
}

// ============================================
// PROSES EDIT DATA
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = (int)$_POST['id_pemilik'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $jk = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $telp = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $query = "UPDATE pemilik SET 
              nama_pemilik='$nama', 
              jenis_kelamin='$jk', 
              no_telepon='$telp', 
              alamat='$alamat' 
              WHERE id_pemilik=$id";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: pemilik.php?success=Data pemilik berhasil diupdate");
    } else {
        header("Location: pemilik.php?error=Gagal mengupdate data: " . mysqli_error($koneksi));
    }
    exit();
}

// ============================================
// PROSES HAPUS DATA
// ============================================
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    // Cek apakah pemilik memiliki hewan
    $cek = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM hewan WHERE id_pemilik = $id");
    $data = mysqli_fetch_assoc($cek);

    if ($data['total'] > 0) {
        header("Location: pemilik.php?error=Pemilik ini memiliki " . $data['total'] . " data hewan. Hapus data hewan terlebih dahulu!");
        exit();
    }

    // Hapus data pemilik
    mysqli_query($koneksi, "DELETE FROM pemilik WHERE id_pemilik = $id");
    header("Location: pemilik.php?success=Data pemilik berhasil dihapus");
    exit();
}

// ============================================
// AMBIL NOTIFIKASI DARI URL
// ============================================
$success_msg = isset($_GET['success']) ? $_GET['success'] : '';
$error_msg = isset($_GET['error']) ? $_GET['error'] : '';

// ============================================
// AMBIL DATA PEMILIK
// ============================================
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = $search ? "WHERE nama_pemilik LIKE '%$search%'" : "";
$query = "SELECT * FROM pemilik $where ORDER BY id_pemilik DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pemilik - Klinik Hewan</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-users me-2"></i> Data Pemilik</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="fas fa-plus"></i> Tambah Pemilik
            </button>
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
        
        <!-- SEARCH -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama pemilik..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- TABLE -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>JK</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pemilik']); ?></td>
                                <td><?php echo $row['jenis_kelamin']; ?></td>
                                <td><?php echo $row['no_telepon']; ?></td>
                                <td><?php echo htmlspecialchars(substr($row['alamat'], 0, 40)); ?></td>
                                <td>
                                    <!-- TOMBOL DETAIL (READ) -->
                                    <button class="btn btn-sm btn-info" onclick="detailData(<?php echo $row['id_pemilik']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData(<?php echo $row['id_pemilik']; ?>, '<?php echo addslashes($row['nama_pemilik']); ?>', '<?php echo $row['jenis_kelamin']; ?>', '<?php echo $row['no_telepon']; ?>', '<?php echo addslashes($row['alamat']); ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?hapus=<?php echo $row['id_pemilik']; ?>" class="btn btn-sm btn-danger" onclick="return confirmHapus(event, '<?php echo addslashes($row['nama_pemilik']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TAMBAH -->
<!-- ============================================ -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Pemilik</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Pemilik <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pemilik" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>No Telepon</label>
                        <input type="text" name="no_telepon" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT -->
<!-- ============================================ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Pemilik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_pemilik" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Pemilik <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pemilik" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="edit_jk" class="form-select">
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>No Telepon</label>
                        <input type="text" name="no_telepon" id="edit_telp" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" id="edit_alamat" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL DETAIL (READ) -->
<!-- ============================================ -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Detail Pemilik</h5>
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
// FUNGSI DETAIL (READ) - Ambil data via AJAX
// ============================================
function detailData(id) {
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
    fetch('detail_pemilik.php?id=' + id)
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
// FUNGSI EDIT DATA
// ============================================
function editData(id, nama, jk, telp, alamat) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_jk').value = jk;
    document.getElementById('edit_telp').value = telp;
    document.getElementById('edit_alamat').value = alamat;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// ============================================
// FUNGSI KONFIRMASI HAPUS
// ============================================
function confirmHapus(event, nama) {
    event.preventDefault();
    var url = event.currentTarget.getAttribute('href');
    
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data pemilik '" + nama + "' akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
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