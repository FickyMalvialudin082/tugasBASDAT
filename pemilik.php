<?php
require_once 'database.php';

// Proses tambah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $jk = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $telp = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $query = "INSERT INTO pemilik (nama_pemilik, jenis_kelamin, no_telepon, alamat) 
              VALUES ('$nama', '$jk', '$telp', '$alamat')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data pemilik berhasil ditambahkan',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'pemilik.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menambahkan data: " . mysqli_error($koneksi) . "'
            }).then(() => {
                window.location.href = 'pemilik.php';
            });
        </script>";
    }
    exit();
}

// Proses edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = (int)$_POST['id_pemilik'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $jk = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $telp = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $query = "UPDATE pemilik SET 
              nama_pemilik='$nama', 
              jenis_kelamin='$jk', 
              no_telepon='$telp', 
              alamat='$alamat' 
              WHERE id_pemilik=$id";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data pemilik berhasil diupdate',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'pemilik.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal mengupdate data: " . mysqli_error($koneksi) . "'
            }).then(() => {
                window.location.href = 'pemilik.php';
            });
        </script>";
    }
    exit();
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    // Cek apakah pemilik memiliki hewan
    $cek = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM hewan WHERE id_pemilik = $id");
    $data = mysqli_fetch_assoc($cek);

    if ($data['total'] > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Hapus!',
                text: 'Pemilik ini memiliki " . $data['total'] . " data hewan. Hapus data hewan terlebih dahulu!'
            }).then(() => {
                location.href='pemilik.php';
            });
        </script>";
        exit();
    }

    // Hapus data pemilik
    mysqli_query($koneksi, "DELETE FROM pemilik WHERE id_pemilik = $id");

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'Data pemilik berhasil dihapus',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            location.href='pemilik.php';
        });
    </script>";
    exit();
}

// Ambil data
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$where = $search ? "WHERE nama_pemilik LIKE '%$search%'" : "";
$query = "SELECT * FROM pemilik $where ORDER BY id_pemilik DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pemilik - Klinik Hewan</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-users me-2"></i> Data Pemilik</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="fas fa-plus"></i> Tambah Pemilik
            </button>
        </div>
        
        <!-- Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama pemilik..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>No</th><th>Nama</th><th>JK</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pemilik']); ?></td>
                                <td><?php echo $row['jenis_kelamin']; ?></td>
                                <td><?php echo $row['no_telepon']; ?></td>
                                <td><?php echo htmlspecialchars(substr($row['alamat'], 0, 40)); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editData(<?php echo $row['id_pemilik']; ?>, '<?php echo addslashes($row['nama_pemilik']); ?>', '<?php echo $row['jenis_kelamin']; ?>', '<?php echo $row['no_telepon']; ?>', '<?php echo addslashes($row['alamat']); ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?hapus=<?php echo $row['id_pemilik']; ?>" class="btn btn-sm btn-danger" onclick="return confirmHapus(event)">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Pemilik</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-body">
                    <div class="mb-3"><label>Nama Pemilik</label><input type="text" name="nama_pemilik" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select"><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select>
                    </div>
                    <div class="mb-3"><label>No Telepon</label><input type="text" name="no_telepon" class="form-control"></div>
                    <div class="mb-3"><label>Alamat</label><textarea name="alamat" class="form-control" rows="3"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Pemilik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_pemilik" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3"><label>Nama Pemilik</label><input type="text" name="nama_pemilik" id="edit_nama" class="form-control" required></div>
                    <div class="mb-3"><label>Jenis Kelamin</label><select name="jenis_kelamin" id="edit_jk" class="form-select"><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                    <div class="mb-3"><label>No Telepon</label><input type="text" name="no_telepon" id="edit_telp" class="form-control"></div>
                    <div class="mb-3"><label>Alamat</label><textarea name="alamat" id="edit_alamat" class="form-control" rows="3"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editData(id, nama, jk, telp, alamat) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_jk').value = jk;
    document.getElementById('edit_telp').value = telp;
    document.getElementById('edit_alamat').value = alamat;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function confirmHapus(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin?',
        text: 'Data pemilik akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1A312C',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = event.target.href;
        }
    });
}
</script>

</body>
</html>