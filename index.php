<?php
// ============================================
// FILE: index.php (Dashboard)
// FUNGSI: Halaman utama dengan statistik dan grafik
// ============================================

require_once 'database.php';

// ============================================
// DATA UNTUK GRAFIK 1: Populasi Hewan per Jenis
// ============================================
$query_jenis = "SELECT jenis_hewan, COUNT(*) as jumlah 
                FROM hewan 
                WHERE jenis_hewan IS NOT NULL AND jenis_hewan != ''
                GROUP BY jenis_hewan 
                ORDER BY jumlah DESC";
$result_jenis = mysqli_query($koneksi, $query_jenis);

$jenis_hewan = [];
$jumlah_hewan = [];
while ($row = mysqli_fetch_assoc($result_jenis)) {
    $jenis_hewan[] = $row['jenis_hewan'];
    $jumlah_hewan[] = $row['jumlah'];
}

// ============================================
// DATA UNTUK GRAFIK 2: Kunjungan per Bulan (6 bulan terakhir)
// ============================================
$query_kunjungan = "SELECT tanggal_pemeriksaan 
                    FROM jadwal_pemeriksaan 
                    WHERE tanggal_pemeriksaan >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    ORDER BY tanggal_pemeriksaan ASC";
$result_kunjungan = mysqli_query($koneksi, $query_kunjungan);

$kunjungan_per_bulan = [];
while ($row = mysqli_fetch_assoc($result_kunjungan)) {
    $bulan = date('F', strtotime($row['tanggal_pemeriksaan']));
    if (!isset($kunjungan_per_bulan[$bulan])) {
        $kunjungan_per_bulan[$bulan] = 0;
    }
    $kunjungan_per_bulan[$bulan]++;
}

$bulan_kunjungan = array_keys($kunjungan_per_bulan);
$jumlah_kunjungan = array_values($kunjungan_per_bulan);

