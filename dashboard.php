<?php 
include 'includes/cek_session.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Pelanggaran Siswa</title>

</head>
<body>
    <h1>Selamat datang, <?php echo $_SESSION['name']; ?></h1>
    <p>Anda login sebagai: <?php echo $_SESSION['role']; ?></p>

    <ul>
        <?php if ($_SESSION['role'] == 'admin') { ?>
        <li><a href="kelola_guru.php">Kelola Guru</a></li>
        <li><a href="kelola_siswa.php">Kelola Siswa</a></li>
        <li><a href="kelola_kelas_siswa.php">Kelola Kelas Siswa</a></li>
     <?php } ?>

        <?php if ($_SESSION['role'] == 'guru' ) { ?> 
         <li><a href="menu3.php">menu 3</a></li>
        <li><a href="menu5.php">menu 4</a></li>
        <?php } ?>
    </ul>
    
    <a href="logout.php">Logout</a>
</body>
</html>