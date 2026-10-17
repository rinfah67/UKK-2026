
<?php
// riwayat.php
// Memeriksa session login
include 'includes/cek_session.php';

// Menghubungkan ke database
include 'config/koneksi.php';

// Mengambil kata pencarian
$cari = isset($_GET['cari'])
    ? mysqli_real_escape_string($koneksi, trim($_GET['cari']))
    : '';

// Mengambil filter status
$status = isset($_GET['status'])
    ? mysqli_real_escape_string($koneksi, trim($_GET['status']))
    : '';

// Query dasar untuk mengambil riwayat pelanggaran
$sql = "SELECT *
        FROM t_pelanggaran_siswa
        WHERE 1=1";

// Pencarian nama siswa atau jenis pelanggaran
if ($cari != '') {
    $sql .= " AND (
        nama_siswa LIKE '%$cari%'
        OR nama_pelanggaran LIKE '%$cari%'
        OR nama_kelas LIKE '%$cari%'
        OR nama_guru LIKE '%$cari%'
    )";
}

// Filter berdasarkan status
if ($status != '') {
    $sql .= " AND status = '$status'";
}

// Mengurutkan riwayat dari tanggal terbaru
$sql .= " ORDER BY tanggal DESC, id DESC";

// Menjalankan query
$query = mysqli_query($koneksi, $sql);

// Menghitung jumlah riwayat yang ditampilkan
$jumlah = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Pelanggaran Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .container-fluid {
            padding: 25px;
        }

        .judul {
            color: #1e3a8a;
            font-weight: bold;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .table thead th {
            background-color: #1e3a8a;
            color: white;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-kembali {
            background-color: #1e3a8a;
            color: white;
        }

        .btn-kembali:hover {
            background-color: #172554;
            color: white;
        }

        .isi-tabel {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<div class="container-fluid">
    <div class="row">
        <?php
            include "layout/sidebar.php";
        ?>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
<div class="container-fluid">

    <!-- Judul halaman -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="judul">Riwayat Pelanggaran Siswa</h3>
            <p class="text-muted mb-0">
                Daftar catatan pelanggaran siswa yang telah dicatat.
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-kembali">
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Ringkasan jumlah data -->
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="text-muted">Jumlah Riwayat Ditampilkan</h6>
            <h3 class="fw-bold text-primary">
                <?= $jumlah; ?> data
            </h3>
        </div>
    </div>

    <!-- Pencarian dan filter -->
    <div class="card mb-4">
        <div class="card-body">

            <form method="GET" action="riwayat.php">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Cari Riwayat</label>
                        <input
                            type="text"
                            name="cari"
                            class="form-control"
                            placeholder="Nama siswa, pelanggaran, kelas..."
                            value="<?= htmlspecialchars($cari); ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Filter Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="belum"
                                <?= $status == 'belum' ? 'selected' : ''; ?>>
                                Belum Ditindak
                            </option>
                            <option value="selesai"
                                <?= $status == 'selesai' ? 'selected' : ''; ?>>
                                Selesai
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            Cari
                        </button>

                        <a href="riwayat.php" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- Tabel riwayat -->
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Poin</th>
                            <th>Guru Pencatat</th>
                            <th>Keterangan</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($jumlah > 0) : ?>

                        <?php $no = 1; ?>

                        <?php while ($data = mysqli_fetch_assoc($query)) : ?>

                            <tr>
                                <td><?= $no++; ?></td>

                                <td class="isi-tabel">
                                    <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">
                                        <?= htmlspecialchars((string)($data['poin'] ?? 0)); ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['tindakan'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?php
                                    $status_data = strtolower(
                                        trim($data['status'] ?? '')
                                    );

                                    if ($status_data == 'selesai') {
                                        echo '<span class="badge bg-success">Selesai</span>';
                                    } elseif (
                                        $status_data == 'belum' ||
                                        $status_data == 'belum ditindak'
                                    ) {
                                        echo '<span class="badge bg-danger">Belum Ditindak</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">'
                                            . htmlspecialchars($data['status'] ?? '-')
                                            . '</span>';
                                    }
                                    ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Tidak ada riwayat pelanggaran yang ditemukan.
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
