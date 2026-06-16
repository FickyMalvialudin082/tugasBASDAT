<?php
// ============================================
// FILE: includes/navbar.php
// FUNGSI: Top navigation bar
// ============================================
?>
<nav class="top-navbar">
    <button class="toggle-sidebar" id="toggleSidebarBtn">
        <i class="fas fa-bars"></i>
    </button>
    <div class="navbar-info">
        <span>
            <i class="fas fa-clock"></i> 
            <?php echo date('H:i'); ?>
        </span>
        <span>
            <i class="fas fa-calendar"></i> 
            <?php echo date('d/m/Y'); ?>
        </span>
    </div>
</nav>