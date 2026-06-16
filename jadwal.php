<?php
// ============================================
// FILE: jadwal.php
// FUNGSI: CRUD Jadwal Pemeriksaan
// ============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

// ============================================
// PROSES TAMBAH JADWAL
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $hewan = (int)$_POST['id_hewan'];
    $dokter = (int)$_POST['id_dokter'];
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal_pemeriksaan']);
    $jam = mysqli_real_escape_string($koneksi, $_POST['jam_pemeriksaan']);
    $keluhan = mysqli_real_escape_string($koneksi, $_POST['keluhan']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status_pemeriksaan']);
    
    $query = "INSERT INTO jadwal_pemeriksaan (id_hewan, id_dokter, tanggal_pemeriksaan, jam_pemeriksaan, keluhan, status_pemeriksaan) 
              VALUES ('$hewan', '$dokter', '$tanggal', '$jam', '$keluhan', '$status')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: jadwal.php?success=Jadwal pemeriksaan berhasil ditambahkan");
    } else {
        header("Location: jadwal.php?error=Gagal menambahkan jadwal: " . mysqli_error($koneksi));
    }
    exit();
}

// ============================================
// PROSES EDIT JADWAL
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = (int)$_POST['id_jadwal'];
    $hewan = (int)$_POST['id_hewan'];
    $dokter = (int)$_POST['id_dokter'];
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal_pemeriksaan']);
    $jam = mysqli_real_escape_string($koneksi, $_POST['jam_pemeriksaan']);
    $keluhan = mysqli_real_escape_string($koneksi, $_POST['keluhan']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status_pemeriksaan']);
    
    $query = "UPDATE jadwal_pemeriksaan SET 
              id_hewan='$hewan', 
              id_dokter='$dokter', 
              tanggal_pemeriksaan='$tanggal', 
              jam_pemeriksaan='$jam', 
              keluhan='$keluhan', 
              status_pemeriksaan='$status' 
              WHERE id_jadwal=$id";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: jadwal.php?success=Jadwal pemeriksaan berhasil diupdate");
    } else {
        header("Location: jadwal.php?error=Gagal mengupdate jadwal: " . mysqli_error($koneksi));
    }
    exit();
}

