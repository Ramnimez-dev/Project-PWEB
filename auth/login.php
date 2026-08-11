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
        window.location.href = '../user/index.php';
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
</head>
<body>
    <h1>Login</h1>
    <p>Silahkan Isi dengan Benar</p>

    <form action="" method="post">
        <label for="nomor_induk">Nomor Induk</label>
        <input type="text" name="nomor_induk" id="nomor_induk" placeholder="Masukkan Nomor Induk">

        <label for="username">Username</label>
        <input type="text" name="username" id="username" placeholder="Masukkan Username">

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Masukkan Password">

        <button type="submit">Login</button>
    </form>

    <p>Belum Punya Akun? <a href="register.php">Register</a></p>
    </form>
</body>
</html>
