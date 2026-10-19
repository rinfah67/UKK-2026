
<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
?>

<div class="position-fixed top-0 start-0 vh-100 bg-dark text-white p-2 overflow-y-auto"
     style="width: 222px;">

    <h5 class="fw-bold mt-2 mb-3 ms-1">Pelanggaran Siswa</h5>

    <nav class="nav nav-pills flex-column gap-1">

        <a href="dashboard.php"
           class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            Dashboard
        </a>

        <a href="catatan_pelanggaran.php"
           class="nav-link text-white <?= $current_page == 'catatan_pelanggaran.php' ? 'active' : ''; ?>">
            Catat Pelanggaran
        </a>

        <a href="tindakan.php"
           class="nav-link text-white <?= $current_page == 'tindakan.php' ? 'active' : ''; ?>">
            Tindakan
        </a>

        <a href="riwayat.php"
           class="nav-link text-white <?= $current_page == 'riwayat.php' ? 'active' : ''; ?>">
            Riwayat
        </a>

        <a href="rekap_poin.php"
           class="nav-link text-white <?= $current_page == 'rekap_poin.php' ? 'active' : ''; ?>">
            Rekap Poin
        </a>

        
        
        <?php if ($role == 'admin' || ($role == 'guru' && !empty($is_walikelas))) { ?>
    <a href="laporan.php" class="nav-link text-white">
        Laporan
    </a>
<?php } ?>



        <hr class="border-secondary">

        <a href="logout.php" class="nav-link text-danger">
            Logout
        </a>

    </nav>
</div>
