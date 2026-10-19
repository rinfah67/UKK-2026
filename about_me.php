
<?php
// Memulai session pengguna
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Mengambil role pengguna
$role = $_SESSION['role'] ?? '';

// Menentukan dashboard berdasarkan role
$dashboard = "dashboard.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Mengatur tampilan dasar halaman */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #212529;
        }

        /* Sidebar di sebelah kiri */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 16.666667%;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        /* Konten utama */
        main {
            margin-left: 16.666667%;
            width: 83.3333%;
            min-height: 90vh;
        }

        /* Kotak profil di tengah halaman */
        .profil {
            max-width: 500px;
            margin: 50px auto;
            padding: 35px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Lingkaran inisial profil */
        .foto {
            width: 85px;
            height: 85px;
            margin: 0 auto 20px;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 35px;
            font-weight: bold;
        }

        /* Judul halaman */
        h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #0d6efd;
        }

        /* Teks perkenalan */
        .deskripsi {
            text-align: center;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        /* Informasi profil */
        .info p {
            padding: 12px 0;
            margin: 0;
            border-bottom: 1px solid #eeeeee;
            line-height: 1.6;
        }

        .info strong {
            display: inline-block;
            width: 100px;
        }

        /* Tampilan untuk layar kecil */
        @media (max-width: 767.98px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            main {
                margin-left: 0;
                width: 100%;
            }

            .profil {
                margin: 25px 10px;
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<div class="container-fluid">
    <?php include "layout/sidebar.php"; ?>

    <main class="row">
        <div class="col-9 offset-3 col-lg-10 offset-lg-2 p-4">
        <div class="profile-card">

            <div class="profil">

                <!-- Inisial profil -->
                <div class="foto">F</div>

                <!-- Judul halaman -->
                <h1>About Me</h1>

                <!-- Perkenalan diri -->
                <p class="deskripsi">
                    Halo! Saya seorang pelajar yang sedang belajar
                    dan mengembangkan kemampuan di bidang teknologi.
                </p>

                <!-- Informasi pribadi -->
                <div class="info">

                    <p>
                        <strong>Status</strong> : Pelajar SMK
                    </p>

                    <p>
                        <strong>Sekolah</strong> : SMK Muhammadiyah Kota Tasikmalaya
                    </p>

                    <p>
                        <strong>Umur</strong> : 17 tahun
                    </p>

                    <p>
                        <strong>Hobi</strong> : Main game
                    </p>

                </div>

            </div>

        </main>

    </div>
</div>

</body>
</html>
