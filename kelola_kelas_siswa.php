<?php

session_start();

include 'config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE
if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

// AMBIL DATA
$sql = "SELECT *
        FROM t_kelas_siswa
        ORDER BY id DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Gagal mengambil data kelas: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Kelas</title>
</head>

<body>

<h1>Kelola Kelas</h1>

<a href="dashboard.php">
    Kembali ke Dashboard
</a>

<br><br>

<a href="tambah_kelas.php">
    Tambah Kelas
</a>

<hr>

<h2>Data Kelas Siswa</h2>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Siswa ID</th>
        <th>Tahun Ajaran ID</th>
        <th>Kelas ID</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Updated At</th>
        <th>Aksi</th>
    </tr>

    <?php

    $no = 1;

    while ($kelas = mysqli_fetch_assoc($hasil)) {

    ?>

    <tr>

        <td>
            <?php echo $no++; ?>
        </td>

        <td>
            <?php echo $kelas['siswa_id']; ?>
        </td>

        <td>
            <?php echo $kelas['tahun_ajaran_id']; ?>
        </td>

        <td>
            <?php echo $kelas['kelas_id']; ?>
        </td>

        <td>
            <?php echo $kelas['tanggal_mulai']; ?>
        </td>

        <td>
            <?php echo $kelas['tanggal_selesai']; ?>
        </td>

        <td>

            <?php

            if ($kelas['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>
            <?php echo $kelas['created_at']; ?>
        </td>

        <td>
            <?php echo $kelas['updated_at']; ?>
        </td>

        <td>

            <a href="edit_kelas.php?id=<?php echo $kelas['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_kelas.php?id=<?php echo $kelas['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus data kelas ini?')"
            >
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