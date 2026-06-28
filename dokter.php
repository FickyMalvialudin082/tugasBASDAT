<?php
// ============================================
// FILE: dokter.php
// FUNGSI: CRUD Data Dokter + Detail Modal (Tanpa File Eksternal)
// ============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

// ============================================
// PROSES TAMBAH
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_dokter']);
    $spesialisasi = mysqli_real_escape_string($koneksi, $_POST['spesialisasi']);
    $telp = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $jadwal = mysqli_real_escape_string($koneksi, $_POST['jadwal_praktik']);
    
    $query = "INSERT INTO dokter (nama_dokter, spesialisasi, no_telepon, email, jadwal_praktik) 
              VALUES ('$nama', '$spesialisasi', '$telp', '$email', '$jadwal')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: dokter.php?success=Data dokter berhasil ditambahkan");
    } else {
        header("Location: dokter.php?error=Gagal menambahkan data: " . mysqli_error($koneksi));
    }
    exit();
}

// ============================================
// PROSES EDIT
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = (int)$_POST['id_dokter'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_dokter']);
    $spesialisasi = mysqli_real_escape_string($koneksi, $_POST['spesialisasi']);
    $telp = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $jadwal = mysqli_real_escape_string($koneksi, $_POST['jadwal_praktik']);
    
    $query = "UPDATE dokter SET 
              nama_dokter='$nama', 
              spesialisasi='$spesialisasi', 
              no_telepon='$telp', 
              email='$email', 
              jadwal_praktik='$jadwal' 
              WHERE id_dokter=$id";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: dokter.php?success=Data dokter berhasil diupdate");
    } else {
        header("Location: dokter.php?error=Gagal mengupdate data: " . mysqli_error($koneksi));
    }
    exit();
}

// ============================================
// PROSES HAPUS
// ============================================
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    $query = "DELETE FROM dokter WHERE id_dokter = $id";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: dokter.php?success=Data dokter berhasil dihapus");
    } else {
        header("Location: dokter.php?error=Gagal menghapus data: " . mysqli_error($koneksi));
    }
    exit();
}

// ============================================
// AMBIL NOTIFIKASI DARI URL
// ============================================
$success_msg = isset($_GET['success']) ? $_GET['success'] : '';
$error_msg = isset($_GET['error']) ? $_GET['error'] : '';

// ============================================
// SEARCH & AMBIL DATA
// ============================================
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$where = $search ? "WHERE nama_dokter LIKE '%$search%'" : "";
$query = "SELECT * FROM dokter $where ORDER BY id_dokter DESC";
$result = mysqli_query($koneksi, $query);

