<?php
// ============================================
// FILE: detail_pemilik.php
// FUNGSI: Menampilkan detail pemilik + daftar hewan
// ============================================

require_once 'database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    echo '<div class="alert alert-danger">ID tidak valid!</div>';
    exit();
}

// Ambil data pemilik
$query = "SELECT * FROM pemilik WHERE id_pemilik = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo '<div class="alert alert-danger">Data pemilik tidak ditemukan!</div>';
    exit();
}

// Ambil data hewan milik pemilik
$hewan_query = "SELECT * FROM hewan WHERE id_pemilik = $id ORDER BY nama_hewan";
$hewan_result = mysqli_query($koneksi, $hewan_query);
?>

<div class="row">
    <!-- Informasi Pemilik -->
    <div class="col-md-6">
        <h6 class="border-bottom pb-2"><i class="fas fa-user"></i> Informasi Pemilik</h6>
        <table class="table table-borderless table-sm">
            <tr>
                <td width="120"><strong>Nama</strong></td>
                <td>: <?php echo htmlspecialchars($data['nama_pemilik']); ?></td>
            </tr>
            <tr>
                <td><strong>Jenis Kelamin</strong></td>
                <td>: <?php echo $data['jenis_kelamin']; ?></td>
            </tr>
            <tr>
                <td><strong>No Telepon</strong></td>
                <td>: <?php echo htmlspecialchars($data['no_telepon']); ?></td>
            </tr>
            <tr>
                <td><strong>Alamat</strong></td>
                <td>: <?php echo nl2br(htmlspecialchars($data['alamat'])); ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal Daftar</strong></td>
                <td>: <?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Daftar Hewan -->
    <div class="col-md-6">
        <h6 class="border-bottom pb-2"><i class="fas fa-paw"></i> Daftar Hewan</h6>
        <?php if (mysqli_num_rows($hewan_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Ras</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($h = mysqli_fetch_assoc($hewan_result)): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($h['nama_hewan']); ?></td>
                            <td><?php echo htmlspecialchars($h['jenis_hewan']); ?></td>
                            <td><?php echo htmlspecialchars($h['ras']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-3">
                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                Belum ada hewan terdaftar untuk pemilik ini.
            </p>
        <?php endif; ?>
    </div>
</div>