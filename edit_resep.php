<?php
// ============================================
// FILE: edit_resep.php
// FUNGSI: Form edit resep obat
// ============================================

require_once 'database.php';

$id = $_GET['id'];
$query = "SELECT * FROM resep_obat WHERE id_resep = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $obat = $_POST['obat'];
    $dosis = $_POST['dosis'];
    $aturan_pakai = $_POST['aturan_pakai'];
    $durasi = $_POST['durasi'];
    $catatan = $_POST['catatan'];
    
    $update = "UPDATE resep_obat SET 
               obat='$obat', 
               dosis='$dosis', 
               aturan_pakai='$aturan_pakai', 
               durasi='$durasi', 
               catatan='$catatan' 
               WHERE id_resep=$id";
    
    if (mysqli_query($koneksi, $update)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Resep berhasil diupdate',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'resep.php';
            });
        </script>";
    } else {
        $error = "Gagal mengupdate resep: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resep - Klinik Hewan</title>
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
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Resep Obat</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama Obat <span class="text-danger">*</span></label>
                            <input type="text" name="obat" class="form-control" value="<?php echo $data['obat']; ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Dosis</label>
                            <input type="text" name="dosis" class="form-control" value="<?php echo $data['dosis']; ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Durasi</label>
                            <input type="text" name="durasi" class="form-control" value="<?php echo $data['durasi']; ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aturan Pakai</label>
                            <input type="text" name="aturan_pakai" class="form-control" value="<?php echo $data['aturan_pakai']; ?>">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="3"><?php echo $data['catatan']; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Resep
                        </button>
                        <a href="resep.php" class="btn btn-secondary">
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