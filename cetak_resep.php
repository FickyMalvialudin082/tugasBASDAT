<?php
// ============================================
// FILE: cetak_resep.php
// FUNGSI: Mencetak resep obat dalam format HTML (siap print)
// ============================================

require_once 'database.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = "SELECT r.*, h.nama_hewan, h.jenis_hewan, h.ras, p.nama_pemilik, p.alamat, 
                 j.tanggal_pemeriksaan, d.nama_dokter
          FROM resep_obat r
          JOIN hewan h ON r.id_hewan = h.id_hewan
          JOIN pemilik p ON h.id_pemilik = p.id_pemilik
          JOIN jadwal_pemeriksaan j ON r.id_jadwal = j.id_jadwal
          JOIN dokter d ON j.id_dokter = d.id_dokter
          WHERE r.id_resep = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Resep - Klinik Hewan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            padding: 20px;
            background: white;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1A312C;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1A312C;
            margin-bottom: 5px;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 5px;
        }
        .resep-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        .resep-box h4 {
            color: #1A312C;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
        }
        .signature {
            margin-top: 30px;
            text-align: right;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .btn-print {
            background: #1A312C;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .btn-print:hover {
            background: #428475;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="btn-print no-print" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak / Print
        </button>
        
        <div class="header">
            <h1>🏥 KLINIK HEWAN</h1>
            <p>Sistem Manajemen Klinik Hewan</p>
            <p>Jl. Kesehatan No. 123, Kota | Telp: (021) 1234567</p>
        </div>
        
        <div class="info">
            <table>
                <tr>
                    <td width="150"><strong>Nama Pemilik</strong></td>
                    <td>: <?php echo htmlspecialchars($data['nama_pemilik']); ?></td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td>: <?php echo htmlspecialchars($data['alamat']); ?></td>
                </tr>
                <tr>
                    <td><strong>Nama Hewan</strong></td>
                    <td>: <?php echo htmlspecialchars($data['nama_hewan']); ?></td>
                </tr>
                <tr>
                    <td><strong>Jenis / Ras</strong></td>
                    <td>: <?php echo htmlspecialchars($data['jenis_hewan']) . ' / ' . htmlspecialchars($data['ras']); ?></td>
                </tr>
                <tr>
                    <td><strong>Tanggal Pemeriksaan</strong></td>
                    <td>: <?php echo date('d/m/Y', strtotime($data['tanggal_pemeriksaan'])); ?></td>
                </tr>
                <tr>
                    <td><strong>Dokter Penanggung Jawab</strong></td>
                    <td>: <?php echo htmlspecialchars($data['nama_dokter']); ?></td>
                </tr>
            </table>
        </div>
        
        <div class="resep-box">
            <h4>📋 RESEP OBAT</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #1A312C; color: white;">
                    <th style="padding: 8px; text-align: left;">Nama Obat</th>
                    <th style="padding: 8px; text-align: left;">Dosis</th>
                    <th style="padding: 8px; text-align: left;">Aturan Pakai</th>
                    <th style="padding: 8px; text-align: left;">Durasi</th>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($data['obat']); ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($data['dosis']); ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($data['aturan_pakai']); ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($data['durasi']); ?></td>
                </tr>
            </table>
            
            <?php if ($data['catatan']): ?>
                <div style="margin-top: 15px;">
                    <strong>Catatan:</strong>
                    <p style="margin-top: 5px;"><?php echo nl2br(htmlspecialchars($data['catatan'])); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="signature">
            <p>Hormat kami,</p>
            <br><br>
            <p>( <?php echo htmlspecialchars($data['nama_dokter']); ?> )</p>
            <p>Dokter Hewan</p>
        </div>
        
        <div class="footer">
            <p>Resep ini berlaku selama pengobatan berlangsung</p>
            <p>Terima kasih telah mempercayakan kesehatan hewan peliharaan Anda kepada kami</p>
        </div>
    </div>
</body>
</html>