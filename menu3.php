<?php

session_start();

include 'config/koneksi.php';


// =========================
// CEK LOGIN
// =========================

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}


// =========================
// CEK ROLE GURU
// =========================

if ($_SESSION['role'] != 'guru') {
    echo "Akses ditolak!";
    exit;
}


// =========================
// AMBIL DATA GURU YANG LOGIN
// =========================

$user_id = $_SESSION['id_user'];

$sql_guru = "SELECT * FROM t_guru
             WHERE user_id='$user_id'
             AND status_aktif=1";

$hasil_guru = mysqli_query($koneksi, $sql_guru);

if (!$hasil_guru) {
    die("Gagal mengambil data guru: " . mysqli_error($koneksi));
}

$guru = mysqli_fetch_assoc($hasil_guru);


// Kalau data guru tidak ditemukan
if (!$guru) {
    die("Data guru untuk akun ini belum tersedia.");
}

$guru_id = $guru['id'];
$nama_guru = $guru['nama'];


// =========================
// SIMPAN PELANGGARAN
// =========================

if (isset($_POST['simpan'])) {

    $siswa_id = $_POST['siswa_id'];
    $pelanggaran_id = $_POST['pelanggaran_id'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $tindakan = $_POST['tindakan'];


    // =========================
    // AMBIL DATA SISWA
    // =========================

    $sql_siswa = "SELECT * FROM t_siswa
                  WHERE id='$siswa_id'
                  AND status_aktif=1";

    $hasil_siswa = mysqli_query($koneksi, $sql_siswa);

    if (!$hasil_siswa) {
        die("Gagal mengambil data siswa: " . mysqli_error($koneksi));
    }

    $siswa = mysqli_fetch_assoc($hasil_siswa);


    if (!$siswa) {
        die("Data siswa tidak ditemukan.");
    }


    // =========================
    // AMBIL DATA PELANGGARAN
    // =========================

    $sql_pelanggaran = "SELECT * FROM t_pelanggaran
                        WHERE id='$pelanggaran_id'
                        AND status_aktif=1";

    $hasil_pelanggaran = mysqli_query($koneksi, $sql_pelanggaran);

    if (!$hasil_pelanggaran) {
        die("Gagal mengambil data pelanggaran: "
            . mysqli_error($koneksi));
    }

    $pelanggaran = mysqli_fetch_assoc($hasil_pelanggaran);


    if (!$pelanggaran) {
        die("Data pelanggaran tidak ditemukan.");
    }


    // =========================
    // AMBIL DATA YANG DIPERLUKAN
    // =========================

    $nama_siswa = $siswa['nama'];

    $nama_pelanggaran = $pelanggaran['nama'];

    $poin = $pelanggaran['poin'];

    $pelanggaran_kategori_id =
        $pelanggaran['pelanggaran_kategori_id'];


    // =========================
    // SIMPAN KE
    // t_pelanggaran_siswa
    // =========================

    $sql = "INSERT INTO t_pelanggaran_siswa
            (
                siswa_id,
                nama_siswa,
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
            VALUES
            (
                '$siswa_id',
                '$nama_siswa',
                '$pelanggaran_id',
                '$nama_pelanggaran',
                '$pelanggaran_kategori_id',
                '$guru_id',
                '$nama_guru',
                '$tanggal',
                '$keterangan',
                '$poin',
                '$tindakan',
                'dicatat'
            )";


    if (mysqli_query($koneksi, $sql)) {

        header("Location: menu3.php?berhasil=1");
        exit;

    } else {

        echo "Gagal mencatat pelanggaran: "
             . mysqli_error($koneksi);

    }
}


// =========================
// PESAN BERHASIL
// =========================

if (isset($_GET['berhasil'])) {

    echo "<p>Pelanggaran berhasil dicatat.</p>";

}


// =========================
// AMBIL DATA SISWA
// =========================

$sql_siswa = "SELECT * FROM t_siswa
              WHERE status_aktif=1
              ORDER BY nama ASC";

$hasil_siswa = mysqli_query($koneksi, $sql_siswa);

if (!$hasil_siswa) {
    die("Gagal mengambil data siswa: "
        . mysqli_error($koneksi));
}


// =========================
// AMBIL DATA PELANGGARAN
// =========================

$sql_pelanggaran = "SELECT * FROM t_pelanggaran
                    WHERE status_aktif=1
                    ORDER BY nama ASC";

$hasil_pelanggaran = mysqli_query(
    $koneksi,
    $sql_pelanggaran
);

if (!$hasil_pelanggaran) {
    die("Gagal mengambil data pelanggaran: "
        . mysqli_error($koneksi));
}


// =========================
// AMBIL CATATAN PELANGGARAN
// =========================

$sql_data = "SELECT *
             FROM t_pelanggaran_siswa
             ORDER BY id DESC";

$hasil_data = mysqli_query($koneksi, $sql_data);

if (!$hasil_data) {
    die("Gagal mengambil catatan pelanggaran: "
        . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Catat Pelanggaran</title>

</head>

<body>


<h1>Catat Pelanggaran Siswa</h1>


<p>
    Guru:
    <b><?php echo $nama_guru; ?></b>
</p>


<a href="dashboard.php">
    Kembali ke Dashboard
</a>


<hr>


<h2>Form Catat Pelanggaran</h2>


<form method="POST">


    <table>


        <!-- SISWA -->

        <tr>

            <td>Siswa</td>

            <td>:</td>

            <td>

                <select name="siswa_id" required>

                    <option value="">
                        -- Pilih Siswa --
                    </option>


                    <?php

                    while ($siswa = mysqli_fetch_assoc($hasil_siswa)) {

                    ?>

                        <option value="<?php echo $siswa['id']; ?>">

                            <?php

                            echo $siswa['nis']
                                 . " - "
                                 . $siswa['nama'];

                            ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

            </td>

        </tr>


        <!-- PELANGGARAN -->

        <tr>

            <td>Pelanggaran</td>

            <td>:</td>

            <td>

                <select name="pelanggaran_id" required>

                    <option value="">
                        -- Pilih Pelanggaran --
                    </option>


                    <?php

                    while (
                        $pelanggaran =
                        mysqli_fetch_assoc($hasil_pelanggaran)
                    ) {

                    ?>

                        <option
                            value="<?php echo $pelanggaran['id']; ?>"
                        >

                            <?php

                            echo $pelanggaran['nama']
                                 . " - "
                                 . $pelanggaran['poin']
                                 . " poin";

                            ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

            </td>

        </tr>


        <!-- TANGGAL -->

        <tr>

            <td>Tanggal</td>

            <td>:</td>

            <td>

                <input
                    type="date"
                    name="tanggal"
                    value="<?php echo date('Y-m-d'); ?>"
                    required
                >

            </td>

        </tr>


        <!-- KETERANGAN -->

        <tr>

            <td>Keterangan</td>

            <td>:</td>

            <td>

                <textarea
                    name="keterangan"
                    rows="4"
                    cols="30"
                ></textarea>

            </td>

        </tr>


        <!-- TINDAKAN -->

        <tr>

            <td>Tindakan</td>

            <td>:</td>

            <td>

                <textarea
                    name="tindakan"
                    rows="4"
                    cols="30"
                ></textarea>

            </td>

        </tr>


        <!-- TOMBOL -->

        <tr>

            <td></td>

            <td></td>

            <td>

                <button
                    type="submit"
                    name="simpan"
                >
                    Simpan Pelanggaran
                </button>

            </td>

        </tr>


    </table>


</form>


<hr>


<h2>Catatan Pelanggaran Terbaru</h2>


<table
    border="1"
    cellpadding="8"
    cellspacing="0"
>


    <tr>

        <th>No</th>

        <th>Nama Siswa</th>

        <th>Pelanggaran</th>

        <th>Poin</th>

        <th>Tanggal</th>

        <th>Guru</th>

        <th>Keterangan</th>

        <th>Tindakan</th>

        <th>Status</th>

    </tr>


    <?php

    $no = 1;

    while ($data = mysqli_fetch_assoc($hasil_data)) {

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
                <?php echo $data['nama_guru']; ?>
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


        </tr>

    <?php

    }

    ?>


</table>


</body>

</html>