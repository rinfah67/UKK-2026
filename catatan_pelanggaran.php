
<?php
session_start();
include 'config/koneksi.php';

// Memeriksa login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Halaman ini khusus guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    die("Akses ditolak. Halaman ini khusus guru.");
}

$pesan = "";
$error = "";
$data_edit = null;

// ======================================
// AKSI: MENGAMBIL DATA UNTUK DIEDIT
// ======================================
if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT * FROM t_pelanggaran_siswa
         WHERE id = ?
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_edit);
        mysqli_stmt_execute($stmt);

        $hasil_edit = mysqli_stmt_get_result($stmt);
        $data_edit = mysqli_fetch_assoc($hasil_edit);

        mysqli_stmt_close($stmt);

        if (!$data_edit) {
            $error = "Catatan tidak ditemukan.";
        }
    } else {
        $error = "Gagal mengambil data catatan.";
    }
}

// ======================================
// AKSI: UPDATE CATATAN
// ======================================
if (isset($_POST['update_catatan'])) {
    $id_edit = (int) ($_POST['id_catatan'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (
        $id_edit <= 0 ||
        $tanggal == '' ||
        $keterangan == '' ||
        $status == ''
    ) {
        $error = "Tanggal, keterangan, dan status wajib diisi.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran_siswa
             SET tanggal = ?,
                 keterangan = ?,
                 tindakan = ?,
                 status = ?
             WHERE id = ?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssssi",
                $tanggal,
                $keterangan,
                $tindakan,
                $status,
                $id_edit
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header(
                    "Location: catatan_pelanggaran.php?pesan=update"
                );
                exit;
            } else {
                $error = "Gagal memperbarui catatan: " .
                         mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        } else {
            $error = "Query update gagal: " .
                     mysqli_error($koneksi);
        }
    }

    // Memuat kembali data yang sedang diedit
    // agar form tidak kembali menjadi form tambah
    if ($id_edit > 0) {
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT * FROM t_pelanggaran_siswa
             WHERE id = ?
             LIMIT 1"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_edit);
            mysqli_stmt_execute($stmt);

            $hasil_edit = mysqli_stmt_get_result($stmt);
            $data_edit = mysqli_fetch_assoc($hasil_edit);

            mysqli_stmt_close($stmt);
        }

        // Menampilkan nilai yang baru diketik jika update gagal
        if ($data_edit) {
            $data_edit['tanggal'] = $tanggal;
            $data_edit['keterangan'] = $keterangan;
            $data_edit['tindakan'] = $tindakan;
            $data_edit['status'] = $status;
        }
    }
}

// ======================================
// AKSI: HAPUS CATATAN
// ======================================
if (isset($_POST['hapus_catatan'])) {
    $id_catatan = (int) ($_POST['id_catatan'] ?? 0);

    if ($id_catatan > 0) {
        $stmt = mysqli_prepare(
            $koneksi,
            "DELETE FROM t_pelanggaran_siswa
             WHERE id = ?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_catatan);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header(
                    "Location: catatan_pelanggaran.php?pesan=hapus"
                );
                exit;
            } else {
                $error = "Gagal menghapus catatan: " .
                         mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        } else {
            $error = "Query hapus gagal: " .
                     mysqli_error($koneksi);
        }
    } else {
        $error = "ID catatan tidak valid.";
    }
}

// ======================================
// AKSI: SIMPAN CATATAN BARU
// ======================================
if (isset($_POST['simpan'])) {
    $tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
    $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tindakan = trim($_POST['tindakan'] ?? '');

    // Mengambil data siswa berdasarkan kelas dan tahun ajaran
    $siswa = null;

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT s.nama, k.nama AS nama_kelas
         FROM t_siswa s
         INNER JOIN t_kelas_siswa ks
             ON ks.siswa_id = s.id
         INNER JOIN t_kelas k
             ON k.id = ks.kelas_id
         WHERE s.id = ?
           AND ks.tahun_ajaran_id = ?
           AND ks.kelas_id = ?
           AND ks.status_aktif = 1
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $siswa_id,
            $tahun_ajaran_id,
            $kelas_id
        );

        mysqli_stmt_execute($stmt);

        $hasil_siswa = mysqli_stmt_get_result($stmt);
        $siswa = mysqli_fetch_assoc($hasil_siswa);

        mysqli_stmt_close($stmt);
    }

    // Mengambil data pelanggaran
    $pelanggaran = null;

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT nama, pelanggaran_kategori_id, poin
         FROM t_pelanggaran
         WHERE id = ?
           AND status_aktif = 1
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $pelanggaran_id);
        mysqli_stmt_execute($stmt);

        $hasil_pelanggaran = mysqli_stmt_get_result($stmt);
        $pelanggaran = mysqli_fetch_assoc($hasil_pelanggaran);

        mysqli_stmt_close($stmt);
    }

    // Mengambil data guru yang sedang login
    $guru = null;
    $user_id = (int) ($_SESSION['id_user'] ?? 0);

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama
         FROM t_guru
         WHERE user_id = ?
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $hasil_guru = mysqli_stmt_get_result($stmt);
        $guru = mysqli_fetch_assoc($hasil_guru);

        mysqli_stmt_close($stmt);
    }

    // Memeriksa kelengkapan data
    if (
        !$siswa ||
        !$pelanggaran ||
        !$guru ||
        $tahun_ajaran_id <= 0 ||
        $siswa_id <= 0 ||
        $kelas_id <= 0 ||
        $pelanggaran_id <= 0 ||
        $tanggal == '' ||
        $keterangan == ''
    ) {
        $error = "Data tidak lengkap atau siswa tidak terdaftar " .
                 "di kelas dan tahun ajaran yang dipilih.";
    } else {
        $nama_siswa = $siswa['nama'];
        $nama_kelas = $siswa['nama_kelas'];
        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) $pelanggaran['pelanggaran_kategori_id'];
        $poin = (int) $pelanggaran['poin'];
        $guru_id = (int) $guru['id'];
        $nama_guru = $guru['nama'];

        // Status awal untuk catatan baru
        $status = "Belum ditindaklanjuti";

        $sql = "INSERT INTO t_pelanggaran_siswa (
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
            $error = "Query penyimpanan gagal: " .
                     mysqli_error($koneksi);
        } else {
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
                mysqli_stmt_close($stmt);

                header(
                    "Location: catatan_pelanggaran.php?pesan=simpan"
                );
                exit;
            } else {
                $error = "Gagal menyimpan: " .
                         mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }
}

