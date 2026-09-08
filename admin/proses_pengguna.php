<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['nama'])) {
    header("Location: ../auth/login.php");
    exit();
}

$aksi = $_GET['aksi'] ?? '';


// insert

if ($aksi === 'tambah') {

    $nomor_induk = trim($_POST['nomor_induk'] ?? '');
    $nama        = trim($_POST['nama'] ?? '');
    $username    = trim($_POST['username'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $no_telp     = trim($_POST['no_telp'] ?? '');
    $role        = trim($_POST['role'] ?? 'user');

    $query = "INSERT INTO users (nomor_induk, nama, username, password, no_telp, role) VALUES('$nomor_induk', '$nama', '$username', '$password', '$no_telp', '$role')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: data_pengguna.php?status=add_success");
    } else {
        header("Location: data_pengguna.php?status=failed");
    }

    exit();
}


// edit
if ($aksi === 'edit') {

    $nomor_induk = trim($_POST['nomor_induk'] ?? '');
    $nama        = trim($_POST['nama'] ?? '');
    $username    = trim($_POST['username'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $no_telp     = trim($_POST['no_telp'] ?? '');
    $role        = trim($_POST['role'] ?? 'user');

    // Jika password diisi, password diubah
    if ($password !== '') {

        $query = "UPDATE users SET nama = '$nama', username = '$username', password = '$password',no_telp = '$no_telp', role = '$role' WHERE nomor_induk = '$nomor_induk'";

    } else {

        // Jika password kosong, password lama tetap
        $query = "UPDATE users SET nama = '$nama', username = '$username', no_telp = '$no_telp',role = '$role' WHERE nomor_induk = '$nomor_induk'";
    }

    if (mysqli_query($koneksi, $query)) {
        header("Location: data_pengguna.php?status=update_success");
    } else {
        header("Location: data_pengguna.php?status=failed");
    }

    exit();
}

// delete
if ($aksi === 'delete') {

    $nomor_induk = $_GET['nomor_induk'] ?? '';
    $nomor_induk = mysqli_real_escape_string($koneksi, $nomor_induk);

    $query = "DELETE FROM users WHERE nomor_induk = '$nomor_induk'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: data_pengguna.php?status=delete_success");
    } else {
        header("Location: data_pengguna.php?status=failed");
    }

    exit();
}
?>