// ============================================
// PROSES HAPUS JADWAL
// ============================================
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    $query = "DELETE FROM jadwal_pemeriksaan WHERE id_jadwal = $id";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: jadwal.php?success=Jadwal pemeriksaan berhasil dihapus");
    } else {
        header("Location: jadwal.php?error=Gagal menghapus jadwal: " . mysqli_error($koneksi));
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
$where = '';
if ($search) {
    $where = "WHERE h.nama_hewan LIKE '%$search%' OR j.tanggal_pemeriksaan = '$search'";
}

$query = "SELECT j.*, h.nama_hewan, d.nama_dokter 
          FROM jadwal_pemeriksaan j
          JOIN hewan h ON j.id_hewan = h.id_hewan
          JOIN dokter d ON j.id_dokter = d.id_dokter
          $where
          ORDER BY j.tanggal_pemeriksaan DESC";
$result = mysqli_query($koneksi, $query);

// Data untuk dropdown
$hewan_list = mysqli_query($koneksi, "SELECT * FROM hewan ORDER BY nama_hewan");
$dokter_list = mysqli_query($koneksi, "SELECT * FROM dokter ORDER BY nama_dokter");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pemeriksaan - Klinik Hewan</title>
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
            <h4><i class="fas fa-calendar-alt me-2"></i> Jadwal Pemeriksaan</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="fas fa-plus"></i> Tambah Jadwal
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
                <form method="GET" class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari berdasarkan nama hewan atau tanggal (YYYY-MM-DD)..."
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

        <!-- TABLE -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hewan</th>
                                <th>Dokter</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Resep</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($result)):
                                $status_class = $row['status_pemeriksaan'] == 'Menunggu' ? 'badge-menunggu' : 
                                              ($row['status_pemeriksaan'] == 'Sedang Diperiksa' ? 'badge-diperiksa' : 'badge-selesai');
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_hewan']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></td>
                                <td><?php echo date('H:i', strtotime($row['jam_pemeriksaan'])); ?></td>
                                <td>
                                    <span class="badge <?php echo $status_class; ?>">
                                        <?php echo $row['status_pemeriksaan']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (isset($row['resep_terbit']) && $row['resep_terbit'] == 'Ya'): ?>
                                        <a href="resep.php?search=<?php echo $row['nama_hewan']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-prescription-bottle"></i> Ada
                                        </a>
                                    <?php else: ?>
                                        <a href="tambah_resep.php?jadwal=<?php echo $row['id_jadwal']; ?>" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-plus"></i> Buat
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                            onclick="editData(<?php echo $row['id_jadwal']; ?>, <?php echo $row['id_hewan']; ?>, <?php echo $row['id_dokter']; ?>, '<?php echo $row['tanggal_pemeriksaan']; ?>', '<?php echo $row['jam_pemeriksaan']; ?>', '<?php echo addslashes($row['keluhan']); ?>', '<?php echo $row['status_pemeriksaan']; ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?hapus=<?php echo $row['id_jadwal']; ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirmHapus(event, '<?php echo addslashes($row['nama_hewan']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada jadwal pemeriksaan</p>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                                        <i class="fas fa-plus"></i> Tambah Jadwal
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
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Hewan <span class="text-danger">*</span></label>
                        <select name="id_hewan" class="form-select" required>
                            <option value="">Pilih Hewan</option>
                            <?php while ($h = mysqli_fetch_assoc($hewan_list)): ?>
                                <option value="<?php echo $h['id_hewan']; ?>"><?php echo htmlspecialchars($h['nama_hewan']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Dokter <span class="text-danger">*</span></label>
                        <select name="id_dokter" class="form-select" required>
                            <option value="">Pilih Dokter</option>
                            <?php while ($d = mysqli_fetch_assoc($dokter_list)): ?>
                                <option value="<?php echo $d['id_dokter']; ?>"><?php echo htmlspecialchars($d['nama_dokter']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pemeriksaan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Jam <span class="text-danger">*</span></label>
                        <input type="time" name="jam_pemeriksaan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Keluhan</label>
                        <textarea name="keluhan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status_pemeriksaan" class="form-select">
                            <option value="Menunggu">Menunggu</option>
                            <option value="Sedang Diperiksa">Sedang Diperiksa</option>
                            <option value="Selesai">Selesai</option>
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
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_jadwal" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Hewan <span class="text-danger">*</span></label>
                        <select name="id_hewan" id="edit_hewan" class="form-select" required>
                            <?php mysqli_data_seek($hewan_list, 0);
                            while ($h = mysqli_fetch_assoc($hewan_list)): ?>
                                <option value="<?php echo $h['id_hewan']; ?>"><?php echo htmlspecialchars($h['nama_hewan']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Dokter <span class="text-danger">*</span></label>
                        <select name="id_dokter" id="edit_dokter" class="form-select" required>
                            <?php mysqli_data_seek($dokter_list, 0);
                            while ($d = mysqli_fetch_assoc($dokter_list)): ?>
                                <option value="<?php echo $d['id_dokter']; ?>"><?php echo htmlspecialchars($d['nama_dokter']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pemeriksaan" id="edit_tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Jam <span class="text-danger">*</span></label>
                        <input type="time" name="jam_pemeriksaan" id="edit_jam" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Keluhan</label>
                        <textarea name="keluhan" id="edit_keluhan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status_pemeriksaan" id="edit_status" class="form-select">
                            <option value="Menunggu">Menunggu</option>
                            <option value="Sedang Diperiksa">Sedang Diperiksa</option>
                            <option value="Selesai">Selesai</option>
                        </select>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ============================================
// FUNGSI EDIT DATA
// ============================================
function editData(id, hewan, dokter, tanggal, jam, keluhan, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_hewan').value = hewan;
    document.getElementById('edit_dokter').value = dokter;
    document.getElementById('edit_tanggal').value = tanggal;
    document.getElementById('edit_jam').value = jam;
    document.getElementById('edit_keluhan').value = keluhan;
    document.getElementById('edit_status').value = status;
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
        text: "Jadwal pemeriksaan untuk hewan '" + nama + "' akan dihapus permanen!",
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