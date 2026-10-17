
<?php
session_start();
include 'config/koneksi.php';

// Memeriksa login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Memeriksa hak akses guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    die("Akses ditolak. Halaman ini khusus guru.");
}

$pesan = "";
$error = "";

// Memproses form ketika tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
    $siswa_id = (int) $_POST['siswa_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $pelanggaran_id = (int) $_POST['pelanggaran_id'];
    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan']);
    $tindakan = trim($_POST['tindakan']);

    // Mengambil data siswa
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT s.nama, k.nama AS nama_kelas
         FROM t_siswa s
         LEFT JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
         LEFT JOIN t_kelas k ON k.id = ks.kelas_id
         WHERE s.id = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "i", $siswa_id);
    mysqli_stmt_execute($stmt);
    $hasil_siswa = mysqli_stmt_get_result($stmt);
    $siswa = mysqli_fetch_assoc($hasil_siswa);
    mysqli_stmt_close($stmt);

    // Mengambil data pelanggaran
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT nama, pelanggaran_kategori_id, poin
         FROM t_pelanggaran
         WHERE id = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "i", $pelanggaran_id);
    mysqli_stmt_execute($stmt);
    $hasil_pelanggaran = mysqli_stmt_get_result($stmt);
    $pelanggaran = mysqli_fetch_assoc($hasil_pelanggaran);
    mysqli_stmt_close($stmt);

    // Mengambil identitas guru yang sedang login
    $user_id = (int) $_SESSION['id_user'];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $hasil_guru = mysqli_stmt_get_result($stmt);
    $guru = mysqli_fetch_assoc($hasil_guru);
    mysqli_stmt_close($stmt);

    if (!$siswa || !$pelanggaran || !$guru) {
        $error = "Data siswa, pelanggaran, atau akun guru tidak ditemukan.";
    } elseif ($tanggal == "" || $keterangan == "") {
        $error = "Tanggal dan keterangan wajib diisi.";
    } else {
        $nama_siswa = $siswa['nama'];
        $nama_kelas = $siswa['nama_kelas'] ?? '';
        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) $pelanggaran['pelanggaran_kategori_id'];
        $poin = (int) $pelanggaran['poin'];
        $guru_id = (int) $guru['id'];
        $nama_guru = $guru['nama'];
        $status = "Belum ditindaklanjuti";

        /*
         * Pastikan kolom tabel t_pelanggaran_siswa sesuai
         * dengan daftar kolom pada query INSERT ini.
         */
        $sql = "INSERT INTO t_pelanggaran_siswa
                (
                    tahun_ajaran_id,
                    siswa_id,
                    nama_siswa,
                    kelas_id,
                    nama_kelas,
                    pelanggaran_id,
                    nama_pelanggaran,
                    pelanggaran_kategori_id,
                    guru_id,
                    nama_guru,
                    tanggal,
                    keterangan,
                    poin,
                    tindakan,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($koneksi, $sql);

        if (!$stmt) {
            $error = "Query penyimpanan gagal: " . mysqli_error($koneksi);
        } else {
            // Ada 15 tanda tanya dan 15 variabel.
            mysqli_stmt_bind_param(
                $stmt,
                "iisisssiiississ",
                $tahun_ajaran_id,
                $siswa_id,
                $nama_siswa,
                $kelas_id,
                $nama_kelas,
                $pelanggaran_id,
                $nama_pelanggaran,
                $kategori_id,
                $guru_id,
                $nama_guru,
                $tanggal,
                $keterangan,
                $poin,
                $tindakan,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                $pesan = "Catatan pelanggaran berhasil disimpan.";
            } else {
                $error = "Gagal menyimpan: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }
}

// Mengambil daftar tahun ajaran
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

// Mengambil daftar siswa
$siswa_list = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama FROM t_siswa
     WHERE status_aktif = 1 ORDER BY nama ASC"
);

// Mengambil daftar kelas
$kelas_list = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas
     WHERE status_aktif = 1 ORDER BY nama ASC"
);

// Mengambil daftar pelanggaran
$pelanggaran_list = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin FROM t_pelanggaran
     WHERE status_aktif = 1 ORDER BY nama ASC"
);

// Mengambil riwayat catatan
$riwayat = mysqli_query(
    $koneksi,
    "SELECT id, nama_siswa, nama_kelas, nama_pelanggaran,
            tanggal, poin, status
     FROM t_pelanggaran_siswa
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catatan Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
         <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-12 col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">
            <h4 class="mb-4">Si Pelang</h4>

            <nav class="nav nav-pills flex-column gap-2">
                <a href="dashboard.php" class="nav-link text-white">
                    Dashboard
                </a>

                <a href="catatan_pelanggaran.php" class="nav-link active">
                    Catat Pelanggaran
                </a>

                <a href="tindakan.php" class="nav-link text-white">
                    Tindakan
                </a>

                <a href="riwayat.php" class="nav-link text-white">
                    Riwayat
                </a>

                <a href="rekap_poin.php" class="nav-link text-white">
                    Rekap Poin
                </a>

                <hr>

                <a href="logout.php"
                   class="nav-link text-danger"
                   onclick="return confirm('Yakin ingin logout?')">
                    Logout
                </a>
            </nav>
        </div>

        <!-- KONTEN UTAMA -->
        <main class="col-12 col-md-9 col-lg-10 p-3 p-md-4">
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Catatan Pelanggaran Siswa</h3>
        <a href="dashboard.php" class="btn btn-secondary">
            Kembali ke Dashboard
        </a>
    </div>

    <?php if ($pesan != ""): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <strong>Form Catatan Pelanggaran</strong>
        </div>

        <div class="card-body">
            <form method="POST">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id"
                                class="form-select" required>
                            <option value="">Pilih tahun ajaran</option>
                            <?php while ($t = mysqli_fetch_assoc($tahun)): ?>
                                <option value="<?= $t['id'] ?>">
                                    <?= htmlspecialchars($t['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Siswa</label>
                        <select name="siswa_id" class="form-select" required>
                            <option value="">Pilih siswa</option>
                            <?php while ($s = mysqli_fetch_assoc($siswa_list)): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">Pilih kelas</option>
                            <?php while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                                <option value="<?= $k['id'] ?>">
                                    <?= htmlspecialchars($k['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Pelanggaran</label>
                        <select name="pelanggaran_id"
                                class="form-select" required>
                            <option value="">Pilih pelanggaran</option>
                            <?php while ($p = mysqli_fetch_assoc($pelanggaran_list)): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nama']) ?>
                                    (<?= (int) $p['poin'] ?> poin)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal"
                               class="form-control"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tindakan</label>
                        <input type="text" name="tindakan"
                               class="form-control"
                               placeholder="Contoh: Teguran lisan">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan"
                                  class="form-control" rows="3"
                                  placeholder="Jelaskan kejadian pelanggaran"
                                  required></textarea>
                    </div>
                </div>

                <button type="submit" name="simpan"
                        class="btn btn-primary">
                    Simpan Catatan
                </button>

            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Riwayat Pelanggaran</strong>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Tanggal</th>
                            <th>Poin</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($riwayat && mysqli_num_rows($riwayat) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($r['nama_siswa']) ?></td>
                                    <td><?= htmlspecialchars($r['nama_kelas']) ?></td>
                                    <td><?= htmlspecialchars($r['nama_pelanggaran']) ?></td>
                                    <td><?= htmlspecialchars($r['tanggal']) ?></td>
                                    <td><?= (int) $r['poin'] ?></td>
                                    <td><?= htmlspecialchars($r['status']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    Belum ada catatan pelanggaran.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>
