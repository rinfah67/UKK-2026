
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Halaman ini khusus guru
if (($_SESSION['role'] ?? '') != 'guru') {
    header("Location: dashboard.php");
    exit;
}

$user_id = (int)($_SESSION['id_user'] ?? 0);

// Cari data guru dari akun yang sedang login
$stmt = mysqli_prepare($koneksi, "SELECT id, nama FROM t_guru WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$guru) {
    die("Data guru belum terhubung dengan akun pengguna.");
}

$guru_id = (int)$guru['id'];

// Ambil siswa yang mempunyai penempatan kelas aktif
$siswa = mysqli_query($koneksi, "
    SELECT ks.id AS penempatan_id, s.nama AS nama_siswa,
           k.nama AS nama_kelas
    FROM t_kelas_siswa ks
    JOIN t_siswa s ON s.id = ks.siswa_id
    JOIN t_kelas k ON k.id = ks.kelas_id
    WHERE ks.status_aktif = 1 AND s.status_aktif = 1
    ORDER BY s.nama
");

// Ambil jenis pelanggaran aktif
$pelanggaran = mysqli_query($koneksi, "
    SELECT id, nama, poin
    FROM t_pelanggaran
    WHERE status_aktif = 1
    ORDER BY nama
");

// Ambil catatan milik guru yang login
$stmt = mysqli_prepare($koneksi, "
    SELECT *
    FROM t_pelanggaran_siswa
    WHERE guru_id = ?
    ORDER BY tanggal DESC, id DESC
");
mysqli_stmt_bind_param($stmt, "i", $guru_id);
mysqli_stmt_execute($stmt);
$catatan = mysqli_stmt_get_result($stmt);

// Mengamankan teks sebelum ditampilkan ke halaman
function aman_catatan($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Catatan Pelanggaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
<div class="row">

<!-- SIDEBAR SAMA DENGAN DASHBOARD -->
<div class="container-fluid">
    <div class="row">
        <?php
            include "layout/sidebar.php";
        ?>
         <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                Pelanggaran Siswa
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="catatan_pelanggaran.php" class="nav-link active">
                        Catat Pelanggaran
                    </a>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <h2>Dashboard</h2>
            <p>

<!-- KONTEN UTAMA -->
<main class="col-md-9 col-lg-10 p-4">

    <h2>Catatan Pelanggaran Siswa</h2>
    <p>Selamat datang, <b><?= aman_catatan($guru['nama']) ?></b>.</p>

    <?php if (isset($_GET['pesan'])): ?>
        <div class="alert alert-info">
            <?= aman_catatan($_GET['pesan']) ?>
        </div>
    <?php endif; ?>

    <!-- FORM TAMBAH -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Tambah Catatan Pelanggaran</h5>

            <form action="proses_tambah_pelanggaran.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Siswa dan Kelas</label>
                    <select name="penempatan_id" class="form-select" required>
                        <option value="">-- Pilih Siswa --</option>

                        <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
                            <option value="<?= (int)$s['penempatan_id'] ?>">
                                <?= aman_catatan($s['nama_siswa']) ?>
                                - <?= aman_catatan($s['nama_kelas']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Pelanggaran</label>
                    <select name="pelanggaran_id" class="form-select" required>
                        <option value="">-- Pilih Pelanggaran --</option>

                        <?php while ($p = mysqli_fetch_assoc($pelanggaran)): ?>
                            <option value="<?= (int)$p['id'] ?>">
                                <?= aman_catatan($p['nama']) ?>
                                (<?= (int)$p['poin'] ?> poin)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control"
                           value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control"
                              rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tindakan</label>
                    <textarea name="tindakan" class="form-control"
                              rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan Catatan
                </button>
            </form>
        </div>
    </div>

    <!-- DAFTAR CATATAN -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Daftar Catatan Pelanggaran</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                            <th>Keterangan</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($catatan) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($d = mysqli_fetch_assoc($catatan)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= aman_catatan($d['tanggal']) ?></td>
                                <td><?= aman_catatan($d['nama_siswa']) ?></td>
                                <td><?= aman_catatan($d['nama_kelas']) ?></td>
                                <td><?= aman_catatan($d['nama_pelanggaran']) ?></td>
                                <td><?= (int)$d['poin'] ?></td>
                                <td><?= aman_catatan($d['keterangan']) ?></td>
                                <td><?= aman_catatan($d['tindakan']) ?></td>
                                <td><?= aman_catatan($d['status']) ?></td>
                                <td>
                                    <a href="edit_pelanggaran.php?id=<?= (int)$d['id'] ?>"
                                       class="btn btn-warning btn-sm mb-1">Edit</a>

                                    <form action="hapus_pelanggaran.php" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                                        <input type="hidden" name="id"
                                               value="<?= (int)$d['id'] ?>">
                                        <button type="submit"
                                                class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">
                                Belum ada catatan pelanggaran.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>
</div>
</div>
</body>
</html>
