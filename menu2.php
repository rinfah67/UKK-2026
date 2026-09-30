<?php

session_start();

include 'config/koneksi.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Cek hanya admin
if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}


// =========================
// TAMBAH SISWA
// =========================

if (isset($_POST['tambah'])) {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status_aktif = $_POST['status_aktif'];

    $sql = "INSERT INTO t_siswa
            (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
            VALUES
            ('$nis', '$nisn', '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamat', '$status_aktif')";

    if (mysqli_query($koneksi, $sql)) {

        header("Location: kelolasiswa.php");
        exit;

    } else {

        echo "Gagal menambahkan siswa: " . mysqli_error($koneksi);
    }
}


// =========================
// HAPUS SISWA
// =========================

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $sql = "DELETE FROM t_siswa WHERE id='$id'";

    if (mysqli_query($koneksi, $sql)) {

        header("Location: kelolasiswa.php");
        exit;

    } else {

        echo "Gagal menghapus siswa: " . mysqli_error($koneksi);
    }
}


// =========================
// AMBIL DATA SISWA
// =========================

$sql = "SELECT * FROM t_siswa ORDER BY id DESC";

$hasil = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Kelola Siswa</title>

</head>

<body>

<h1>Kelola Siswa</h1>

<a href="dashboard.php">Kembali ke Dashboard</a>

<hr>


<h2>Tambah Siswa</h2>

<form method="POST">

    <table>

        <tr>
            <td>NIS</td>
            <td>:</td>
            <td>
                <input type="text" name="nis">
            </td>
        </tr>

        <tr>
            <td>NISN</td>
            <td>:</td>
            <td>
                <input type="text" name="nisn" required>
            </td>
        </tr>

        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>
                <input type="text" name="nama">
            </td>
        </tr>

        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>

                <select name="jenis_kelamin" required>

                    <option value="">-- Pilih --</option>

                    <option value="L">
                        Laki-laki
                    </option>

                    <option value="P">
                        Perempuan
                    </option>

                </select>

            </td>
        </tr>

        <tr>
            <td>Tanggal Lahir</td>
            <td>:</td>
            <td>
                <input type="date" name="tanggal_lahir" required>
            </td>
        </tr>

        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>
                <textarea name="alamat" required></textarea>
            </td>
        </tr>

        <tr>
            <td>Status Aktif</td>
            <td>:</td>
            <td>

                <select name="status_aktif" required>

                    <option value="1">
                        Aktif
                    </option>

                    <option value="0">
                        Tidak Aktif
                    </option>

                </select>

            </td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td>

                <button type="submit" name="tambah">
                    Tambah Siswa
                </button>

            </td>
        </tr>

    </table>

</form>

<hr>


<h2>Data Siswa</h2>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>

        <th>No</th>
        <th>NIS</th>
        <th>NISN</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>Tanggal Lahir</th>
        <th>Alamat</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Aksi</th>

    </tr>

    <?php

    $no = 1;

    while ($siswa = mysqli_fetch_assoc($hasil)) {

    ?>

    <tr>

        <td>
            <?php echo $no++; ?>
        </td>

        <td>
            <?php echo $siswa['nis']; ?>
        </td>

        <td>
            <?php echo $siswa['nisn']; ?>
        </td>

        <td>
            <?php echo $siswa['nama']; ?>
        </td>

        <td>
            <?php

            if ($siswa['jenis_kelamin'] == 'L') {
                echo "Laki-laki";
            } else {
                echo "Perempuan";
            }

            ?>
        </td>

        <td>
            <?php echo $siswa['tanggal_lahir']; ?>
        </td>

        <td>
            <?php echo $siswa['alamat']; ?>
        </td>

        <td>

            <?php

            if ($siswa['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>
            <?php echo $siswa['created_at']; ?>
        </td>

        <td>

            <a href="kelolasiswa.php?hapus=<?php echo $siswa['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus siswa ini?')">

                Hapus

            </a>

        </td>

    </tr>

    <?php

    }

    ?>

</table>

</body>

</html>