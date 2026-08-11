<?php
session_start();
require '../config/koneksi.php'; 

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_induk = $_POST['nomor_induk'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE nomor_induk='$nomor_induk'");
    $data = mysqli_fetch_assoc($query);

    if(!$data) {
        echo "<script>
        alert('Nomor Induk Salah!');
        window.location.href = 'login.php';
        </script>";
        exit;
    } if($username !== $data['username']) {
        echo "<script>
        alert('Username Salah!');
        window.location.href = 'login.php';
        </script>";
        exit;
    } if($password !== $data['password']) {
        echo "<script>
        alert('Password Salah!');
        window.location.href = 'login.php';
        </script>";
        exit;
    }

    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['nomor_induk'] = $data['nomor_induk'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['password'] = $data['password'];
    $_SESSION['role'] = $data['role'];

    if($data['role'] == 'admin') {
        echo "<script>
        alert('Login Berhasil! Selamat Datang {$data['nama']}');
        window.location.href = '../admin/dashboard.php';
        </script>";
        exit;
    } elseif($data['role'] == 'user') {
        echo "<script>
        alert('Login Berhasil! Selamat Datang {$data['nama']}');
        window.location.href = '../user/dashboard.php';
        </script>";
        exit;
    } else {
        echo "<script>
        alert('Role Akun tidak Valid');
        window.location.href = 'login.php';
        </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">

        <div class="sidebar">
            <div class="logo">
                <img src="../img/logo sapras.png" alt="Logo Sarpras">
            </div>
            <div class="brand-text">
                <h1>
                    Halo<br>
                    <span>SarPras!</span>
                </h1>
            </div>
            <div class="line"><hr></div>
            <p class="description">
                Sistem informasi sarana
                dan prasarana sekolah
                yang terintegrasi
                dan mudah
                digunakan.
            </p>
            <a href="../index.php" class="btn-kembali">Kembali</a>
        </div>

        <div class="content">
            <div class="login-wrapper">
                <div class="header-login">
                    <h1>Login</h1>
                    <p>Silahkan Isi dengan Benar</p>
                </div>
                <div class="card-login">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="nomor_induk">Nomor Induk</label>
                            <input type="text" name="nomor_induk" id="nomor_induk" placeholder="Masukkan Nomor Induk">
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" placeholder="Masukkan Username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Masukkan Password">
                        </div>
                        <button type="submit">Login</button>
                    </form>
                </div>
                <p>Belum Punya Akun? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</body>
</html>
