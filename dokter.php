<?php
// ============================================
// FILE: dokter.php
// FUNGSI: CRUD Data Dokter (Sederhana)
// ============================================

require_once 'database.php';

// Tambah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = $_POST['nama_dokter'];
    $spesialisasi = $_POST['spesialisasi'];
    $telp = $_POST['no_telepon'];
    $email = $_POST['email'];
    $jadwal = $_POST['jadwal_praktik'];
    mysqli_query($koneksi, "INSERT INTO dokter (nama_dokter, spesialisasi, no_telepon, email, jadwal_praktik) VALUES ('$nama', '$spesialisasi', '$telp', '$email', '$jadwal')");
    echo "<script>Swal.fire('Berhasil!','Data dokter ditambahkan','success').then(()=>{location.href='dokter.php';});</script>";
}

// Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id_dokter'];
    $nama = $_POST['nama_dokter'];
    $spesialisasi = $_POST['spesialisasi'];
    $telp = $_POST['no_telepon'];
    $email = $_POST['email'];
    $jadwal = $_POST['jadwal_praktik'];
    mysqli_query($koneksi, "UPDATE dokter SET nama_dokter='$nama', spesialisasi='$spesialisasi', no_telepon='$telp', email='$email', jadwal_praktik='$jadwal' WHERE id_dokter=$id");
    echo "<script>Swal.fire('Berhasil!','Data dokter diupdate','success').then(()=>{location.href='dokter.php';});</script>";
}

// Hapus
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM dokter WHERE id_dokter=" . $_GET['hapus']);
    echo "<script>Swal.fire('Terhapus!','Data dokter dihapus','success').then(()=>{location.href='dokter.php';});</script>";
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = $search ? "WHERE nama_dokter LIKE '%$search%'" : "";
$result = mysqli_query($koneksi, "SELECT * FROM dokter $where ORDER BY id_dokter DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dokter - Klinik Hewan</title>
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
            <h4><i class="fas fa-user-md me-2"></i> Data Dokter</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class="fas fa-plus"></i> Tambah Dokter</button>
        </div>
        
        <div class="card mb-4"><div class="card-body">
            <form method="GET" class="row"><div class="col-md-8"><input type="text" name="search" class="form-control" placeholder="Cari dokter..." value="<?php echo htmlspecialchars($search); ?>"></div>
            <div class="col-md-4"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button></div></form>
        </div></div>
        
        <div class="card"><div class="card-body p-0"><div class="table-responsive">
            <table class="table table-hover mb-0"><thead><tr><th>No</th><th>Nama Dokter</th><th>Spesialisasi</th><th>Telepon</th><th>Email</th><th>Jadwal Praktik</th><th>Aksi</th></tr></thead>
            <tbody><?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
            <tr><td><?php echo $no++; ?></td><td><?php echo $row['nama_dokter']; ?></td><td><?php echo $row['spesialisasi']; ?></td><td><?php echo $row['no_telepon']; ?></td><td><?php echo $row['email']; ?></td><td><?php echo $row['jadwal_praktik']; ?></td>
            <td><button class="btn btn-sm btn-warning" onclick="editData(<?php echo $row['id_dokter']; ?>, '<?php echo addslashes($row['nama_dokter']); ?>', '<?php echo addslashes($row['spesialisasi']); ?>', '<?php echo $row['no_telepon']; ?>', '<?php echo $row['email']; ?>', '<?php echo addslashes($row['jadwal_praktik']); ?>')"><i class="fas fa-edit"></i></button>
            <a href="?hapus=<?php echo $row['id_dokter']; ?>" class="btn btn-sm btn-danger" onclick="return confirmHapus(event)"><i class="fas fa-trash"></i></a></td></tr>
            <?php endwhile; ?></tbody></table>
        </div></div></div>
    </div>
</div>

<!-- Modal Tambah & Edit Sederhana -->
<div class="modal fade" id="tambahModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title">Tambah Dokter</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="POST"><input type="hidden" name="action" value="tambah"><div class="modal-body">
        <div class="mb-3"><label>Nama Dokter</label><input type="text" name="nama_dokter" class="form-control" required></div>
        <div class="mb-3"><label>Spesialisasi</label><input type="text" name="spesialisasi" class="form-control"></div>
        <div class="mb-3"><label>No Telepon</label><input type="text" name="no_telepon" class="form-control"></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
        <div class="mb-3"><label>Jadwal Praktik</label><input type="text" name="jadwal_praktik" class="form-control" placeholder="Senin - Jumat, 09:00-16:00"></div>
    </div><div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div></form>
</div></div></div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header bg-warning"><h5 class="modal-title">Edit Dokter</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id_dokter" id="edit_id"><div class="modal-body">
        <div class="mb-3"><label>Nama Dokter</label><input type="text" name="nama_dokter" id="edit_nama" class="form-control" required></div>
        <div class="mb-3"><label>Spesialisasi</label><input type="text" name="spesialisasi" id="edit_spesialisasi" class="form-control"></div>
        <div class="mb-3"><label>No Telepon</label><input type="text" name="no_telepon" id="edit_telp" class="form-control"></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" id="edit_email" class="form-control"></div>
        <div class="mb-3"><label>Jadwal Praktik</label><input type="text" name="jadwal_praktik" id="edit_jadwal" class="form-control"></div>
    </div><div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div></form>
</div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editData(id, nama, spesialisasi, telp, email, jadwal) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_spesialisasi').value = spesialisasi;
    document.getElementById('edit_telp').value = telp;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_jadwal').value = jadwal;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
function confirmHapus(event) { event.preventDefault(); Swal.fire({title:'Yakin?',text:'Data akan dihapus!',icon:'warning',showCancelButton:true,confirmButtonColor:'#d33',confirmButtonText:'Ya, hapus!'}).then((r)=>{if(r.isConfirmed)window.location.href=event.target.href;}); }
</script>
</body>
</html>