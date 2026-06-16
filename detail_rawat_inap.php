<?php
// ============================================
// FILE: detail_rawat_inap.php
// FUNGSI: Detail rawat inap dan perawatan harian
// ============================================

require_once 'database.php';

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

if ($id == 0) {
    echo "<script>alert('ID tidak valid!'); window.location.href='rawat_inap.php';</script>";
    exit();
}

// Query detail rawat inap
$query = "SELECT r.*, h.nama_hewan, h.jenis_hewan, p.nama_pemilik, p.no_telepon, d.nama_dokter 
          FROM rawat_inap r
          LEFT JOIN hewan h ON r.id_hewan = h.id_hewan
          LEFT JOIN pemilik p ON h.id_pemilik = p.id_pemilik
          LEFT JOIN dokter d ON r.id_dokter = d.id_dokter
          WHERE r.id_rawat = $id";
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='rawat_inap.php';</script>";
    exit();
}

$data = mysqli_fetch_assoc($result);

// Perawatan harian
$perawatan_query = "SELECT * FROM perawatan_harian WHERE id_rawat = $id ORDER BY tanggal DESC, jam DESC";
$perawatan_result = mysqli_query($koneksi, $perawatan_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rawat Inap - Klinik Hewan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>
    
    <div class="content-wrapper fade-in">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-hospital me-2"></i> Detail Rawat Inap</h5>
            </div>
            <div class="card-body">
                <!-- Informasi Hewan & Pemilik -->
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Hewan</strong></td>
                                <td>: <?php echo htmlspecialchars($data['nama_hewan'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Jenis</strong></td>
                                <td>: <?php echo htmlspecialchars($data['jenis_hewan'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Pemilik</strong></td>
                                <td>: <?php echo htmlspecialchars($data['nama_pemilik'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: <?php echo htmlspecialchars($data['no_telepon'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Dokter</strong></td>
                                <td>: <?php echo htmlspecialchars($data['nama_dokter'] ?? '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Tanggal Masuk</strong></td>
                                <td>: <?php echo date('d/m/Y', strtotime($data['tanggal_masuk'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Keluar</strong></td>
                                <td>: <?php echo $data['tanggal_keluar'] ? date('d/m/Y', strtotime($data['tanggal_keluar'])) : '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php
                                    $status_class = '';
                                    if ($data['status_rawat'] == 'Menunggu') $status_class = 'badge-menunggu';
                                    elseif ($data['status_rawat'] == 'Dirawat') $status_class = 'badge-diperiksa';
                                    elseif ($data['status_rawat'] == 'Selesai') $status_class = 'badge-selesai';
                                    else $status_class = 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo $data['status_rawat']; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Biaya</strong></td>
                                <td>: Rp <?php echo number_format($data['biaya'] ?? 0, 0, ',', '.'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Diagnosa & Tindakan -->
                <div class="row">
                    <div class="col-12">
                        <h6><strong>Diagnosa Awal:</strong></h6>
                        <p><?php echo nl2br(htmlspecialchars($data['diagnosa_awal'] ?? '-')); ?></p>
                    </div>
                    <?php if (!empty($data['diagnosa_akhir'])): ?>
                    <div class="col-12">
                        <h6><strong>Diagnosa Akhir:</strong></h6>
                        <p><?php echo nl2br(htmlspecialchars($data['diagnosa_akhir'])); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($data['tindakan'])): ?>
                    <div class="col-12">
                        <h6><strong>Tindakan:</strong></h6>
                        <p><?php echo nl2br(htmlspecialchars($data['tindakan'])); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($data['catatan'])): ?>
                    <div class="col-12">
                        <h6><strong>Catatan:</strong></h6>
                        <p><?php echo nl2br(htmlspecialchars($data['catatan'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Perawatan Harian -->
                <hr>
                <h6><i class="fas fa-notes-medical me-2"></i> Perawatan Harian</h6>
                <div class="table-responsive mt-3">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Aktivitas</th>
                                <th>Obat</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($perawatan_result && mysqli_num_rows($perawatan_result) > 0): ?>
                                <?php while ($p = mysqli_fetch_assoc($perawatan_result)): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($p['tanggal'])); ?></td>
                                    <td><?php echo $p['jam']; ?></td>
                                    <td><?php echo htmlspecialchars($p['aktivitas']); ?></td>
                                    <td><?php echo htmlspecialchars($p['obat']); ?></td>
                                    <td><?php echo htmlspecialchars($p['catatan_perawat']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada catatan perawatan harian</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3 d-flex gap-2">
                    <a href="rawat_inap.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="edit_rawat_inap.php?id=<?php echo $id; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button class="btn btn-danger" onclick="hapusData(<?php echo $id; ?>, '<?php echo $data['nama_hewan']; ?>')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function hapusData(id, nama) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data rawat inap untuk '" + nama + "' akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'hapus_rawat_inap.php?id=' + id;
        }
    });
}
</script>
</body>
</html>