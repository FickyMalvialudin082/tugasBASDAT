<?php
// ============================================
// FILE: backup.php
// FUNGSI: Manajemen Backup Database
// ============================================

session_start();

// ============ PERBAIKAN PATH ============
// Gunakan __DIR__ untuk menghindari error path
require_once __DIR__ . '/config/database.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// ============ KONFIGURASI ============
$backup_dir = __DIR__ . '/backup/database/';  // Gunakan path absolut
$max_backup_files = 10;

// Buat folder jika belum ada
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// ============ FUNGSI ============

// Fungsi format ukuran file
function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

// Fungsi backup database
function backupDatabase($pdo, $backup_dir) {
    // Ambil daftar tabel
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    if (empty($tables)) {
        throw new Exception("Tidak ada tabel dalam database!");
    }
    
    // Buat nama file
    $filename = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $handle = fopen($filename, 'w');
    
    if (!$handle) {
        throw new Exception("Gagal membuat file backup!");
    }
    
    // Header backup
    fwrite($handle, "-- ============================================\n");
    fwrite($handle, "-- Backup Database Klinik Hewan\n");
    fwrite($handle, "-- Tanggal: " . date('Y-m-d H:i:s') . "\n");
    fwrite($handle, "-- Host: " . DB_HOST . "\n");
    fwrite($handle, "-- Database: " . DB_NAME . "\n");
    fwrite($handle, "-- ============================================\n\n");
    fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
    fwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
    fwrite($handle, "SET AUTOCOMMIT = 0;\n");
    fwrite($handle, "START TRANSACTION;\n\n");
    
    // Proses setiap tabel
    foreach ($tables as $table) {
        // Drop table
        fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
        
        // Create table
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $create = $stmt->fetch(PDO::FETCH_ASSOC);
        fwrite($handle, $create['Create Table'] . ";\n\n");
        
        // Insert data
        $data = $pdo->query("SELECT * FROM `$table`");
        if ($data->rowCount() > 0) {
            fwrite($handle, "INSERT INTO `$table` VALUES \n");
            $rows = [];
            while ($row = $data->fetch(PDO::FETCH_NUM)) {
                $values = array_map(function($val) {
                    if ($val === null) return 'NULL';
                    return "'" . addslashes($val) . "'";
                }, $row);
                $rows[] = "(" . implode(',', $values) . ")";
            }
            fwrite($handle, implode(",\n", $rows) . ";\n\n");
        }
    }
    
    // Footer
    fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
    fwrite($handle, "COMMIT;\n");
    fclose($handle);
    
    // Kompres file dengan ZIP
    if (extension_loaded('zip')) {
        $zip = new ZipArchive();
        $zip_filename = str_replace('.sql', '.zip', $filename);
        if ($zip->open($zip_filename, ZipArchive::CREATE) === true) {
            $zip->addFile($filename, basename($filename));
            $zip->close();
            unlink($filename); // Hapus file SQL asli
            return $zip_filename;
        }
    }
    
    return $filename;
}

// ============ PROSES ACTION ============
$message = '';
$message_type = '';

