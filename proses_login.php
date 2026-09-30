<?php

session_start();

include 'config/koneksi.php';

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cari user berdasarkan email
    $sql = "SELECT * FROM t_users WHERE email='$email'";
    $hasil = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($hasil) > 0) {

        $user = mysqli_fetch_assoc($hasil);

        // Cek password
        if (password_verify($password, $user['password'])) {

            // Simpan data ke session
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Masuk ke dashboard
            header("Location: dashboard.php");
            exit;

        } else {

            echo "Password salah.";
        }

    } else {

        echo "Email tidak ditemukan.";
    }

} else {

    header("Location: login.php");
    exit;
}

?>