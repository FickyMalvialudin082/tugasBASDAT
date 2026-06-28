<?php
// ============================================
// FILE: detail_hewan.php
// FUNGSI: Menampilkan detail hewan + info pemilik
// ============================================

require_once 'database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    echo '<div class="alert alert-danger">ID tidak valid!</div>';
    exit();
}

// Ambil data hewan + pemilik
$query = "SELECT h.*, p.nama_pemilik, p.jenis_kelamin as jk_pemilik, p.no_telepon, p.alamat 
          FROM hewan h 
          JOIN pemilik p ON h.id_pemilik = p.id_pemilik 
          WHERE h.id_hewan = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo '<div class="alert alert-danger">Data hewan tidak ditemukan!</div>';
    exit();
}

// Ambil riwayat pemeriksaan hewan
$riwayat_query = "SELECT * FROM jadwal_pemeriksaan WHERE id_hewan = $id ORDER BY tanggal_pemeriksaan DESC LIMIT 5";
$riwayat_result = mysqli_query($koneksi, $riwayat_query);
?>

<div class="row">
    <!-- Foto Hewan -->
    <div class="col-md-4 text-center">
        <?php if ($data['foto'] && file_exists('uploads/' . $data['foto'])): ?>
            <img src="uploads/<?php echo $data['foto']; ?>" class="img-fluid rounded" style="max-height: 200px;" alt="Foto">
        <?php else: ?>
            <img src="assets/img/default-pet.png" class="img-fluid rounded" style="max-height: 200px;" alt="Default">
        <?php endif; ?>
    </div>
    
    <!-- Informasi Hewan -->
    <div class="col-md-8">
        <h6 class="border-bottom pb-2"><i class="fas fa-paw"></i> Informasi Hewan</h6>
        <table class="table table-borderless table-sm">
            <tr><td width="120"><strong>Nama Hewan</strong></td><td>: <?php echo htmlspecialchars($data['nama_hewan']); ?></td></tr>
            <tr><td><strong>Jenis</strong></td><td>: <?php echo htmlspecialchars($data['jenis_hewan']); ?></td></tr>
            <tr><td><strong>Ras</strong></td><td>: <?php echo htmlspecialchars($data['ras']); ?></td></tr>
            <tr><td><strong>Jenis Kelamin</strong></td><td>: <?php echo $data['jenis_kelamin']; ?></td></tr>
            <tr><td><strong>Tanggal Lahir</strong></td><td>: <?php echo $data['tanggal_lahir'] ? date('d/m/Y', strtotime($data['tanggal_lahir'])) : '-'; ?></td></tr>
            <tr><td><strong>Warna</strong></td><td>: <?php echo htmlspecialchars($data['warna']); ?></td></tr>
            <tr><td><strong>Keluhan</strong></td><td>: <?php echo nl2br(htmlspecialchars($data['keluhan'])); ?></td></tr>
        </table>
    </div>
</div>

<hr>

<!-- Informasi Pemilik -->
<div class="row">
    <div class="col-md-6">
        <h6 class="border-bottom pb-2"><i class="fas fa-user"></i> Informasi Pemilik</h6>
        <table class="table table-borderless table-sm">
            <tr><td width="120"><strong>Nama Pemilik</strong></td><td>: <?php echo htmlspecialchars($data['nama_pemilik']); ?></td></tr>
            <tr><td><strong>Jenis Kelamin</strong></td><td>: <?php echo $data['jk_pemilik']; ?></td></tr>
            <tr><td><strong>No Telepon</strong></td><td>: <?php echo htmlspecialchars($data['no_telepon']); ?></td></tr>
            <tr><td><strong>Alamat</strong></td><td>: <?php echo nl2br(htmlspecialchars($data['alamat'])); ?></td></tr>
        </table>
    </div>
    
    <!-- Riwayat Pemeriksaan -->
    <div class="col-md-6">
        <h6 class="border-bottom pb-2"><i class="fas fa-notes-medical"></i> Riwayat Pemeriksaan</h6>
        <?php if (mysqli_num_rows($riwayat_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = mysqli_fetch_assoc($riwayat_result)): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($r['tanggal_pemeriksaan'])); ?></td>
                            <td>
                                <span class="badge <?php echo $r['status_pemeriksaan'] == 'Menunggu' ? 'badge-menunggu' : ($r['status_pemeriksaan'] == 'Sedang Diperiksa' ? 'badge-diperiksa' : 'badge-selesai'); ?>">
                                    <?php echo $r['status_pemeriksaan']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-2">
                <i class="fas fa-inbox"></i> Belum ada riwayat pemeriksaan
            </p>
        <?php endif; ?>
    </div>
</div>