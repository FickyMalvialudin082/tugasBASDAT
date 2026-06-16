<?php
// ============================================
// FILE: edit_hewan.php
// FUNGSI: Form edit data hewan
// ============================================

require_once 'database.php';

$id = $_GET['id'];
$query = "SELECT * FROM hewan WHERE id_hewan = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

$pemilik_query = "SELECT * FROM pemilik ORDER BY nama_pemilik";
$pemilik_result = mysqli_query($koneksi, $pemilik_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pemilik = $_POST['id_pemilik'];
    $nama_hewan = $_POST['nama_hewan'];
    $jenis_hewan = $_POST['jenis_hewan'];
    $ras = $_POST['ras'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $warna = $_POST['warna'];
    $keluhan = $_POST['keluhan'];
    $foto_lama = $data['foto'];
    
    // Upload foto baru jika ada
    $foto = $foto_lama;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file_size = $_FILES['foto']['size'];
        
        if (in_array($ext, $allowed) && $file_size <= 2 * 1024 * 1024) {
            // Hapus foto lama
            if ($foto_lama && file_exists('uploads/' . $foto_lama)) {
                unlink('uploads/' . $foto_lama);
            }
            $foto = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $foto);
        }
    }
    
    $query = "UPDATE hewan SET 
              id_pemilik='$id_pemilik',
              nama_hewan='$nama_hewan',
              jenis_hewan='$jenis_hewan',
              ras='$ras',
              jenis_kelamin='$jenis_kelamin',
              tanggal_lahir='$tanggal_lahir',
              warna='$warna',
              keluhan='$keluhan',
              foto='$foto'
              WHERE id_hewan=$id";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data hewan berhasil diupdate',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'hewan.php';
            });
        </script>";
    } else {
        $error = "Gagal mengupdate data: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hewan - Klinik Hewan</title>
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
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Data Hewan</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Hewan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_hewan" class="form-control" value="<?php echo $data['nama_hewan']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pemilik <span class="text-danger">*</span></label>
                            <select name="id_pemilik" class="form-select" required>
                                <?php while ($p = mysqli_fetch_assoc($pemilik_result)): ?>
                                    <option value="<?php echo $p['id_pemilik']; ?>" <?php echo $p['id_pemilik'] == $data['id_pemilik'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p['nama_pemilik']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Hewan</label>
                            <input type="text" name="jenis_hewan" class="form-control" value="<?php echo $data['jenis_hewan']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ras</label>
                            <input type="text" name="ras" class="form-control" value="<?php echo $data['ras']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="Jantan" <?php echo $data['jenis_kelamin'] == 'Jantan' ? 'selected' : ''; ?>>Jantan</option>
                                <option value="Betina" <?php echo $data['jenis_kelamin'] == 'Betina' ? 'selected' : ''; ?>>Betina</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $data['tanggal_lahir']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Warna</label>
                            <input type="text" name="warna" class="form-control" value="<?php echo $data['warna']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Foto Hewan</label>
                            <?php if ($data['foto'] && file_exists('uploads/' . $data['foto'])): ?>
                                <div class="mb-2">
                                    <img src="uploads/<?php echo $data['foto']; ?>" class="thumbnail" alt="Foto">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/jpg">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Keluhan</label>
                            <textarea name="keluhan" class="form-control" rows="3"><?php echo $data['keluhan']; ?></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
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