// ============================================
// STATISTIK CARD
// ============================================
$total_hewan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM hewan"))['total'];
$total_pemilik = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pemilik"))['total'];
$total_dokter = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM dokter"))['total'];
$total_jadwal = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jadwal_pemeriksaan"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Klinik Hewan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Chart.js untuk grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>
    
    <div class="content-wrapper fade-in">
        <!-- Welcome Section -->
        <div class="alert alert-info alert-dismissible fade show mb-4" style="background: linear-gradient(135deg, #1A312C 0%, #428475 100%); color: white; border: none;">
            <i class="fas fa-hand-peace"></i> 
            Selamat datang di <strong>Sistem Manajemen Klinik Hewan</strong>!
            <p class="mb-0 mt-1 small">
                <i class="fas fa-calendar-alt"></i> Hari ini: <?php echo date('l, d F Y'); ?>
            </p>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Hewan</p>
                                <h2 class="mb-0"><?php echo number_format($total_hewan); ?></h2>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-dog"></i>
                            </div>
                        </div>
                        <small class="text-success mt-2 d-block">
                            <i class="fas fa-paw"></i> Pasien terdaftar
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Pemilik</p>
                                <h2 class="mb-0"><?php echo number_format($total_pemilik); ?></h2>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <small class="text-success mt-2 d-block">
                            <i class="fas fa-user-plus"></i> Terdaftar
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Dokter</p>
                                <h2 class="mb-0"><?php echo number_format($total_dokter); ?></h2>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                        <small class="text-success mt-2 d-block">
                            <i class="fas fa-stethoscope"></i> Praktik
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Jadwal</p>
                                <h2 class="mb-0"><?php echo number_format($total_jadwal); ?></h2>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <small class="text-success mt-2 d-block">
                            <i class="fas fa-clock"></i> Pemeriksaan
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ============================================ -->
        <!-- GRAFIK 1 & 2 (2 KOLOM) -->
        <!-- ============================================ -->
        <div class="row mb-4">
            <!-- Grafik 1: Populasi Hewan per Jenis -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-2"></i> Populasi Hewan per Jenis
                    </div>
                    <div class="card-body">
                        <?php if (count($jenis_hewan) > 0): ?>
                            <canvas id="jenisHewanChart" height="250"></canvas>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data hewan</p>
                                <a href="tambah_hewan.php" class="btn btn-sm btn-primary">Tambah Hewan</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Grafik 2: Kunjungan per Bulan -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i> Kunjungan Pemeriksaan (6 Bulan Terakhir)
                    </div>
                    <div class="card-body">
                        <?php if (count($bulan_kunjungan) > 0): ?>
                            <canvas id="kunjunganChart" height="250"></canvas>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data kunjungan</p>
                                <a href="jadwal.php" class="btn btn-sm btn-primary">Tambah Jadwal</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Data -->
        <div class="row">
            <!-- Recent Schedules -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt me-2"></i> Pemeriksaan Terbaru
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Hewan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT j.*, h.nama_hewan 
                                              FROM jadwal_pemeriksaan j
                                              JOIN hewan h ON j.id_hewan = h.id_hewan
                                              ORDER BY j.tanggal_pemeriksaan DESC
                                              LIMIT 5";
                                    $result = mysqli_query($koneksi, $query);
                                    if (mysqli_num_rows($result) > 0):
                                    while ($row = mysqli_fetch_assoc($result)):
                                        $status_class = '';
                                        if ($row['status_pemeriksaan'] == 'Menunggu') $status_class = 'badge-menunggu';
                                        elseif ($row['status_pemeriksaan'] == 'Sedang Diperiksa') $status_class = 'badge-diperiksa';
                                        else $status_class = 'badge-selesai';
                                    ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_hewan']); ?></td>
                                        <td><span class="badge <?php echo $status_class; ?>"><?php echo $row['status_pemeriksaan']; ?></span></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <i class="fas fa-calendar-alt fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Belum ada jadwal pemeriksaan</p>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- New Pets -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-paw me-2"></i> Hewan Baru Terdaftar
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr><th>Nama Hewan</th><th>Jenis</th><th>Pemilik</th><th>Tanggal</th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT h.*, p.nama_pemilik 
                                              FROM hewan h
                                              JOIN pemilik p ON h.id_pemilik = p.id_pemilik
                                              ORDER BY h.created_at DESC
                                              LIMIT 10";
                                    $result = mysqli_query($koneksi, $query);
                                    if (mysqli_num_rows($result) > 0):
                                    while ($row = mysqli_fetch_assoc($result)):
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nama_hewan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['jenis_hewan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_pemilik']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="fas fa-dog fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Belum ada data hewan</p>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

<!-- Script untuk Grafik -->
<script>
// Grafik 1: Populasi Hewan per Jenis (Bar Chart)
<?php if (count($jenis_hewan) > 0): ?>
const ctx1 = document.getElementById('jenisHewanChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($jenis_hewan); ?>,
        datasets: [{
            label: 'Jumlah Hewan',
            data: <?php echo json_encode($jumlah_hewan); ?>,
            backgroundColor: [
                'rgba(26, 49, 44, 0.8)',
                'rgba(66, 132, 117, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: '#1A312C',
            borderWidth: 1,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                backgroundColor: '#1A312C',
                titleColor: '#fff',
                bodyColor: '#fff'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0
                }
            }
        }
    }
});
<?php endif; ?>

// Grafik 2: Kunjungan per Bulan (Line Chart)
<?php if (count($bulan_kunjungan) > 0): ?>
const ctx2 = document.getElementById('kunjunganChart').getContext('2d');
new Chart(ctx2, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($bulan_kunjungan); ?>,
        datasets: [{
            label: 'Jumlah Kunjungan',
            data: <?php echo json_encode($jumlah_kunjungan); ?>,
            backgroundColor: 'rgba(26, 49, 44, 0.1)',
            borderColor: '#1A312C',
            borderWidth: 3,
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#428475',
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                backgroundColor: '#1A312C',
                titleColor: '#fff',
                bodyColor: '#fff'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0
                }
            }
        }
    }
});
<?php endif; ?>
</script>
</body>
</html>