if (isset($_GET['action'])) {
    // Action: Buat Backup
    if ($_GET['action'] == 'backup') {
        try {
            $backup_file = backupDatabase($pdo, $backup_dir);
            $message = "✅ Backup berhasil dibuat: " . basename($backup_file);
            $message_type = 'success';
            
            // Hapus backup lama (jika melebihi batas)
            $files = glob($backup_dir . 'backup_*.{sql,zip}', GLOB_BRACE);
            if (count($files) > $max_backup_files) {
                usort($files, function($a, $b) {
                    return filemtime($a) - filemtime($b);
                });
                $files_to_delete = array_slice($files, 0, count($files) - $max_backup_files);
                foreach ($files_to_delete as $file) {
                    unlink($file);
                }
            }
        } catch (Exception $e) {
            $message = "❌ Gagal backup: " . $e->getMessage();
            $message_type = 'danger';
        }
    }
    
    // Action: Hapus Backup
    if ($_GET['action'] == 'delete' && isset($_GET['file'])) {
        // Validasi keamanan: cegah path traversal
        $file = basename($_GET['file']);
        $file_path = $backup_dir . $file;
        
        if (file_exists($file_path) && is_file($file_path)) {
            if (unlink($file_path)) {
                $message = "✅ File backup berhasil dihapus";
                $message_type = 'success';
            } else {
                $message = "❌ Gagal menghapus file";
                $message_type = 'danger';
            }
        } else {
            $message = "❌ File tidak ditemukan";
            $message_type = 'danger';
        }
    }
    
    // Action: Download Backup
    if ($_GET['action'] == 'download' && isset($_GET['file'])) {
        $file = basename($_GET['file']);
        $file_path = $backup_dir . $file;
        
        if (file_exists($file_path) && is_file($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            die("File tidak ditemukan!");
        }
    }
}

// ============ AMBIL DATA ============

// Daftar file backup
$backup_files = glob($backup_dir . 'backup_*.{sql,zip}', GLOB_BRACE);
$backup_files = array_map('basename', $backup_files);
rsort($backup_files);

// Informasi database
try {
    $total_tables = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'")->fetchColumn();
    
    $total_data = 0;
    $tables_list = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables_list as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $total_data += $count;
    }
} catch (Exception $e) {
    $total_tables = 0;
    $total_data = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database - Klinik Hewan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid px-4 py-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-database me-2"></i> Backup Database</h4>
            <div>
                <a href="?action=backup" class="btn btn-primary me-2" onclick="return confirmBackup()">
                    <i class="fas fa-plus-circle me-2"></i> Buat Backup Baru
                </a>
                <a href="restore.php" class="btn btn-success">
                    <i class="fas fa-upload me-2"></i> Restore
                </a>
            </div>
        </div>
        
        <!-- Alert Info -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Backup akan menyimpan semua data database ke dalam file. 
            Maksimal <strong><?php echo $max_backup_files; ?></strong> file backup akan disimpan.
            <?php if (extension_loaded('zip')): ?>
                <span class="badge bg-success ms-2"><i class="fas fa-check"></i> ZIP Support</span>
            <?php else: ?>
                <span class="badge bg-warning ms-2"><i class="fas fa-exclamation-triangle"></i> ZIP Tidak Aktif</span>
            <?php endif; ?>
        </div>
        
        <!-- Message -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Tabel Backup -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> Daftar Backup</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama File</th>
                                        <th style="width: 150px;">Tanggal</th>
                                        <th style="width: 100px;">Ukuran</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($backup_files) > 0): ?>
                                        <?php $no = 1; foreach ($backup_files as $file): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($file); ?>
                                                <?php if (strpos($file, '.zip') !== false): ?>
                                                    <span class="badge bg-info">ZIP</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                preg_match('/backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $file, $matches);
                                                if (isset($matches[1])) {
                                                    echo date('d/m/Y H:i:s', strtotime(str_replace('_', ' ', $matches[1])));
                                                } else {
                                                    echo date('d/m/Y H:i:s', filemtime($backup_dir . $file));
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo formatFileSize(filesize($backup_dir . $file)); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?action=download&file=<?php echo urlencode($file); ?>" class="btn btn-info" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="btn btn-danger" onclick="hapusBackup('<?php echo $file; ?>')" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-database fa-3x text-muted"></i>
                                                <p class="text-muted mt-2">Belum ada backup yang tersimpan</p>
                                                <a href="?action=backup" class="btn btn-sm btn-primary" onclick="return confirmBackup()">
                                                    <i class="fas fa-plus me-2"></i> Buat Backup Pertama
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        Total: <strong><?php echo count($backup_files); ?></strong> file backup
                        <?php if (count($backup_files) > 0): ?>
                            | Total ukuran: <strong><?php 
                            $total_size = array_sum(array_map('filesize', array_map(function($f) use ($backup_dir) { 
                                return $backup_dir . $f; 
                            }, $backup_files)));
                            echo formatFileSize($total_size);
                            ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Database -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Info Database</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Database</strong></td>
                                <td><code><?php echo DB_NAME; ?></code></td>
                            </tr>
                            <tr>
                                <td><strong>Host</strong></td>
                                <td><code><?php echo DB_HOST; ?></code></td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah Tabel</strong></td>
                                <td><span class="badge bg-primary"><?php echo number_format($total_tables); ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Total Data</strong></td>
                                <td><span class="badge bg-success"><?php echo number_format($total_data); ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Folder Backup</strong></td>
                                <td><code><?php echo str_replace(__DIR__, '', $backup_dir); ?></code></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Tips -->
                <div class="card mt-3 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i> Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Backup otomatis via cron: <code>backup_schedule.php</code>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Maksimal <strong><?php echo $max_backup_files; ?></strong> file tersimpan
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                File backup ada di: <code>backup/database/</code>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Konfirmasi membuat backup
function confirmBackup() {
    Swal.fire({
        title: 'Buat Backup Baru?',
        text: 'Proses backup akan menyimpan semua data database.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1E3A5F',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Backup!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?action=backup';
        }
    });
    return false;
}

// Konfirmasi hapus backup
function hapusBackup(filename) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "File backup '" + filename + "' akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?action=delete&file=' + encodeURIComponent(filename);
        }
    });
}

// Auto hide alert setelah 5 detik
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            setTimeout(function() {
                bsAlert.close();
            }, 5000);
        });
    }, 1000);
});
</script>
</body>
</html> 