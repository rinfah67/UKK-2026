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

// AMBIL DATA GURU
$sql = "SELECT
            g.id,
            g.nip,
            g.nama,
            g.email,
            g.status_aktif,
            g.created_at,
            g.updated_at
        FROM t_guru g
        JOIN t_users u
            ON g.user_id = u.id
        WHERE u.role = 'guru'
        ORDER BY g.id DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Gagal mengambil data guru: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Guru</title>
</head>

<body>

<h1>Kelola Guru</h1>

<a href="dashboard.php">
    Kembali ke Dashboard
</a>

<br><br>

<a href="tambah_guru.php">
    Tambah Guru
</a>

<hr>

<h2>Data Guru</h2>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Updated At</th>
        <th>Aksi</th>
    </tr>

    <?php

    $no = 1;

    while ($guru = mysqli_fetch_assoc($hasil)) {

    ?>

    <tr>

        <td>
            <?php echo $no++; ?>
        </td>

        <td>
            <?php echo $guru['nip']; ?>
        </td>

        <td>
            <?php echo $guru['nama']; ?>
        </td>

        <td>
            <?php echo $guru['email']; ?>
        </td>

        <td>

            <?php

            if ($guru['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>
            <?php echo $guru['created_at']; ?>
        </td>

        <td>
            <?php echo $guru['updated_at']; ?>
        </td>

        <td>

            <a href="edit_guru.php?id=<?php echo $guru['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_guru.php?id=<?php echo $guru['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus guru ini?')"
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