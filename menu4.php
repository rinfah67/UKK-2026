<?php

session_start();

include 'config/koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Cek hanya guru
if ($_SESSION['role'] != 'guru') {
    echo "Akses ditolak!";
    exit;
}


// =========================
// SIMPAN TINDAKAN
// =========================

if (isset($_POST['simpan'])) {

    $id = $_POST['id'];
    $tindakan = $_POST['tindakan'];

    $sql = "UPDATE t_pelanggaran_siswa
            SET tindakan='$tindakan',
                status='selesai'
            WHERE id='$id'";

    if (mysqli_query($koneksi, $sql)) {

        header("Location: tindakan.php");
        exit;

    } else {

        echo "Gagal menyimpan tindakan: "
             . mysqli_error($koneksi);
    }
}


// =========================
// AMBIL DATA PELANGGARAN
// =========================

$sql = "SELECT *
        FROM t_pelanggaran_siswa
        ORDER BY id DESC";

$hasil = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Tindakan Pelanggaran</title>

</head>

<body>

<h1>Tindakan Pelanggaran</h1>

<a href="dashboard.php">Kembali ke Dashboard</a>

<hr>

<h2>Data Pelanggaran Siswa</h2>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>

        <th>No</th>
        <th>Nama Siswa</th>
        <th>Pelanggaran</th>
        <th>Poin</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
        <th>Tindakan</th>
        <th>Status</th>
        <th>Aksi</th>

    </tr>

    <?php

    $no = 1;

    while ($data = mysqli_fetch_assoc($hasil)) {

    ?>

    <tr>

        <td>
            <?php echo $no++; ?>
        </td>

        <td>
            <?php echo $data['nama_siswa']; ?>
        </td>

        <td>
            <?php echo $data['nama_pelanggaran']; ?>
        </td>

        <td>
            <?php echo $data['poin']; ?>
        </td>

        <td>
            <?php echo $data['tanggal']; ?>
        </td>

        <td>
            <?php echo $data['keterangan']; ?>
        </td>

        <td>
            <?php echo $data['tindakan']; ?>
        </td>

        <td>
            <?php echo $data['status']; ?>
        </td>

        <td>

            <form method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $data['id']; ?>"
                >

                <input
                    type="text"
                    name="tindakan"
                    placeholder="Masukkan tindakan"
                    required
                >

                <button
                    type="submit"
                    name="simpan"
                >
                    Simpan
                </button>

            </form>

        </td>

    </tr>

    <?php

    }

    ?>

</table>

</body>

</html>