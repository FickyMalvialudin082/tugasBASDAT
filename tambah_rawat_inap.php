<?php
// ============================================
// FILE: tambah_rawat_inap.php
// FUNGSI: Form tambah rawat inap
// ============================================

require_once 'database.php';

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil data hewan dan dokter untuk dropdown
$hewan_list = mysqli_query($koneksi, "SELECT * FROM hewan ORDER BY nama_hewan");
$dokter_list = mysqli_query($koneksi, "SELECT * FROM dokter ORDER BY nama_dokter");

// Inisialisasi variabel untuk mempertahankan nilai form
$id_hewan = '';
$id_dokter = '';
$tanggal_masuk = date('Y-m-d');
$diagnosa_awal = '';
$status_rawat = 'Dirawat';
$biaya = '';
$catatan = '';

// Proses Tambah Data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form (tidak diubah otomatis)
    $id_hewan = $_POST['id_hewan'];
    $id_dokter = $_POST['id_dokter'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $diagnosa_awal = mysqli_real_escape_string($koneksi, $_POST['diagnosa_awal']);
    $status_rawat = $_POST['status_rawat'];
    $biaya = !empty($_POST['biaya']) ? $_POST['biaya'] : 0;
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    
    // Validasi: pastikan semua field wajib terisi
    if (empty($id_hewan) || empty($id_dokter) || empty($tanggal_masuk) || empty($diagnosa_awal)) {
        $error = "Semua field wajib harus diisi!";
    } else {
        // Query INSERT (hanya field yang diisi, tidak ada yang diubah otomatis)
        $query = "INSERT INTO rawat_inap (
                    id_hewan, 
                    id_dokter, 
                    tanggal_masuk, 
                    diagnosa_awal, 
                    status_rawat, 
                    biaya, 
                    catatan
                  ) VALUES (
                    '$id_hewan', 
                    '$id_dokter', 
                    '$tanggal_masuk', 
                    '$diagnosa_awal', 
                    '$status_rawat', 
                    '$biaya', 
                    '$catatan'
                  )";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data rawat inap berhasil ditambahkan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'rawat_inap.php';
                });
            </script>";
            exit();
        } else {
            $error = "Gagal menambahkan data: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rawat Inap - Klinik Hewan</title>
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
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-hospital me-2"></i> Tambah Rawat Inap</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <!-- Hewan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hewan <span class="text-danger">*</span></label>
                            <select name="id_hewan" class="form-select" required>
                                <option value="">Pilih Hewan</option>
                                <?php while ($h = mysqli_fetch_assoc($hewan_list)): ?>
                                    <option value="<?php echo $h['id_hewan']; ?>" <?php echo $h['id_hewan'] == $id_hewan ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($h['nama_hewan']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Dokter -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dokter <span class="text-danger">*</span></label>
                            <select name="id_dokter" class="form-select" required>
                                <option value="">Pilih Dokter</option>
                                <?php while ($d = mysqli_fetch_assoc($dokter_list)): ?>
                                    <option value="<?php echo $d['id_dokter']; ?>" <?php echo $d['id_dokter'] == $id_dokter ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($d['nama_dokter']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Tanggal Masuk -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="<?php echo $tanggal_masuk; ?>" required>
                        </div>
                        
                        <!-- Status Rawat -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Rawat</label>
                            <select name="status_rawat" class="form-select">
                                <option value="Menunggu" <?php echo $status_rawat == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="Dirawat" <?php echo $status_rawat == 'Dirawat' ? 'selected' : ''; ?>>Dirawat</option>
                                <option value="Selesai" <?php echo $status_rawat == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                <option value="Dirujuk" <?php echo $status_rawat == 'Dirujuk' ? 'selected' : ''; ?>>Dirujuk</option>
                            </select>
                        </div>
                        
                        <!-- Diagnosa Awal -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Diagnosa Awal <span class="text-danger">*</span></label>
                            <textarea name="diagnosa_awal" class="form-control" rows="3" required><?php echo htmlspecialchars($diagnosa_awal); ?></textarea>
                        </div>
                        
                        <!-- Biaya -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Biaya Perawatan</label>
                            <input type="number" name="biaya" class="form-control" value="<?php echo $biaya; ?>" placeholder="0">
                        </div>
                        
                        <!-- Catatan -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"><?php echo htmlspecialchars($catatan); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Tombol -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
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
</body>
</html>