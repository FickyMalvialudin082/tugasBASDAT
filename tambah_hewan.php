<?php
// ============================================
// FILE: tambah_hewan.php
// FUNGSI: Form tambah data hewan
// ============================================

require_once 'database.php';

// Ambil data pemilik untuk dropdown
$pemilik_query = "SELECT * FROM pemilik ORDER BY nama_pemilik";
$pemilik_result = mysqli_query($koneksi, $pemilik_query);

// Proses tambah data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pemilik = mysqli_real_escape_string($koneksi, $_POST['id_pemilik']);
    $nama_hewan = mysqli_real_escape_string($koneksi, $_POST['nama_hewan']);
    $jenis_hewan = mysqli_real_escape_string($koneksi, $_POST['jenis_hewan']);
    $ras = mysqli_real_escape_string($koneksi, $_POST['ras']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $warna = mysqli_real_escape_string($koneksi, $_POST['warna']);
    $keluhan = mysqli_real_escape_string($koneksi, $_POST['keluhan']);
    
    // Upload foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file_size = $_FILES['foto']['size'];
        
        if (in_array($ext, $allowed) && $file_size <= 2 * 1024 * 1024) {
            $foto = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $foto);
        }
    }
    
    $query = "INSERT INTO hewan (id_pemilik, nama_hewan, jenis_hewan, ras, jenis_kelamin, tanggal_lahir, warna, keluhan, foto) 
              VALUES ('$id_pemilik', '$nama_hewan', '$jenis_hewan', '$ras', '$jenis_kelamin', '$tanggal_lahir', '$warna', '$keluhan', '$foto')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data hewan berhasil ditambahkan',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'hewan.php';
            });
        </script>";
    } else {
        $error = "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Hewan - Klinik Hewan</title>
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
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i> Tambah Data Hewan</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Hewan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_hewan" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pemilik <span class="text-danger">*</span></label>
                            <select name="id_pemilik" class="form-select" required>
                                <option value="">Pilih Pemilik</option>
                                <?php while ($p = mysqli_fetch_assoc($pemilik_result)): ?>
                                    <option value="<?php echo $p['id_pemilik']; ?>"><?php echo htmlspecialchars($p['nama_pemilik']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Hewan</label>
                            <input type="text" name="jenis_hewan" class="form-control" placeholder="Contoh: Kucing, Anjing, dll">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ras</label>
                            <input type="text" name="ras" class="form-control" placeholder="Contoh: Persia, Golden Retriever">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="Jantan">Jantan</option>
                                <option value="Betina">Betina</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Warna</label>
                            <input type="text" name="warna" class="form-control" placeholder="Contoh: Putih, Coklat, Hitam">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Foto Hewan</label>
                            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/jpg">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Keluhan</label>
                            <textarea name="keluhan" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="hewan.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>