// ======================================
// PESAN NOTIFIKASI
// ======================================
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'simpan') {
        $pesan = "Catatan pelanggaran berhasil disimpan.";
    } elseif ($_GET['pesan'] == 'update') {
        $pesan = "Catatan berhasil diperbarui.";
    } elseif ($_GET['pesan'] == 'hapus') {
        $pesan = "Catatan berhasil dihapus.";
    }
}

// ======================================
// DATA UNTUK FORM
// ======================================
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     ORDER BY id DESC"
);

$siswa_list = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

$kelas_list = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

$pelanggaran_list = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Mengambil riwayat pelanggaran
$riwayat = mysqli_query(
    $koneksi,
    "SELECT id, nama_siswa, nama_kelas,
            nama_pelanggaran, tanggal, poin,
            tindakan, status
     FROM t_pelanggaran_siswa
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Catatan Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<?php include "layout/sidebar.php"; ?>

<!-- Konten utama di sebelah sidebar -->
<main class="container-fluid">
    <div class="row">
        <div class="col-12 ms-auto p-4"
             style="width: calc(100% - 222px);">

            <h3 class="mb-4">Catatan Pelanggaran Siswa</h3>

            <!-- Notifikasi -->
            <?php if ($pesan != ''): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($pesan) ?>
                </div>
            <?php endif; ?>

            <?php if ($error != ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- FORM CATATAN -->
            <div class="card mb-4">
                <div class="card-header">
                    <strong>
                        <?= $data_edit
                            ? 'Edit Catatan Pelanggaran'
                            : 'Form Catatan Pelanggaran' ?>
                    </strong>
                </div>

                <div class="card-body">
                    <form method="POST"
                          action="<?= $data_edit
                              ? 'catatan_pelanggaran.php?edit=' . (int) $data_edit['id']
                              : 'catatan_pelanggaran.php' ?>">

                        <?php if ($data_edit): ?>
                            <input type="hidden"
                                   name="id_catatan"
                                   value="<?= (int) $data_edit['id'] ?>">
                        <?php endif; ?>

                        <div class="row">

                            <?php if (!$data_edit): ?>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun Ajaran</label>

                                    <select name="tahun_ajaran_id"
                                            class="form-select"
                                            required>
                                        <option value="">Pilih tahun ajaran</option>

                                        <?php if ($tahun): ?>
                                            <?php while ($t = mysqli_fetch_assoc($tahun)): ?>
                                                <option value="<?= (int) $t['id'] ?>">
                                                    <?= htmlspecialchars($t['nama']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Siswa</label>

                                    <select name="siswa_id"
                                            class="form-select"
                                            required>
                                        <option value="">Pilih siswa</option>

                                        <?php if ($siswa_list): ?>
                                            <?php while ($s = mysqli_fetch_assoc($siswa_list)): ?>
                                                <option value="<?= (int) $s['id'] ?>">
                                                    <?= htmlspecialchars(
                                                        $s['nis'] . ' - ' . $s['nama']
                                                    ) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kelas</label>

                                    <select name="kelas_id"
                                            class="form-select"
                                            required>
                                        <option value="">Pilih kelas</option>

                                        <?php if ($kelas_list): ?>
                                            <?php while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                                                <option value="<?= (int) $k['id'] ?>">
                                                    <?= htmlspecialchars($k['nama']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Jenis Pelanggaran
                                    </label>

                                    <select name="pelanggaran_id"
                                            class="form-select"
                                            required>
                                        <option value="">Pilih pelanggaran</option>

                                        <?php if ($pelanggaran_list): ?>
                                            <?php while ($p = mysqli_fetch_assoc($pelanggaran_list)): ?>
                                                <option value="<?= (int) $p['id'] ?>">
                                                    <?= htmlspecialchars($p['nama']) ?>
                                                    (<?= (int) $p['poin'] ?> poin)
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                            <?php else: ?>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Nama Siswa</label>

                                    <input type="text"
                                           class="form-control"
                                           value="<?= htmlspecialchars(
                                               $data_edit['nama_siswa'] ?? ''
                                           ) ?>"
                                           disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kelas</label>

                                    <input type="text"
                                           class="form-control"
                                           value="<?= htmlspecialchars(
                                               $data_edit['nama_kelas'] ?? ''
                                           ) ?>"
                                           disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Jenis Pelanggaran
                                    </label>

                                    <input type="text"
                                           class="form-control"
                                           value="<?= htmlspecialchars(
                                               $data_edit['nama_pelanggaran'] ?? ''
                                           ) ?>"
                                           disabled>
                                </div>

                            <?php endif; ?>

                            <!-- TANGGAL -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>

                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?= htmlspecialchars(
                                           $data_edit['tanggal'] ?? date('Y-m-d')
                                       ) ?>"
                                       required>
                            </div>

                            <!-- STATUS -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    Status
                                </label>

                                <select name="status"
                                        id="status"
                                        class="form-select"
                                        required>
                                    <?php
                                    $status_sekarang = $data_edit['status']
                                        ?? 'Belum ditindaklanjuti';

                                    $pilihan_status = [
                                        'Belum ditindaklanjuti',
                                        'Diproses',
                                        'Selesai'
                                    ];
                                    ?>

                                    <?php foreach ($pilihan_status as $pilihan): ?>
                                        <option
                                            value="<?= htmlspecialchars($pilihan) ?>"
                                            <?= $status_sekarang == $pilihan
                                                ? 'selected'
                                                : '' ?>>
                                            <?= htmlspecialchars($pilihan) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- TINDAKAN -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tindakan</label>

                                <input type="text"
                                       name="tindakan"
                                       class="form-control"
                                       value="<?= htmlspecialchars(
                                           $data_edit['tindakan'] ?? ''
                                       ) ?>"
                                       placeholder="Contoh: Teguran lisan">
                            </div>

                            <!-- KETERANGAN -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Keterangan</label>

                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Jelaskan kejadian pelanggaran"
                                          required><?= htmlspecialchars(
                                              $data_edit['keterangan'] ?? ''
                                          ) ?></textarea>
                            </div>

                        </div>

                        <button type="submit"
                                name="<?= $data_edit
                                    ? 'update_catatan'
                                    : 'simpan' ?>"
                                value="1"
                                class="btn btn-primary">
                            <?= $data_edit
                                ? 'Update Catatan'
                                : 'Simpan Catatan' ?>
                        </button>

                        <?php if ($data_edit): ?>
                            <a href="catatan_pelanggaran.php"
                               class="btn btn-secondary">
                                Batal
                            </a>
                        <?php endif; ?>

                    </form>
                </div>
            </div>

            <!-- TABEL RIWAYAT -->
            <div class="card">
                <div class="card-header">
                    <strong>Riwayat Pelanggaran</strong>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Pelanggaran</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($riwayat && mysqli_num_rows($riwayat) > 0): ?>
                                    <?php $no = 1; ?>

                                    <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $r['nama_siswa'] ?? '-'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $r['nama_kelas'] ?? '-'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $r['nama_pelanggaran'] ?? '-'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $r['tanggal'] ?? '-'
                                                ) ?>
                                            </td>

                                            <td><?= (int) $r['poin'] ?></td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $r['tindakan'] ?: '-'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?php
                                                $warna_status = 'secondary';

                                                if ($r['status'] == 'Selesai') {
                                                    $warna_status = 'success';
                                                } elseif ($r['status'] == 'Diproses') {
                                                    $warna_status = 'warning';
                                                } elseif (
                                                    strtolower($r['status']) ==
                                                    'belum ditindaklanjuti'
                                                ) {
                                                    $warna_status = 'danger';
                                                }
                                                ?>

                                                <span class="badge text-bg-<?= $warna_status ?>">
                                                    <?= htmlspecialchars(
                                                        $r['status'] ?? '-'
                                                    ) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <a
                                                        href="catatan_pelanggaran.php?edit=<?= (int) $r['id'] ?>"
                                                        class="btn btn-warning btn-sm">
                                                        Edit
                                                    </a>

                                                    <form method="POST"
                                                          action="catatan_pelanggaran.php"
                                                          onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">

                                                        <input
                                                            type="hidden"
                                                            name="id_catatan"
                                                            value="<?= (int) $r['id'] ?>">

                                                        <button
                                                            type="submit"
                                                            name="hapus_catatan"
                                                            value="1"
                                                            class="btn btn-danger btn-sm">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">
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
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
