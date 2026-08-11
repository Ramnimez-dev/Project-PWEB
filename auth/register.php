<?php
session_start();
require '../config/koneksi.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['nama'])) {
        $nama = $_POST['nama'];
        $nomor_induk = $_POST['nomor_induk'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $no_telp = $_POST['no_telp'];
        $role = $_POST['role'];


        $cek = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM users WHERE nama='$nama' OR username='$username'"));
        if($cek > 0) {
            echo 
            "<script>
            alert('Nama sudah terdaftar!');
            window.location.href = 'register.php';
            </script>";
        } else {
            $query = mysqli_query($koneksi, "INSERT INTO users(nomor_induk, nama, username, password, no_telp, role, created_at) VALUES('$nomor_induk', '$nama', '$username', '$password', '$no_telp','$role', NOW())");
            echo "<script>
            alert('Registrasi Berhasil! Silahkan Login');
            window.location.href = 'login.php';
            </script>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="register.css">
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
        </div>

        <div class="content">
            <div class="register-wrapper">
                <div class="header-register">
                    <h1>Registrasi</h1>
                    <p>Silahkan isi dengan benar</p>
                </div>
                <div class="card-register">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="nomor_induk">Nomor Induk</label>
                            <input type="text" name="nomor_induk" id="nomor_induk" placeholder="Masukkan Nomor Induk">
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" placeholder="Masukkan Nama Lengkap">
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" placeholder="Masukkan Username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Masukkan Password">
                        </div>
                            <div class="form-group">
                            <label for="no_telp">Nomor Telepon</label>
                            <input type="tel" name="no_telp" id="no_telp" placeholder="Masukkan Nomor Telepon">
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role">
                                <option value="">--Pilih Role--</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit">Login</button>
                    </form>
                </div>
                <p>Sudah Punya Akun? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>