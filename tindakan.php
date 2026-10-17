<?php
// tindakan.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Proses menyimpan tindakan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];
    $tindakan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tindakan'])
    );

    $sql_update = "UPDATE pelanggaran_siswa
                   SET tindakan = '$tindakan'
                   WHERE id = $id";

    if (mysqli_query($koneksi, $sql_update)) {
        header('Location: tindakan.php?pesan=berhasil');
        exit;
    } else {
        $pesan_error = mysqli_error($koneksi);
    }
}

// Mengambil data pelanggaran siswa
$sql = "SELECT id, nama_siswa, nama_kelas,
               nama_pelanggaran, tanggal,
               keterangan, tindakan
        FROM t_pelanggaran_siswa
        ORDER BY tanggal DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die('Gagal mengambil data: ' . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tindakan Pelanggaran Siswa</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 25px;
            background: #f5f6fa;
        }

        h2 {
            color: #243b53;
        }

        .pesan {
            padding: 10px;
            margin: 10px 0;
            background: #d4edda;
            color: #155724;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #243b53;
            color: white;
        }

        textarea {
            width: 180px;
            min-height: 60px;
        }

        button, a {
            padding: 7px 10px;
            cursor: pointer;
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
    <h2>Tindakan Pelanggaran Siswa</h2>
    <p>Kelola tindakan untuk setiap pelanggaran siswa.</p>

    <a href="dashboard.php">Kembali ke Dashboard</a>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'berhasil') { ?>
        <div class="pesan">Tindakan berhasil disimpan.</div>
    <?php } ?>

    <?php if (isset($pesan_error)) { ?>
        <div style="color:red">
            Gagal menyimpan tindakan:
            <?= htmlspecialchars($pesan_error) ?>
        </div>
    <?php } ?>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Pelanggaran</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Tindakan</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php
        $no = 1;

        while ($row = mysqli_fetch_assoc($hasil)) {
        ?>
            <tr>
                <td><?= $no++ ?></td>

                <td>
                    <?= htmlspecialchars($row['nama_siswa'] ?? '-') ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-') ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['tanggal'] ?? '-') ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['keterangan'] ?? '-') ?>
                </td>

                <td>
                    <form method="POST" action="tindakan.php">
                        <input type="hidden" name="id"
                               value="<?= (int) $row['id'] ?>">

                        <textarea name="tindakan"
                                  required><?= htmlspecialchars($row['tindakan'] ?? '') ?></textarea>
                </td>

                <td>
                        <button type="submit">Simpan</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</body>
</html>