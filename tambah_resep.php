<?php
// ============================================
// FILE: tambah_resep.php
// FUNGSI: Form tambah resep obat
// ============================================

require_once 'database.php';

// Ambil data jadwal pemeriksaan yang sudah selesai atau sedang diperiksa
$jadwal_query = "SELECT j.*, h.nama_hewan, p.nama_pemilik 
                 FROM jadwal_pemeriksaan j
                 JOIN hewan h ON j.id_hewan = h.id_hewan
                 JOIN pemilik p ON h.id_pemilik = p.id_pemilik
                 WHERE j.status_pemeriksaan IN ('Selesai', 'Sedang Diperiksa')
                 ORDER BY j.tanggal_pemeriksaan DESC";
$jadwal_result = mysqli_query($koneksi, $jadwal_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jadwal = mysqli_real_escape_string($koneksi, $_POST['id_jadwal']);
    $id_hewan = mysqli_real_escape_string($koneksi, $_POST['id_hewan']);
    $obat = mysqli_real_escape_string($koneksi, $_POST['obat']);
    $dosis = mysqli_real_escape_string($koneksi, $_POST['dosis']);
    $aturan_pakai = mysqli_real_escape_string($koneksi, $_POST['aturan_pakai']);
    $durasi = mysqli_real_escape_string($koneksi, $_POST['durasi']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    
    $query = "INSERT INTO resep_obat (id_jadwal, id_hewan, obat, dosis, aturan_pakai, durasi, catatan) 
              VALUES ('$id_jadwal', '$id_hewan', '$obat', '$dosis', '$aturan_pakai', '$durasi', '$catatan')";
    
    // Update status resep_terbit di jadwal
    $update_jadwal = "UPDATE jadwal_pemeriksaan SET resep_terbit = 'Ya' WHERE id_jadwal = $id_jadwal";
    
    if (mysqli_query($koneksi, $query)) {
        mysqli_query($koneksi, $update_jadwal);
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Resep obat berhasil ditambahkan',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'resep.php';
            });
        </script>";
    } else {
        $error = "Gagal menambahkan resep: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Resep - Klinik Hewan</title>
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
                <h5 class="mb-0"><i class="fas fa-prescription-bottle me-2"></i> Tambah Resep Obat</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" id="formResep">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Pemeriksaan <span class="text-danger">*</span></label>
                            <select name="id_jadwal" id="id_jadwal" class="form-select" required>
                                <option value="">-- Pilih Jadwal Pemeriksaan --</option>
                                <?php while ($j = mysqli_fetch_assoc($jadwal_result)): ?>
                                    <option value="<?php echo $j['id_jadwal']; ?>" data-hewan="<?php echo $j['id_hewan']; ?>">
                                        <?php echo date('d/m/Y', strtotime($j['tanggal_pemeriksaan'])) . ' - ' . $j['nama_hewan'] . ' (' . $j['nama_pemilik'] . ')'; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Hewan</label>
                            <input type="text" name="id_hewan" id="id_hewan" class="form-control" readonly>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama Obat <span class="text-danger">*</span></label>
                            <input type="text" name="obat" class="form-control" required placeholder="Contoh: Amoxicillin, Paracetamol, dll">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Dosis</label>
                            <input type="text" name="dosis" class="form-control" placeholder="Contoh: 1x sehari, 500mg">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Durasi</label>
                            <input type="text" name="durasi" class="form-control" placeholder="Contoh: 3 hari, 1 minggu">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aturan Pakai</label>
                            <input type="text" name="aturan_pakai" class="form-control" placeholder="Contoh: Setelah makan, Pagi/Sore">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan khusus untuk pemilik hewan..."></textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Resep
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
<script>
// Auto-fill id_hewan ketika memilih jadwal
document.getElementById('id_jadwal').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var hewanId = selectedOption.getAttribute('data-hewan');
    document.getElementById('id_hewan').value = hewanId || '';
});
</script>
</body>
</html>