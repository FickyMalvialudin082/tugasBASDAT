<?php
// ============================================
// FILE: includes/sidebar.php
// FUNGSI: Sidebar navigasi
// ============================================

$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-paw"></i>
        <h3>Klinik Hewan</h3>
        <small>Sistem Manajemen</small>
    </div>
    <ul class="sidebar-nav">
        <!-- Dashboard -->
        <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <a href="index.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
         <!-- Data Pemilik -->
        <li class="<?php echo $current_page == 'pemilik.php' ? 'active' : ''; ?>">
            <a href="pemilik.php">
                <i class="fas fa-users"></i>
                <span>Data Pemilik</span>
            </a>
        </li>
        
        <!-- Data Hewan -->
        <li class="<?php echo $current_page == 'hewan.php' || $current_page == 'tambah_hewan.php' || $current_page == 'edit_hewan.php' ? 'active' : ''; ?>">
            <a href="hewan.php">
                <i class="fas fa-dog"></i>
                <span>Data Hewan</span>
            </a>
        </li>
        
       
        
        <!-- Data Dokter -->
        <li class="<?php echo $current_page == 'dokter.php' ? 'active' : ''; ?>">
            <a href="dokter.php">
                <i class="fas fa-user-md"></i>
                <span>Data Dokter</span>
            </a>
        </li>
        
        <li class="<?php echo $current_page == 'rawat_inap.php' ? 'active' : ''; ?>">
    <a href="rawat_inap.php">
        <i class="fas fa-hospital"></i>
        <span>Rawat Inap</span>
    </a>
</li>

        <!-- Jadwal Pemeriksaan -->
        <li class="<?php echo $current_page == 'jadwal.php' ? 'active' : ''; ?>">
            <a href="jadwal.php">
                <i class="fas fa-calendar-alt"></i>
                <span>Jadwal Pemeriksaan</span>
            </a>
        </li>
        
        <!-- ============================================ -->
        <!-- RESEP OBAT (MENU BARU) -->
        <!-- ============================================ -->
        <li class="<?php echo $current_page == 'resep.php' || $current_page == 'tambah_resep.php' || $current_page == 'edit_resep.php' ? 'active' : ''; ?>">
            <a href="resep.php">
                <i class="fas fa-prescription-bottle"></i>
                <span>Resep Obat</span>
            </a>
        </li>
        <!-- ============================================ -->
        
    </ul>
</div>