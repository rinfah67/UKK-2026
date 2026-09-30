<?php

session_start();

include 'config/koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin yang boleh masuk
if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

// Tambah guru
if (isset($_POST['tambah'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO t_users (name, email, password, role)
            VALUES ('$name', '$email', '$password', 'guru')";

    if (mysqli_query($koneksi, $sql)) {
        echo "Guru berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan guru: " . mysqli_error($koneksi);
    }
}

// Hapus guru
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $sql = "DELETE FROM t_users 
            WHERE id='$id' AND role='guru'";

    if (mysqli_query($koneksi, $sql)) {
        header("Location: kelolaguru.php");
        exit;
    } else {
        echo "Gagal menghapus guru: " . mysqli_error($koneksi);
    }
}

// Ambil data guru
$sql = "SELECT * FROM t_users 
        WHERE role='guru'
        ORDER BY id DESC";

$hasil = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Guru</title>
</head>

<body>

    <h1>Kelola Guru</h1>

    <a href="dashboard.php">Kembali ke Dashboard</a>

    <hr>

    <h2>Tambah Guru</h2>

    <form method="POST">

        <table>

            <tr>
                <td>Nama Guru</td>
                <td>:</td>
                <td>
                    <input type="text" name="name" required>
                </td>
            </tr>

            <tr>
                <td>Email</td>
                <td>:</td>
                <td>
                    <input type="email" name="email" required>
                </td>
            </tr>

            <tr>
                <td>Password</td>
                <td>:</td>
                <td>
                    <input type="password" name="password" required>
                </td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td>
                    <button type="submit" name="tambah">
                        Tambah Guru
                    </button>
                </td>
            </tr>

        </table>

    </form>

    <hr>

    <h2>Data Guru</h2>

    <table border="1" cellpadding="8" cellspacing="0">

        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
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
                <?php echo $guru['name']; ?>
            </td>

            <td>
                <?php echo $guru['email']; ?>
            </td>

            <td>
                <?php echo $guru['role']; ?>
            </td>

            <td>
                <?php echo $guru['created_at']; ?>
            </td>

            <td>
                <a href="kelolaguru.php?hapus=<?php echo $guru['id']; ?>"
                   onclick="return confirm('Yakin ingin menghapus guru ini?')">
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