<?php
// ============================================
// FILE: edit_rawat_inap.php
// ============================================

// Aktifkan error reporting (untuk debugging, bisa dihapus nanti)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal!");
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    echo "<script>alert('ID tidak valid!'); window.location.href='rawat_inap.php';</script>";
    exit();
}

// Ambil data rawat inap
$query = "SELECT * FROM rawat_inap WHERE id_rawat = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='rawat_inap.php';</script>";
    exit();
}

// Ambil data dropdown
$hewan_list = mysqli_query($koneksi, "SELECT * FROM hewan ORDER BY nama_hewan");
$dokter_list = mysqli_query($koneksi, "SELECT * FROM dokter ORDER BY nama_dokter");

// Variabel untuk menyimpan status notifikasi
$notification = null;

// Proses Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_hewan = (int)$_POST['id_hewan'];
    $id_dokter = (int)$_POST['id_dokter'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $tanggal_keluar = !empty($_POST['tanggal_keluar']) ? "'" . $_POST['tanggal_keluar'] . "'" : "NULL";
    $diagnosa_awal = mysqli_real_escape_string($koneksi, $_POST['diagnosa_awal']);
    $diagnosa_akhir = mysqli_real_escape_string($koneksi, $_POST['diagnosa_akhir'] ?? '');
    $tindakan = mysqli_real_escape_string($koneksi, $_POST['tindakan'] ?? '');
    $status_rawat = $_POST['status_rawat'];
    $biaya = !empty($_POST['biaya']) ? (float)$_POST['biaya'] : 0;
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan'] ?? '');
    
    $query_update = "UPDATE rawat_inap SET 
              id_hewan = $id_hewan,
              id_dokter = $id_dokter,
              tanggal_masuk = '$tanggal_masuk',
              tanggal_keluar = $tanggal_keluar,
              diagnosa_awal = '$diagnosa_awal',
              diagnosa_akhir = '$diagnosa_akhir',
              tindakan = '$tindakan',
              status_rawat = '$status_rawat',
              biaya = $biaya,
              catatan = '$catatan'
              WHERE id_rawat = $id";
    
    if (mysqli_query($koneksi, $query_update)) {
        // Sukses - set notifikasi sukses
        $notification = [
            'type' => 'success',
            'message' => 'Data rawat inap berhasil diupdate!',
            'redirect' => 'rawat_inap.php'
        ];
    } else {
        // Gagal - set notifikasi error
        $notification = [
            'type' => 'error',
            'message' => 'Gagal mengupdate data: ' . mysqli_error($koneksi)
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rawat Inap - Klinik Hewan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>
    
    <div class="content-wrapper fade-in">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Rawat Inap</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <!-- Hewan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hewan <span class="text-danger">*</span></label>
                            <select name="id_hewan" class="form-control" required>
                                <option value="">Pilih Hewan</option>
                                <?php while ($h = mysqli_fetch_assoc($hewan_list)): ?>
                                    <option value="<?php echo $h['id_hewan']; ?>" <?php echo $h['id_hewan'] == $data['id_hewan'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($h['nama_hewan']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Dokter -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dokter <span class="text-danger">*</span></label>
                            <select name="id_dokter" class="form-control" required>
                                <option value="">Pilih Dokter</option>
                                <?php while ($d = mysqli_fetch_assoc($dokter_list)): ?>
                                    <option value="<?php echo $d['id_dokter']; ?>" <?php echo $d['id_dokter'] == $data['id_dokter'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($d['nama_dokter']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Tanggal Masuk -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="<?php echo $data['tanggal_masuk']; ?>" required>
                        </div>
                        
                        <!-- Tanggal Keluar -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Keluar</label>
                            <input type="date" name="tanggal_keluar" class="form-control" value="<?php echo $data['tanggal_keluar']; ?>">
                            <small class="text-muted">Kosongkan jika belum keluar</small>
                        </div>
                        
                        <!-- Diagnosa Awal -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Diagnosa Awal <span class="text-danger">*</span></label>
                            <textarea name="diagnosa_awal" class="form-control" rows="2" required><?php echo htmlspecialchars($data['diagnosa_awal'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- Diagnosa Akhir -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Diagnosa Akhir</label>
                            <textarea name="diagnosa_akhir" class="form-control" rows="2"><?php echo htmlspecialchars($data['diagnosa_akhir'] ?? ''); ?></textarea>
                            <small class="text-muted">Diisi setelah pemeriksaan selesai</small>
                        </div>
                        
                        <!-- Tindakan -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Tindakan</label>
                            <textarea name="tindakan" class="form-control" rows="2"><?php echo htmlspecialchars($data['tindakan'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Rawat</label>
                            <select name="status_rawat" class="form-control">
                                <option value="Menunggu" <?php echo $data['status_rawat'] == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="Dirawat" <?php echo $data['status_rawat'] == 'Dirawat' ? 'selected' : ''; ?>>Dirawat</option>
                                <option value="Selesai" <?php echo $data['status_rawat'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                <option value="Dirujuk" <?php echo $data['status_rawat'] == 'Dirujuk' ? 'selected' : ''; ?>>Dirujuk</option>
                            </select>
                        </div>
                        
                        <!-- Biaya -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Biaya Perawatan</label>
                            <input type="number" name="biaya" class="form-control" value="<?php echo $data['biaya']; ?>" placeholder="0">
                        </div>
                        
                        <!-- Catatan -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"><?php echo htmlspecialchars($data['catatan'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Tombol -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="rawat_inap.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($notification): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '<?php echo $notification['type']; ?>',
            title: '<?php echo $notification['type'] == 'success' ? 'Berhasil!' : 'Gagal!'; ?>',
            text: '<?php echo addslashes($notification['message']); ?>',
            <?php if (isset($notification['redirect'])): ?>
            showConfirmButton: true,
            confirmButtonText: 'OK',
            timer: 2000
            <?php else: ?>
            showConfirmButton: true,
            confirmButtonText: 'OK'
            <?php endif; ?>
        }).then((result) => {
            <?php if (isset($notification['redirect'])): ?>
            if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                window.location.href = '<?php echo $notification['redirect']; ?>';
            }
            <?php endif; ?>
        });
    });
</script>
<?php endif; ?>
</body>
</html>