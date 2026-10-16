
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$query = "SELECT wk.*, 
                 g.nama AS nama_guru,
                 k.nama AS nama_kelas,
                 k.tingkat,
                 k.jurusan,
                 ta.nama AS tahun_ajaran
          FROM t_wali_kelas wk
          LEFT JOIN t_guru g ON wk.guru_id = g.id
          LEFT JOIN t_kelas k ON wk.kelas_id = k.id
          LEFT JOIN t_tahun_ajaran ta ON wk.tahun_ajaran_id = ta.id
          ORDER BY wk.id DESC";

$data = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <title>Kelola Wali Kelas</title>
    <style>/* Sidebar tetap di kiri dan memenuhi tinggi layar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 1000;
}

/* Mengatur posisi konten utama */
main {
    margin-left: 16.666667%;
}

/* Untuk layar besar */
@media (min-width: 992px) {
    main {
        margin-left: 16.666667%;
    }
}

/* Untuk layar kecil */
@media (max-width: 767.98px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    main {
        margin-left: 0;
    }
}</style>
</head>
<body>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark p-3 sidebar">
            <h4 class="text-white mb-4">
                My Website
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_guru.php" class="nav-link text-white">
                        Data Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="Kelola_kelas.php" class="nav-link text-white">
                        Data Kelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_tahun_ajaran.php" class="nav-link text-white">
                        Data Tahun Ajaran
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_walikelas.php" class="nav-link active">
                        Kelola Walikelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_pelanggaran_kategori.php" class="nav-link text-white">
                        Pelanggaran Kategori
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>
                
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

<h2>Data Wali Kelas</h2>

<a href="dashboard.php">Kembali ke Dashboard</a> |
<a href="tambah_walikelas.php">Tambah Wali Kelas</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Guru</th>
        <th>Kelas</th>
        <th>Tahun Ajaran</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($row = mysqli_fetch_assoc($data)) {
    ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($row['nama_guru'] ?? '-'); ?></td>
        <td>
            <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
            (<?= htmlspecialchars($row['tingkat'] ?? '-'); ?>
            <?= htmlspecialchars($row['jurusan'] ?? ''); ?>)
        </td>
        <td><?= htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?></td>
        <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
        <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
        <td>
            <?= $row['status_aktif'] == 1 ? 'Aktif' : 'Tidak Aktif'; ?>
        </td>
        <td>
            <a href="edit_walikelas.php?id=<?= $row['id']; ?>">Edit</a> |
            <a href="hapus_walikelas.php?id=<?= $row['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus data ini?')">
               Hapus
            </a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
