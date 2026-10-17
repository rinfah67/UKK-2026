
<?php
session_start();

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Si Pelang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    background: #111827;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.login-panel {
    width: 100%;
    max-width: 765px;
    min-height: 545px;
    background: #2563eb;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 30px 20px 40px;
}

.logo {
    width: 150px;
    height: 150px;
    object-fit: contain;
    margin-bottom: 7px;
}

.login-card {
    width: 100%;
    max-width: 357px;
    background: #f8fafc;
    border-radius: 11px;
    padding: 7px 35px 22px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
}

.form-label {
    font-size: 25px;
    color: #1e293b;
    margin-bottom: 1px;
}

.form-control {
    height: 63px;
    border: 1px solid transparent;
    border-radius: 14px;
    background: #e2e8f0;
    color: #0f172a;
    font-size: 23px;
    padding: 10px 18px;
    margin-bottom: 1px;
}

.form-control:focus {
    background: #ffffff;
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.btn-login {
    display: block;
    width: 216px;
    max-width: 100%;
    height: 63px;
    margin: 34px auto 0;
    border: none;
    border-radius: 13px;
    background: #1d4ed8;
    color: #ffffff;
    font-size: 25px;
    transition: background 0.2s ease;
}

.btn-login:hover {
    background: #1e40af;
    color: #ffffff;
}

.alert {
    font-size: 14px;
    margin-top: 12px;
}

@media (max-width: 480px) {
    .login-panel {
        min-height: auto;
        padding: 25px 15px 35px;
    }

    .logo {
        width: 125px;
        height: 125px;
    }

    .login-card {
        padding: 12px 22px 22px;
    }

    .form-label {
        font-size: 21px;
    }

    .form-control {
        height: 55px;
        font-size: 19px;
    }

    .btn-login {
        height: 55px;
        font-size: 22px;
    }
}

    </style>
</head>

<body>

    <div class="login-panel">

        <!-- Logo dari folder img -->
        <img src="img/halo.png" alt="Logo Si Pelang" class="logo">

        <!-- Form login -->
        <div class="login-card">

            <form action="proses_login.php" method="POST">

                <div>
                    <label for="email" class="form-label">Username</label>
                    <input
                        type="text"
                        name="email"
                        id="email"
                        class="form-control"
                        placeholder="Masukkan username atau email"
                        autocomplete="username"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger mb-0">
                        Login gagal. Periksa username dan password.
                    </div>
                <?php endif; ?>

                <button type="submit" name="login" class="btn-login">
                    MASUK
                </button>

            </form>

        </div>

    </div>

</body>
</html>
