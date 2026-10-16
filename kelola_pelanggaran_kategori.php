
<?php
session_start(); // Memulai session login
include 'config/koneksi.php'; // Menghubungkan ke database

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php"); // Kembali ke halaman login
    exit; // Menghentikan program
}

// Memastikan hanya admin yang bisa mengakses halaman
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!"); // Menolak akses selain admin
}

// Mengambil semua kategori pelanggaran dari database
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <title>Kelola Kategori Pelanggaran</title>
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
                    <a href="kelola_walikelas.php" class="nav-link text-white">
                        Kelola Walikelas
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_pelanggaran_kategori.php" class="nav-link active">
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

<h2>Data Kategori Pelanggaran</h2>

<!-- Tombol kembali ke dashboard dan tambah kategori -->
<a href="dashboard.php">Kembali ke Dashboard</a> |
<a href="tambah_pelanggaran_kategori.php">Tambah Kategori</a>

<br><br>

<!-- Tabel untuk menampilkan data kategori -->
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Nama Kategori</th>
        <th>Deskripsi</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1; // Nomor urut tabel

    // Mengulang data kategori satu per satu
    while ($data = mysqli_fetch_assoc($query)) {
    ?>
    <tr>
        <td><?= $no++; ?></td>

        <!-- Menampilkan nama kategori -->
        <td><?= htmlspecialchars($data['nama']); ?></td>

        <!-- Menampilkan deskripsi kategori -->
        <td><?= htmlspecialchars($data['deskripsi']); ?></td>

        <!-- Menampilkan status kategori -->
        <td>
            <?= $data['status_aktif'] == 1 ? 'Aktif' : 'Tidak Aktif'; ?>
        </td>

        <td>
            <!-- Tombol edit berdasarkan ID kategori -->
            <a href="edit_pelanggaran_kategori.php?id=<?= $data['id']; ?>">
                Edit
            </a> |

            <!-- Tombol hapus berdasarkan ID kategori -->
            <a href="hapus_pelanggaran_kategori.php?id=<?= $data['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                Hapus
            </a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