// ============================================
// FUNGSI UNTUK MENAMPILKAN DETAIL (via GET)
// ============================================
if (isset($_GET['detail']) && $_GET['detail'] == 'load') {
    $detail_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($detail_id == 0) {
        echo '<div class="alert alert-danger">ID tidak valid!</div>';
        exit();
    }
    
    $detail_query = "SELECT * FROM dokter WHERE id_dokter = $detail_id";
    $detail_result = mysqli_query($koneksi, $detail_query);
    $data = mysqli_fetch_assoc($detail_result);
    
    if (!$data) {
        echo '<div class="alert alert-danger">Data dokter tidak ditemukan!</div>';
        exit();
    }
    
    $total_query = "SELECT COUNT(*) as total FROM jadwal_pemeriksaan WHERE id_dokter = $detail_id";
    $total_result = mysqli_query($koneksi, $total_query);
    $total_data = mysqli_fetch_assoc($total_result);
    ?>
    <div class="row">
        <div class="col-md-3 text-center">
            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 60px;">
                <i class="fas fa-user-md"></i>
            </div>
            <h5 class="mt-2"><?php echo htmlspecialchars($data['nama_dokter']); ?></h5>
            <span class="badge bg-primary"><?php echo htmlspecialchars($data['spesialisasi']); ?></span>
        </div>
        
        <div class="col-md-9">
            <h6 class="border-bottom pb-2"><i class="fas fa-id-card"></i> Informasi Dokter</h6>
            <table class="table table-borderless table-sm">
                <tr><td width="150"><strong>Nama Lengkap</strong></td><td>: <?php echo htmlspecialchars($data['nama_dokter']); ?></td></tr>
                <tr><td><strong>Spesialisasi</strong></td><td>: <?php echo htmlspecialchars($data['spesialisasi']); ?></td></tr>
                <tr><td><strong>No Telepon</strong></td><td>: <?php echo htmlspecialchars($data['no_telepon']); ?></td></tr>
                <tr><td><strong>Email</strong></td><td>: <?php echo htmlspecialchars($data['email']); ?></td></tr>
                <tr><td><strong>Jadwal Praktik</strong></td><td>: <?php echo nl2br(htmlspecialchars($data['jadwal_praktik'])); ?></td></tr>
                <tr><td><strong>Total Pemeriksaan</strong></td><td>: <?php echo $total_data['total']; ?> pasien</td></tr>
                <tr><td><strong>Terdaftar Sejak</strong></td><td>: <?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?></td></tr>
            </table>
        </div>
    </div>
    <?php
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dokter - Klinik Hewan</title>
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
            <h4><i class="fas fa-user-md me-2"></i> Data Dokter</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="fas fa-plus"></i> Tambah Dokter
            </button>
        </div>
        
        <!-- NOTIFIKASI -->
        <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- SEARCH -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Cari dokter..." value="<?php echo htmlspecialchars($search); ?>">
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
                                <th>Nama Dokter</th>
                                <th>Spesialisasi</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Jadwal Praktik</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                                <td><?php echo htmlspecialchars($row['spesialisasi']); ?></td>
                                <td><?php echo htmlspecialchars($row['no_telepon']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['jadwal_praktik']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="detailDokter(<?php echo $row['id_dokter']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editData(<?php echo $row['id_dokter']; ?>, '<?php echo addslashes($row['nama_dokter']); ?>', '<?php echo addslashes($row['spesialisasi']); ?>', '<?php echo $row['no_telepon']; ?>', '<?php echo $row['email']; ?>', '<?php echo addslashes($row['jadwal_praktik']); ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?hapus=<?php echo $row['id_dokter']; ?>" class="btn btn-sm btn-danger" onclick="return confirmHapus(event, '<?php echo addslashes($row['nama_dokter']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-user-md fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada data dokter</p>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                                        <i class="fas fa-plus"></i> Tambah Dokter
                                    </button>
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
<!-- MODAL TAMBAH -->
<!-- ============================================ -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Dokter</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-body">
                    <div class="mb-3"><label>Nama Dokter <span class="text-danger">*</span></label><input type="text" name="nama_dokter" class="form-control" required></div>
                    <div class="mb-3"><label>Spesialisasi</label><input type="text" name="spesialisasi" class="form-control"></div>
                    <div class="mb-3"><label>No Telepon</label><input type="text" name="no_telepon" class="form-control"></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
                    <div class="mb-3"><label>Jadwal Praktik</label>
                        <select name="jadwal_praktik" class="form-select">
                            <option value="Senin - Jumat, 09:00-16:00">Senin - Jumat (09:00-16:00)</option>
                            <option value="Senin - Jumat, 08:00-16:00">Senin - Jumat (08:00-16:00)</option>
                            <option value="Senin, Rabu, Jumat, 10:00-15:00">Senin, Rabu, Jumat (10:00-15:00)</option>
                            <option value="Selasa, Kamis, Sabtu, 08:00-12:00">Selasa, Kamis, Sabtu (08:00-12:00)</option>
                            <option value="Senin - Sabtu, 09:00-17:00">Senin - Sabtu (09:00-17:00)</option>
                            <option value="Selasa - Minggu, 10:00-18:00">Selasa - Minggu (10:00-18:00)</option>
                        </select>
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
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Dokter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_dokter" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3"><label>Nama Dokter <span class="text-danger">*</span></label><input type="text" name="nama_dokter" id="edit_nama" class="form-control" required></div>
                    <div class="mb-3"><label>Spesialisasi</label><input type="text" name="spesialisasi" id="edit_spesialisasi" class="form-control"></div>
                    <div class="mb-3"><label>No Telepon</label><input type="text" name="no_telepon" id="edit_telp" class="form-control"></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" id="edit_email" class="form-control"></div>
                    <div class="mb-3"><label>Jadwal Praktik</label><input type="text" name="jadwal_praktik" id="edit_jadwal" class="form-control"></div>
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
<!-- MODAL DETAIL (LOAD VIA AJAX KE HALAMAN SENDIRI) -->
<!-- ============================================ -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Detail Dokter</h5>
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
// FUNGSI DETAIL DOKTER (PAKAI FILE INI SENDIRI)
// ============================================
function detailDokter(id) {
    // Tampilkan loading
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
    
    // Ambil data dari halaman yang sama (dokter.php?detail=load&id=...)
    fetch('dokter.php?detail=load&id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('detailContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Gagal memuat data: ${error.message}
                </div>
            `;
        });
}

// ============================================
// FUNGSI EDIT
// ============================================
function editData(id, nama, spesialisasi, telp, email, jadwal) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_spesialisasi').value = spesialisasi;
    document.getElementById('edit_telp').value = telp;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_jadwal').value = jadwal;
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
        text: "Data dokter '" + nama + "' akan dihapus permanen!",
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
// AUTO HIDE ALERT
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